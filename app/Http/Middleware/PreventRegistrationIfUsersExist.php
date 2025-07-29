<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class PreventRegistrationIfUsersExist
{
    public function handle(Request $request, Closure $next)
    {
        if (User::exists()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
