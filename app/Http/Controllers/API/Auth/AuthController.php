<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Traits\JsonResponseTrait;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Handles user authentication, registration, and JWT token lifecycle operations.
 * This controller provides endpoints for login, logout, token refresh, and
 * user self-identification, all returning JSON-based API responses.
 *
 * @see LoginRequest for validation of login credentials.
 * @see UserStoreRequest for validation of user registration data.
 * @see JWTAuth for JWT token management.
 * @see RateLimiter for rate limiting login attempts.
 */
class AuthController extends Controller
{
    use JsonResponseTrait;

    /**
     * A static hash used to prevent timing attacks when the user does not exist.
     *
     * @var string
     */
    private const DUMMY_HASH = '$2y$12$QGYzxPXvopQXXkx29TxsROfguuQLNlZRO1NBierMiIxbpZoWkPLTK';

    /**
     * Maximum allowed login attempts before temporary lockout.
     *
     * @var int
     */
    private const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Number of seconds before login attempt counter resets.
     *
     * @var int
     */
    private const DECAY_SECONDS = 60;

    /**
     * Registers the first user in the system.
     *
     * This method allows registration **only if** there are no users yet.
     * It ensures that the first admin or root account is securely created.
     *
     * @param UserStoreRequest $request validated data containing name, email, and password
     *
     * @return JsonResponse HTTP 201 response with redirect or 403 if registration is disabled
     *
     * @see User::create
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
                static fn ($v) => $v !== null && $v !== '',
            );

            User::create($userData);

            return $this->createdResponse(['redirect' => route('login')]);
        } catch (\Throwable $e) {
            $this->logError('Erro ao cadastrar o primeiro usuário.', $e, ['data' => $validatedData]);

            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar o primeiro usuário.');
        }
    }

    /**
     * Authenticates a user and issues a JWT token.
     *
     * Validates email and password. If successful, returns a secure JWT cookie
     * and basic user data. Implements rate limiting to prevent brute-force attacks.
     *
     * @param LoginRequest $request validated login credentials
     *
     * @return JsonResponse HTTP 200 response with token and user info, or error message
     *
     * @see self::tooManyAttempts()
     * @see self::validateCredentials()
     * @see \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::fromUser()
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $email = strtolower(trim($validatedData['email']));
        $password = trim($validatedData['password']);
        $key = $email . $request->ip();

        if ($this->tooManyAttempts($key)) {
            return $this->tooManyRequestsResponse('Muitas tentativas. Tente novamente em alguns segundos.');
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
     * Retrieves the currently authenticated user's data.
     *
     * Parses the JWT token and returns the associated user's basic information.
     *
     * @return JsonResponse HTTP 200 with user data or 401 if authentication fails
     *
     * @see \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::parseToken()
     */
    public function me(): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->unauthorizedResponse('Usuário não autenticado.');
            }

            return $this->successResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        } catch (\Throwable) {
            return $this->unauthorizedResponse('Sessão expirada ou token inválido.');
        }
    }

    /**
     * Logs out the authenticated user.
     *
     * Invalidates the current JWT and removes it from the client.
     *
     * @return JsonResponse HTTP 200 with cookie cleared
     *
     * @see \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::invalidate()
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (\Throwable) {
            // Token may already be invalidated; ignore
        }

        return $this->successResponse()
            ->cookie(cookie()->forget(env('JWT_COOKIE_NAME', 'jwt_token')))
        ;
    }

    /**
     * Refreshes the JWT token to extend session validity.
     *
     * @return JsonResponse HTTP 200 with new token cookie or 401 on failure
     *
     * @see \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::refresh()
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            $cookie = $this->makeJwtCookie($token);

            return $this->successResponse(['token' => $token])->cookie($cookie);
        } catch (\Throwable $e) {
            $this->logError('Erro ao atualizar token.', $e);

            return $this->unauthorizedResponse('Não foi possível atualizar o token.');
        }
    }

    /**
     * Creates a secure HTTP-only cookie for storing the JWT token.
     *
     * @param string $token JWT token to embed in the cookie
     *
     * @return Cookie secure cookie instance with SameSite=Strict
     */
    private function makeJwtCookie(string $token): Cookie
    {
        return cookie(
            env('JWT_COOKIE_NAME', 'jwt_token'),
            $token,
            env('JWT_COOKIE_TTL', JWTAuth::factory()->getTTL()),
            env('JWT_COOKIE_PATH', '/'),
            env('JWT_COOKIE_DOMAIN', null),
            config('app.env') !== 'local', // Secure in production only
            true,
            false,
            'Strict',
        );
    }

    /**
     * Checks if too many failed login attempts occurred.
     *
     * @param string $key unique key identifying the login attempt (email + IP)
     *
     * @return bool true if rate limit exceeded
     */
    private function tooManyAttempts(string $key): bool
    {
        return RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS);
    }

    /**
     * Increments login attempt counter.
     *
     * @param string $key unique key identifying the login attempt (email + IP)
     */
    private function incrementAttempts(string $key): void
    {
        RateLimiter::hit($key, self::DECAY_SECONDS);
    }

    /**
     * Clears login attempt counter after successful authentication.
     *
     * @param string $key unique key identifying the login attempt (email + IP)
     */
    private function clearAttempts(string $key): void
    {
        RateLimiter::clear($key);
    }

    /**
     * Validates the user's password and mitigates timing attacks.
     *
     * @param null|User $user the retrieved user or null if not found
     * @param string $password the password provided by the user
     *
     * @return bool true if credentials are valid
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
