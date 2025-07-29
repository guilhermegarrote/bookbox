<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\RecoverySessionKeys;

class EnsureRecoveryCodeWasSent
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->get(RecoverySessionKeys::CODE_SENT)) {
            return redirect()->route('recovery.email.form')
                ->with('error', 'Solicite o código antes de continuar.');
        }

        return $next($request);
    }
}
