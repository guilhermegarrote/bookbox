<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RecoverySessionKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that ensures a recovery code has been requested
 * before allowing the user to proceed to the code validation step.
 *
 * This is used in the password recovery flow to prevent direct access
 * to code validation routes without first requesting a recovery code.
 */
class EnsureRecoveryCodeWasSent
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request the current HTTP request instance
     * @param \Closure $next the next middleware or request handler
     *
     * @see App\Enums\RecoverySessionKey For session key constants used in the password recovery flow.
     */
    public function handle(Request $request, \Closure $next): RedirectResponse|Response
    {
        if (!$request->session()->get(RecoverySessionKey::CODE_SENT->value)) {
            return redirect()
                ->route('recovery.email.form')
                ->with('error', 'Solicite o código antes de continuar.')
            ;
        }

        return $next($request);
    }
}
