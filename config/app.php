<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | The name of your application. This is used when the framework needs to
    | place the application's name in notifications or other UI elements.
    |
    */
    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | Determines the environment your application is running in (e.g., local,
    | production, staging). It can affect configuration of services and
    | logging.
    |
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When debug mode is enabled, detailed error messages with stack traces
    | are shown. Disable this in production for security reasons.
    |
    */
    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | The root URL of your application, used by Artisan and URL generation.
    |
    */
    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Default timezone for your application. Used by PHP date and date-time
    | functions.
    |
    */
    'timezone' => env('TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | Default locale used by the translation / localization methods. Fallback
    | locale is used when the default is unavailable.
    |
    */
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | Used by Laravel's encryption services. Should be a random 32-character
    | string to ensure encrypted values are secure.
    |
    */
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Previous Encryption Keys
    |--------------------------------------------------------------------------
    |
    | If you rotate your encryption keys, you can list previous keys here to
    | allow decryption of old data.
    |
    */
    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how Laravel determines if the application is in maintenance
    | mode. Supported drivers: "file" (default) or "cache".
    |
    */
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Encryption Key
    |--------------------------------------------------------------------------
    |
    | Optional key used for custom encryption needs. Set a secure value in
    | your environment file.
    |
    */
    'encryption_key' => env('ENCRYPTION_KEY', 'default_key'),
];
