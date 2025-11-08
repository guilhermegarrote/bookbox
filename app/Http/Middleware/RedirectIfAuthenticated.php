<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that redirects authenticated users away from guest routes.
 *
 * Commonly used to prevent logged-in users from accessing login or
 * registration pages.
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request the current HTTP request
     * @param \Closure $next the next middleware or request handler
     * @param null|string ...$guards The authentication guards.
     */
    public function handle(Request $request, \Closure $next, ...$guards): RedirectResponse|Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect()->route('loans.view');
            }
        }

        return $next($request);
    }
}
