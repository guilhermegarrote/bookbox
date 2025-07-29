<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

class AuthController extends Controller
{
    use JsonResponseTrait;

    /**
     * Registers the first user in the system.
     * Registration is only allowed if no users currently exist.
     *
     * @param UserStoreRequest $request Validated request containing the user's name, email, and password.
     * @return JsonResponse HTTP response with redirection or error message.
     */
    public function register(UserStoreRequest $request): JsonResponse
    {
        if (User::limit(1)->exists()) {
            return $this->forbiddenResponse('Cadastro desabilitado. Já existe um usuário no sistema.');
        }

        $validatedData = $request->validated();

        try {
            $userData = array_filter(
                array_intersect_key($validatedData, array_flip(['name', 'email', 'password'])),
                fn($value) => !is_null($value) && $value !== ''
            );

            User::create($userData);

            return $this->createdResponse(['redirect' => route('login')]);
        } catch (Throwable $e) {
            $this->logError('Erro ao cadastrar o primeiro usuário.', $e, ['dados' => $validatedData]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar o primeiro usuário.');
        }
    }

    /**
     * Authenticates the user using email and password.
     * If successful, returns a JWT token stored in a secure HTTP-only cookie.
     *
     * @param LoginRequest $request Validated request containing user credentials.
     * @return JsonResponse HTTP response with token, user data, and redirect URL.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $email = strtolower(trim($validatedData['email']));
        $password = trim($validatedData['password']);

        $emailHash = hash('sha256', $email, true);

        $user = User::where('email_hash', $emailHash)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return $this->unauthorizedResponse('Credenciais inválidas.');
        }

        $token = JWTAuth::fromUser($user);

        $cookie = cookie(
            env('JWT_COOKIE_NAME', 'jwt_token'),
            $token,
            env('JWT_COOKIE_TTL', JWTAuth::factory()->getTTL()),
            env('JWT_COOKIE_PATH', '/'),
            env('JWT_COOKIE_DOMAIN', null),
            config('app.env') !== 'local',
            env('JWT_COOKIE_HTTPONLY', true),
            env('JWT_COOKIE_RAW', false),
            env('JWT_COOKIE_SAMESITE', 'Strict')
        );

        return $this->successResponse([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'redirect' => route('loans.view'),
        ])->cookie($cookie);
    }

    /**
     * Returns the currently authenticated user's basic data.
     *
     * @return JsonResponse HTTP response with user ID, name, and email, or an unauthorized message.
     */
    public function me(): JsonResponse
    {
        $user = JWTAuth::user();

        if (!$user) {
            return $this->unauthorizedResponse('Usuário não autenticado.');
        }

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * Logs out the authenticated user by invalidating the JWT token
     * and removing the token cookie from the client.
     *
     * @return JsonResponse HTTP success or error response with cookie cleared.
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            $cookie = cookie()->forget(env('JWT_COOKIE_NAME', 'jwt_token'));

            return $this->successResponse()->cookie($cookie);
        } catch (Throwable $e) {
            return $this->internalErrorResponse($e, 'Erro ao realizar logout.');
        }
    }

    /**
     * Refreshes the user's JWT token and returns a new one in a secure cookie.
     * Used to extend the session without requiring login.
     *
     * @return JsonResponse HTTP response with the new token in a cookie, or an error message.
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());

            $cookie = cookie(
                env('JWT_COOKIE_NAME', 'jwt_token'),
                $token,
                env('JWT_COOKIE_TTL', JWTAuth::factory()->getTTL()),
                env('JWT_COOKIE_PATH', '/'),
                env('JWT_COOKIE_DOMAIN', null),
                config('app.env') !== 'local',
                env('JWT_COOKIE_HTTPONLY', true),
                env('JWT_COOKIE_RAW', false),
                env('JWT_COOKIE_SAMESITE', 'Strict')
            );

            return $this->successResponse(['token' => $token])->cookie($cookie);
        } catch (Throwable $e) {
            $this->logError('Erro ao atualizar token.', $e, ['token' => $token]);
            return $this->unauthorizedResponse('Não foi possível atualizar o token.');
        }
    }
}
