<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RecoverySessionKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that ensures the password recovery code has been validated
 * before allowing access to sensitive routes, such as password reset pages.
 *
 * This middleware performs two main checks:
 * 1. Verifies whether the recovery code was validated in the session.
 * 2. Ensures that the recovery time window has not expired.
 *
 * If either condition fails, the user is redirected back to the appropriate
 * recovery step with an error message.
 */
class EnsureRecoveryCodeIsValid
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
        if (!$request->session()->get(RecoverySessionKey::CODE_VALIDATED->value)) {
            return redirect()
                ->route('recovery.code.form')
                ->with('error', 'Valide o código antes de continuar.')
            ;
        }

        $expiresAt = $request->session()->get(RecoverySessionKey::PASSWORD_RESET_EXPIRATION->value);

        if ($expiresAt && now()->greaterThan($expiresAt)) {
            $request->session()->forget([
                RecoverySessionKey::CODE_SENT->value,
                RecoverySessionKey::CODE_VALIDATED->value,
                RecoverySessionKey::EMAIL_VERIFIED->value,
                RecoverySessionKey::PASSWORD_RESET_EXPIRATION->value,
            ]);

            return redirect()
                ->route('recovery.email.form')
                ->with('error', 'Tempo para redefinição expirado. Solicite um novo código.')
            ;
        }

        return $next($request);
    }
}
