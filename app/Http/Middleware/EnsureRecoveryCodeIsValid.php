<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\RecoverySessionKeys;

class EnsureRecoveryCodeIsValid
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->get(RecoverySessionKeys::CODE_VALIDATED)) {
            return redirect()->route('recovery.code.form')
                ->with('error', 'Valide o código antes de continuar.');
        }

        $expiresAt = $request->session()->get(RecoverySessionKeys::EXPIRATION);

        if ($expiresAt && now()->greaterThan($expiresAt)) {
            $request->session()->forget([
                RecoverySessionKeys::CODE_SENT,
                RecoverySessionKeys::CODE_VALIDATED,
                RecoverySessionKeys::EMAIL,
                RecoverySessionKeys::EXPIRATION,
            ]);

            return redirect()->route('recovery.email.form')
                ->with('error', 'Tempo para redefinição expirado. Solicite um novo código.');
        }

        return $next($request);
    }
}
