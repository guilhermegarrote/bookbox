<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\RecoverySessionKeys;

class RedirectIfCodeAlreadySent
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get(RecoverySessionKeys::CODE_SENT) === true) {
            return redirect()->route('recovery.code.form')
                ->with('error', 'Você já solicitou um código.');
        }

        return $next($request);
    }
}
