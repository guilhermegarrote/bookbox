<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that ensures at least one user exists in the system.
 *
 * Used to redirect to the registration form when the database
 * has no users yet (e.g., on first system setup).
 */
class EnsureUsersExist
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request the current HTTP request
     * @param \Closure $next the next middleware or request handler
     */
    public function handle(Request $request, \Closure $next): RedirectResponse|Response
    {
        if (!User::exists()) {
            return redirect()->route('register.form');
        }

        return $next($request);
    }
}
