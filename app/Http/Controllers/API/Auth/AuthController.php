<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

class AuthController extends Controller
{
    use JsonResponseTrait;

    private const DUMMY_HASH = '$2y$12$QGYzxPXvopQXXkx29TxsROfguuQLNlZRO1NBierMiIxbpZoWkPLTK';
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const DECAY_SECONDS = 60;

    /**
     * Registers the first user in the system.
     * Registration is only allowed if no users currently exist.
     *
     * @param UserStoreRequest $request Validated request containing the user's name, email, and password.
     * @return JsonResponse HTTP response with redirection or error message.
     */
    public function register(UserStoreRequest $request): JsonResponse
    {
        if (User::exists()) {
            return $this->forbiddenResponse('Cadastro desabilitado. Já existe um usuário no sistema.');
        }

        $validatedData = $request->validated();

        try {
            $userData = array_filter(
                array_intersect_key($validatedData, array_flip(['name', 'email', 'password'])),
                fn($v) => $v !== null && $v !== ''
            );

            User::create($userData);

            return $this->createdResponse(['redirect' => route('login')]);
        } catch (Throwable $e) {
            $this->logError('Erro ao cadastrar o primeiro usuário.', $e, ['data' => $validatedData]);
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
        $key = $email . $request->ip();

        if ($this->tooManyAttempts($key)) {
            return $this->tooManyRequestsResponse("Muitas tentativas. Tente novamente em alguns segundos.");
        }

        $user = User::where('email_hash', hash('sha256', $email, true))->first();

        if (!$this->validateCredentials($user, $password)) {
            $this->incrementAttempts($key);
            return $this->unauthorizedResponse('Credenciais inválidas.');
        }

        $this->clearAttempts($key);

        $token = JWTAuth::fromUser($user);
        $cookie = $this->makeJwtCookie($token);

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
            return $this->successResponse()->cookie(cookie()->forget(env('JWT_COOKIE_NAME', 'jwt_token')));
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

            $cookie = $this->makeJwtCookie($token);

            return $this->successResponse(['token' => $token])->cookie($cookie);
        } catch (Throwable $e) {
            $this->logError('Erro ao atualizar token.', $e, ['token' => $token]);
            return $this->unauthorizedResponse('Não foi possível atualizar o token.');
        }
    }

    /**
     * Creates a secure cookie containing the JWT token.
     *
     * @param string $token The JWT token generated for the authenticated user.
     * @return \Symfony\Component\HttpFoundation\Cookie Secure cookie configured with HttpOnly and SameSite Strict.
     */
    private function makeJwtCookie(string $token)
    {
        return cookie(
            env('JWT_COOKIE_NAME', 'jwt_token'),
            $token,
            env('JWT_COOKIE_TTL', JWTAuth::factory()->getTTL()),
            env('JWT_COOKIE_PATH', '/'),
            env('JWT_COOKIE_DOMAIN', null),
            config('app.env') !== 'local', // Secure only in production
            true,  // HTTPOnly
            false,
            'Strict' // SameSite
        );
    }

    /**
     * Checks if the user has exceeded the maximum number of login attempts.
     *
     * @param string $key Unique key to track attempts (usually email + IP).
     * @return bool True if the limit is exceeded, false otherwise.
     */
    private function tooManyAttempts(string $key): bool
    {
        return RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS);
    }

    /**
     * Increments the login attempt counter to prevent brute-force attacks.
     *
     * @param string $key Unique key to track attempts (usually email + IP).
     * @return void
     */
    private function incrementAttempts(string $key): void
    {
        RateLimiter::hit($key, self::DECAY_SECONDS);
    }

    /**
     * Clears the login attempt counter after a successful authentication.
     *
     * @param string $key Unique key to track attempts (usually email + IP).
     * @return void
     */
    private function clearAttempts(string $key): void
    {
        RateLimiter::clear($key);
    }

    /**
     * Validates the user's credentials.
     *
     * Uses a "dummy" hash when the user does not exist to prevent timing attacks.
     *
     * @param User|null $user The user instance or null if not found.
     * @param string $password The password provided by the user.
     * @return bool True if the password is correct, false otherwise.
     */
    private function validateCredentials(?User $user, string $password): bool
    {
        if (!$user) {
            Hash::check($password, self::DUMMY_HASH);
            return false;
        }

        return Hash::check($password, $user->password);
    }
}
