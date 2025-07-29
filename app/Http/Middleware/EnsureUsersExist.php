<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class EnsureUsersExist
{
    public function handle(Request $request, Closure $next)
    {
        if (!User::exists()) {
            return redirect()->route('register.form');
        }

        return $next($request);
    }
}
