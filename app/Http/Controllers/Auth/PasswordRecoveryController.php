<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class PasswordRecoveryController extends Controller
{
    public function showSendRecoveryCodeForm()
    {
        return view('auth.send-recovery-code');
    }

    public function showRecoveryCodeValidationForm()
    {
        return view('auth.recovery-code-validation');
    }

    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }
}
