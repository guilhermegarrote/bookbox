<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class JwtCookieMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('jwt_token');

        if (!$token) {
            Log::warning('Tentativa de acesso sem token JWT no cookie.' . $token);
            return redirect()->route('login');
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                Log::warning('Token JWT inválido: usuário não encontrado.');
                return redirect()->route('login');
            }

            $request->setUserResolver(fn() => $user);
        } catch (TokenExpiredException $e) {
            Log::info('Token JWT expirado.', ['exception' => $e]);
            return redirect()->route('login');
        } catch (JWTException $e) {
            Log::error('Erro ao autenticar token JWT.', ['exception' => $e]);
            return redirect()->route('login');
        }

        return $next($request);
    }
}
