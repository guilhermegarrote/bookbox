<?php

namespace App\Enums;

class RecoverySessionKeys
{
    public const EMAIL = 'email_verified';
    public const CODE_SENT = 'code_sent';
    public const CODE_VALIDATED = 'code_validated';
    public const EXPIRATION = 'password_reset_expires_at';
}
