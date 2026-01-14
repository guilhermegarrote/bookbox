<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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
    public function handle(Request $request, \Closure $next)
    {
        try {
            $accessToken = $request->cookie('access_token');
            $refreshToken = $request->cookie('refresh_token');

            $tokenToUse = $accessToken ?? $refreshToken;

            if (!$tokenToUse) {
                return response()->json(['message' => 'Não autorizado'], 401);
            }

            JWTAuth::setToken($tokenToUse);

            $user = JWTAuth::authenticate();

            if (!$user) {
                return response()->json(['message' => 'Não autorizado'], 401);
            }

            $request->setUserResolver(fn() => $user);
        } catch (TokenExpiredException $e) {
            Log::info('JWT token expired.');

            return response()->json(['message' => 'Token expirado'], 401);
        } catch (JWTException $e) {
            Log::error('Erro ao autenticar token JWT.', ['exception' => $e]);

            return response()->json(['message' => 'Token inválido'], 401);
        }

        return $next($request);
    }
}
