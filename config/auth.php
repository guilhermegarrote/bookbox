<?php

declare(strict_types=1);

use App\Models\User;

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Define the default authentication "guard" and password reset "broker"
    | for your application. You may change these values as required.
    |
    */
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Define every authentication guard for your application. Each guard
    | uses a user provider to retrieve users from your storage system.
    | Here, we use JWT for API authentication.
    |
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'jwt',      // JWT-based authentication
            'provider' => 'users',  // Uses the custom provider for UUID support
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | User providers define how users are retrieved from your database or
    | other storage. You can configure multiple providers if needed.
    |
    | 'custom' driver points to CustomUserProvider, which handles binary
    | UUIDs correctly.
    |
    */
    'providers' => [
        'users' => [
            'driver' => 'custom',
            'model' => User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Settings
    |--------------------------------------------------------------------------
    |
    | Define the password reset behavior, including token table, expiration,
    | and throttle to prevent abuse.
    |
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,   // Minutes until token expires
            'throttle' => 60, // Seconds before requesting a new token
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Number of seconds before a password confirmation window expires.
    | Default is 3 hours.
    |
    */
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
