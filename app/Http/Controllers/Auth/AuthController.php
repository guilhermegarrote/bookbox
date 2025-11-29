<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Controller responsible for rendering authentication-related views,
 * including login and registration forms.
 */
class AuthController extends Controller
{
    /**
     * Display the user registration form.
     *
     * @return View the registration view for new users
     *
     * @see resources/views/pages/auth/register.blade.php
     */
    public function showRegisterForm(): View
    {
        return view('pages.auth.register');
    }

    /**
     * Display the user login form.
     *
     * @return View the login view for existing users
     *
     * @see resources/views/pages/auth/login.blade.php
     */
    public function showLoginForm(): View
    {
        return view('pages.auth.login');
    }
}
