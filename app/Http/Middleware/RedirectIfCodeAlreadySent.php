<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RecoverySessionKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that prevents requesting a new recovery code
 * if one has already been sent and is still valid.
 *
 * Ensures proper flow in password recovery and avoids spam
 * of repeated code requests.
 */
class RedirectIfCodeAlreadySent
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request the current HTTP request
     * @param \Closure $next the next middleware or request handler
     */
    public function handle(Request $request, \Closure $next): RedirectResponse|Response
    {
        if ($request->session()->get(RecoverySessionKey::CODE_SENT) === true) {
            return redirect()
                ->route('recovery.code.form')
                ->with('error', 'Você já solicitou um código.')
            ;
        }

        return $next($request);
    }
}
