<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that redirects authenticated users away from guest routes.
 *
 * Detects both web users (sessions) and API users (JWT via PHPOpenSourceSaver\JWTAuth).
 * Prevents authenticated users from accessing login, register, or recovery routes.
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request The current HTTP request.
     * @param Closure $next The next middleware.
     * @param string[] ...$guards Optional authentication guards.
     *
     * @return RedirectResponse|Response
     */
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? ['web'] : $guards;

        foreach ($guards as $guard) {
            $user = Auth::guard($guard)->user();
            $jwtUser = null;

            if (!$user && $guard === 'api') {
                $jwtUser = $this->getApiUserFromRequest($request);
                $user = $jwtUser;
            }

            if ($user) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Already authenticated.'
                    ], 403);
                }

                return redirect()->route('loans.view');
            }
        }

        return $next($request);
    }

    /**
     * Attempt to get the API user from JWT in the request using JWTAuth.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function getApiUserFromRequest(Request $request)
    {
        try {
            $token = $request->bearerToken() ?? $request->cookie('access_token');

            if (!$token) {
                return null;
            }

            return JWTAuth::setToken($token)->authenticate();
        } catch (\Exception $e) {
            return null;
        }
    }
}
