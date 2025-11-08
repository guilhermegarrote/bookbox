<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that prevents registration if users already exist.
 *
 * Ensures that registration is only available during the
 * initial setup of the system.
 */
class PreventRegistrationIfUsersExist
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next): RedirectResponse|Response
    {
        if (User::exists()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
