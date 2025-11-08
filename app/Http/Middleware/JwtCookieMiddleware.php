<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that authenticates users using a JWT stored in a cookie.
 *
 * Validates the token, authenticates the user, and attaches the user
 * to the current request. Redirects or returns 401 on failure.
 */
class JwtCookieMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request the current HTTP request
     * @param \Closure $next the next middleware or request handler
     */
    public function handle(Request $request, \Closure $next): JsonResponse|RedirectResponse|Response
    {
        $token = $request->cookie('jwt_token');

        if (!$token) {
            Log::info('Access attempt without JWT token.');

            return $this->unauthorizedResponse($request);
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                Log::warning('Invalid JWT token: user not found.');

                return $this->unauthorizedResponse($request);
            }

            $request->setUserResolver(fn () => $user);
        } catch (TokenExpiredException $e) {
            Log::info('JWT token expired.');

            return $this->unauthorizedResponse($request, 'Token expirado.');
        } catch (JWTException $e) {
            Log::error('Error authenticating JWT token.', ['exception' => $e]);

            return $this->unauthorizedResponse($request);
        }

        return $next($request);
    }

    /**
     * Returns an unauthorized response or redirect.
     */
    private function unauthorizedResponse(Request $request, string $message = 'Não autorizado.'): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => $message], 401);
        }

        return redirect()->route('login');
    }
}
