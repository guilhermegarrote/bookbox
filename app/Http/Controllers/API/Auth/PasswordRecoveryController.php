<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Auth;

use App\Enums\RecoverySessionKey;
use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendCodeRequest;
use App\Http\Requests\Auth\ValidateCodeRequest;
use App\Models\PasswordResetCode;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class PasswordRecoveryController.
 *
 * Handles secure password recovery via email-based code validation.
 * Implements rate limiting, expiration control, and session verification
 * to ensure safe password reset flows.
 *
 * @see \App\Services\EmailService::sendRecoveryCode()
 * @see PasswordResetCode
 * @see RecoverySessionKeys
 */
class PasswordRecoveryController extends Controller
{
    private const EXPIRATION_MINUTES = 15;
    private const MAX_ATTEMPTS = 5;
    private const ATTEMPT_TTL_SECONDS = 900;

    protected EmailService $emailService;

    /**
     * Initializes the controller with the email service dependency.
     *
     * @param EmailService $emailService service responsible for sending recovery emails
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Sends a password recovery code to the user's email.
     * Starts a new recovery session.
     *
     * @param SendCodeRequest $request validated request containing the user's email
     *
     * @throws ModelNotFoundException if no user exists with the given email
     * @throws \RuntimeException when too many attempts are detected
     *
     * @return JsonResponse redirects to the code input form or returns an error
     */
    public function sendCode(SendCodeRequest $request): JsonResponse
    {
        $email = $request->validated()['email'];

        try {
            $this->dispatchRecoveryCode($email);

            $request->session()->put(RecoverySessionKey::CODE_SENT->value, true);
            $request->session()->put(RecoverySessionKey::EMAIL_VERIFIED->value, $email);

            return response()->json(['redirect' => route('recovery.code.form')], 201);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (\RuntimeException $e) {
            return $this->tooManyRequestsResponse($e->getMessage());
        } catch (\Throwable $e) {
            $this->logError("Failed to send recovery email to {$email}", $e);

            return $this->internalErrorResponse($e, 'Erro interno ao enviar código.');
        }
    }

    /**
     * Resends a new recovery code using the stored session email.
     *
     * @param Request $request HTTP request containing the session data
     *
     * @return JsonResponse success response or session error
     */
    public function resendCode(Request $request): JsonResponse
    {
        $email = $request->session()->get(RecoverySessionKey::EMAIL_VERIFIED);

        if (empty($email)) {
            return $this->badRequestResponse(['session' => ['Sessão expirada ou e-mail não informado.']]);
        }

        try {
            $emailHash = hash('sha256', strtolower($email), true);
            $user = User::where('email_hash', $emailHash)->firstOrFail();

            PasswordResetCode::where('user_id', Utils::convertUuidToBinary($user->id))->delete();

            $this->dispatchRecoveryCode($email);

            $request->session()->put(RecoverySessionKey::CODE_SENT, true);

            return $this->successResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (\RuntimeException $e) {
            return $this->tooManyRequestsResponse($e->getMessage());
        } catch (\Throwable $e) {
            $this->logError("Failed to resend recovery email to {$email}", $e);

            return $this->internalErrorResponse($e, 'Erro interno ao reenviar código.');
        }
    }

    /**
     * Validates the recovery code and allows password reset if successful.
     *
     * @param ValidateCodeRequest $request validated request containing the recovery code
     *
     * @return JsonResponse redirects to the password reset form or returns an error
     */
    public function validateCode(ValidateCodeRequest $request): JsonResponse
    {
        $email = $request->session()->get(RecoverySessionKey::EMAIL_VERIFIED);

        if (empty($email)) {
            return $this->validationErrorResponse(['email' => ['Email da sessão é obrigatório.']]);
        }

        $inputCode = $request->validated()['code'];

        try {
            $emailHash = hash('sha256', strtolower($email), true);
            $user = User::where('email_hash', $emailHash)->firstOrFail();
            $userId = Utils::convertUuidToBinary($user->id);

            $attemptKey = "code_attempts:{$user->id}";
            $attempts = Cache::increment($attemptKey, 1, self::ATTEMPT_TTL_SECONDS);

            if ($attempts > self::MAX_ATTEMPTS) {
                return $this->tooManyRequestsResponse('Muitas tentativas inválidas. Tente mais tarde.');
            }

            $validCodes = PasswordResetCode::where('user_id', $userId)
                ->where('expiration', '>', now())
                ->get()
            ;

            $codeValid = $validCodes->contains(fn ($record) => Hash::check($inputCode, $record->value));

            if (!$codeValid) {
                Log::warning('Invalid recovery code attempt', ['user_id' => $user->id]);

                return $this->unauthorizedResponse('Código incorreto ou expirado.');
            }

            PasswordResetCode::where('user_id', $userId)->delete();
            Cache::forget($attemptKey);

            $request->session()->put(RecoverySessionKey::CODE_VALIDATED, true);
            $request->session()->put(RecoverySessionKey::PASSWORD_RESET_EXPIRATION, now()->addMinutes(self::EXPIRATION_MINUTES));

            return response()->json(['redirect' => route('recovery.new-password.form')], 201);
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao validar código.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao validar código.');
        }
    }

    /**
     * Resets the user's password after successful code validation.
     *
     * @param ResetPasswordRequest $request validated request containing the new password
     *
     * @return JsonResponse redirects to the login route or returns an error
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        if (!$request->session()->get(RecoverySessionKey::CODE_VALIDATED)) {
            return $this->unauthorizedResponse('Sessão inválida ou expirada.');
        }

        if (now()->greaterThan($request->session()->get(RecoverySessionKey::PASSWORD_RESET_EXPIRATION))) {
            return $this->forbiddenResponse('Tempo para redefinição expirado.');
        }

        $email = $request->session()->get(RecoverySessionKey::EMAIL_VERIFIED);
        $user = User::where('email_hash', hash('sha256', strtolower($email), true))->first();

        if (!$user) {
            return $this->notFoundResponse('Usuário não encontrado.');
        }

        $newPassword = $request->validated()['password'];

        if (Hash::check($newPassword, $user->password)) {
            return $this->validationErrorResponse(['message' => 'Essa senha já está sendo utilizada.']);
        }

        $user->update(['password' => $newPassword]);

        $request->session()->forget([
            RecoverySessionKey::CODE_SENT,
            RecoverySessionKey::EMAIL_VERIFIED,
            RecoverySessionKey::CODE_VALIDATED,
            RecoverySessionKey::PASSWORD_RESET_EXPIRATION,
        ]);

        return response()->json(['redirect' => route('login')]);
    }

    /**
     * Generates and emails a new recovery code to the user.
     * Limits the number of send attempts to prevent abuse.
     *
     * @param string $email target email address
     *
     * @throws ModelNotFoundException if no user matches the email
     * @throws \RuntimeException if the user exceeded the send limit
     */
    private function dispatchRecoveryCode(string $email): void
    {
        $emailHash = hash('sha256', strtolower($email), true);
        $user = User::where('email_hash', $emailHash)->firstOrFail();
        $userId = Utils::convertUuidToBinary($user->id);

        PasswordResetCode::where('user_id', $userId)->delete();

        $attemptKey = "recovery_code_send_attempts:{$user->id}";
        $attempts = Cache::get($attemptKey, 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            throw new \RuntimeException('Número máximo de envios atingido. Tente novamente mais tarde.');
        }

        Cache::put($attemptKey, $attempts + 1, self::ATTEMPT_TTL_SECONDS);

        $code = $this->emailService->sendRecoveryCode($email, $user->name);

        PasswordResetCode::create([
            'user_id' => $userId,
            'value' => $code,
            'expiration' => now()->addMinutes(self::EXPIRATION_MINUTES),
        ]);
    }
}
