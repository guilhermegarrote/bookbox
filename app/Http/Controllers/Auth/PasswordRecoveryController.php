<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Controller responsible for rendering password recovery views,
 * including code request, code validation, and password reset forms.
 */
class PasswordRecoveryController extends Controller
{
    /**
     * Display the form for requesting a password recovery code.
     *
     * @return View the view for sending a password recovery code
     *
     * @see resources/views/auth/send-recovery-code.blade.php
     */
    public function showSendRecoveryCodeForm(): View
    {
        return view('auth.send-recovery-code');
    }

    /**
     * Display the form for validating the received recovery code.
     *
     * @return View the view for recovery code validation
     *
     * @see resources/views/auth/recovery-code-validation.blade.php
     */
    public function showRecoveryCodeValidationForm(): View
    {
        return view('auth.recovery-code-validation');
    }

    /**
     * Display the form for creating a new password after successful code validation.
     *
     * @return View the view for resetting the user's password
     *
     * @see resources/views/auth/reset-password.blade.php
     */
    public function showResetPasswordForm(): View
    {
        return view('auth.reset-password');
    }
}
