<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum representing the session keys used during the password recovery process.
 *
 * This enum defines all the keys stored in the user recovery session.
 * These keys are used to verify user identity, track the validation state,
 * and manage expiration times for password reset tokens.
 *
 * @see \App\Http\Controllers\Auth\PasswordRecoveryController
 *   for how these session keys are used in the password recovery workflow.
 */
enum RecoverySessionKey: string
{
    /**
     * Indicates that the user's email has been successfully verified.
     *
     * @var string
     */
    case EMAIL_VERIFIED = 'email_verified';

    /**
     * Stores whether a recovery code has been sent to the user's email.
     *
     * @var string
     */
    case CODE_SENT = 'code_sent';

    /**
     * Indicates whether the user-provided recovery code was validated successfully.
     *
     * @var string
     */
    case CODE_VALIDATED = 'code_validated';

    /**
     * The timestamp indicating when the password reset session expires.
     *
     * @var string
     */
    case PASSWORD_RESET_EXPIRATION = 'password_reset_expires_at';
}
