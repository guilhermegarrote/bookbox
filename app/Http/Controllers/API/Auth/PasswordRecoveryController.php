<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\RecoverySessionKeys;
use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendCodeRequest;
use App\Http\Requests\Auth\ValidateCodeRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\PasswordResetCode;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use RuntimeException;

class PasswordRecoveryController extends Controller
{
    protected EmailService $emailService;

    private const EXPIRATION_MINUTES = 15;
    private const MAX_ATTEMPTS = 5;
    private const ATTEMPT_TTL_SECONDS = 900;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Sends a recovery code to the provided email address.
     * Stores session data to manage the recovery flow.
     *
     * @param SendCodeRequest $request Validated request containing the user's email.
     * @return JsonResponse Redirects to the code input form or returns an error response.
     */
    public function sendCode(SendCodeRequest $request): JsonResponse
    {
        $email = $request->validated()['email'];

        try {
            $this->dispatchRecoveryCode($email);

            $request->session()->put(RecoverySessionKeys::CODE_SENT, true);
            $request->session()->put(RecoverySessionKeys::EMAIL, $email);

            return response()->json([
                'redirect' => route('recovery.code.form'),
            ], 201);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (RuntimeException $e) {
            return $this->tooManyRequestsResponse($e->getMessage());
        } catch (Exception $e) {
            $this->logError("Erro ao enviar e-mail para {$email}", $e);
            return $this->internalErrorResponse($e, 'Erro interno ao enviar código.');
        }
    }

    /**
     * Resends the recovery code using the email stored in the session.
     *
     * @param Request $request HTTP request containing the session.
     * @return JsonResponse Success or error response depending on the flow.
     */
    public function resendCode(Request $request): JsonResponse
    {
        $email = $request->session()->get(RecoverySessionKeys::EMAIL);

        if (empty($email)) {
            return $this->badRequestResponse([
                'session' => ['Sessão expirada ou e-mail não informado.'],
            ]);
        }

        try {
            $this->dispatchRecoveryCode($email);

            $request->session()->put(RecoverySessionKeys::CODE_SENT, true);

            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (RuntimeException $e) {
            return $this->tooManyRequestsResponse($e->getMessage());
        } catch (Exception $e) {
            $this->logError("Erro ao reenviar e-mail para {$email}", $e);
            return $this->internalErrorResponse($e, 'Erro interno ao reenviar código.');
        }
    }

    /**
     * Validates the code provided by the user and checks the number of attempts.
     * If valid, stores data in the session to allow password reset.
     *
     * @param ValidateCodeRequest $request Validated request containing the code.
     * @return JsonResponse Redirects to the new password form or returns an error response.
     */
    public function validateCode(ValidateCodeRequest $request): JsonResponse
    {
        $email = $request->session()->get(RecoverySessionKeys::EMAIL);

        if (empty($email)) {
            return $this->validationErrorResponse([
                'email' => ['Email da sessão é obrigatório.'],
            ]);
        }

        $inputCode = $request->validated()['code'];

        try {
            $emailHash = hash('sha256', strtolower($email), true);
            $user = User::where('email_hash', $emailHash)->firstOrFail();
            $userId = Utils::convertUuidToBinary($user->id);

            $attemptKey = "code_attempts:{$user->id}";
            $attempts = Cache::get($attemptKey, 0);
            $attempts++;
            Cache::put($attemptKey, $attempts, self::ATTEMPT_TTL_SECONDS);

            if ($attempts > self::MAX_ATTEMPTS) {
                return $this->tooManyRequestsResponse('Muitas tentativas inválidas. Tente mais tarde.');
            }

            $validCodes = PasswordResetCode::where('user_id', $userId)
                ->where('expiration', '>', now())
                ->get();

            $codeValid = $validCodes->contains(fn($record) => Hash::check($inputCode, $record->value));

            if (!$codeValid) {
                Log::warning("Código inválido para usuário {$user->id}");
                return $this->unauthorizedResponse('Código incorreto ou expirado.');
            }

            PasswordResetCode::where('user_id', $userId)->delete();
            Cache::forget($attemptKey);

            $request->session()->put(RecoverySessionKeys::CODE_VALIDATED, true);
            $request->session()->put(RecoverySessionKeys::EXPIRATION, now()->addMinutes(self::EXPIRATION_MINUTES));

            return response()->json([
                'redirect' => route('recovery.new-password.form'),
            ], 201);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuário não encontrado.');
        } catch (Exception $e) {
            $this->logError('Erro ao validar código.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao validar código.');
        }
    }

    /**
     * Resets the user's password using the validated recovery code.
     * Verifies session and expiration time before applying the new password.
     *
     * @param ResetPasswordRequest $request Validated request containing the new password.
     * @return JsonResponse Redirects to login or returns an error response.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        if (!$request->session()->get(RecoverySessionKeys::CODE_VALIDATED)) {
            return $this->unauthorizedResponse('Sessão inválida ou expirada.');
        }

        if (now()->greaterThan($request->session()->get(RecoverySessionKeys::EXPIRATION))) {
            return $this->forbiddenResponse('Tempo para redefinição expirado.');
        }

        $email = $request->session()->get(RecoverySessionKeys::EMAIL);
        $emailHash = hash('sha256', strtolower($email), true);

        $user = User::where('email_hash', $emailHash)->first();

        if (!$user) {
            return $this->notFoundResponse('Usuário não encontrado.');
        }

        $newPassword = $request->validated()['password'];

        if (Hash::check($newPassword, $user->password)) {
            return $this->validationErrorResponse(['message' => 'Essa senha já está sendo utilizada.']);
        }

        $user->password = $newPassword;
        $user->save();

        $request->session()->forget([
            RecoverySessionKeys::CODE_SENT,
            RecoverySessionKeys::EMAIL,
            RecoverySessionKeys::CODE_VALIDATED,
            RecoverySessionKeys::EXPIRATION,
        ]);

        return response()->json([
            'redirect' => route('login'),
        ]);
    }

    /**
     * Sends the recovery code by email and saves it in the database with a hash and expiration.
     * Limits the number of sending attempts to prevent abuse.
     *
     * @param string $email The email address to send the code to.
     * @return void
     *
     * @throws ModelNotFoundException If no user is found with the given email.
     * @throws RuntimeException If the user has exceeded the maximum number of attempts.
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
            throw new RuntimeException('Número máximo de envios atingido. Tente novamente mais tarde.');
        }

        Cache::put($attemptKey, $attempts + 1, self::ATTEMPT_TTL_SECONDS);

        $code = $this->emailService->sendRecoveyCode($email, $user->name);

        PasswordResetCode::create([
            'user_id' => $userId,
            'value' => $code,
            'expiration' => now()->addMinutes(self::EXPIRATION_MINUTES),
        ]);
    }
}
