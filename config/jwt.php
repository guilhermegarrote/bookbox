<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Do not forget to set this in your .env file, as it is used to sign
    | your tokens. A helper command is provided for this:
    | `php artisan jwt:secret`
    |
    | Note: This is only used for symmetric algorithms (HMAC).
    | RSA and ECDSA use a private/public key pair (see below).
    |
    */
    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Keys
    |--------------------------------------------------------------------------
    |
    | The algorithm you use determines whether your tokens are signed
    | with a random string (defined in `JWT_SECRET`) or with the
    | public/private keys below.
    |
    | Symmetric Algorithms (HS256, HS384, HS512) use `JWT_SECRET`.
    | Asymmetric Algorithms (RS256, RS384, RS512, ES256, ES384, ES512)
    | use the keys below.
    |
    */
    'keys' => [
        'public' => env('JWT_PUBLIC_KEY'),   // Path to public key
        'private' => env('JWT_PRIVATE_KEY'), // Path to private key
        'passphrase' => env('JWT_PASSPHRASE'), // Optional passphrase
    ],

    /*
    |--------------------------------------------------------------------------
    | JWT Time to Live
    |--------------------------------------------------------------------------
    |
    | Specify how long (in minutes) a token is valid for.
    | Defaults to 60 minutes (1 hour).
    |
    | Set to null for a token that never expires (not recommended).
    | If null, remove 'exp' from the 'required_claims' list.
    |
    */
    'ttl' => (int) env('JWT_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | Refresh Time to Live
    |--------------------------------------------------------------------------
    |
    | Specify how long (in minutes) a token can be refreshed.
    | Defaults to 2 weeks (720 minutes).
    |
    | Set to null for infinite refresh time (not recommended).
    |
    */
    'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 720),

    /*
    |--------------------------------------------------------------------------
    | JWT Hashing Algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the hashing algorithm used to sign the token.
    | See: https://github.com/namshi/jose/tree/master/src/Namshi/JOSE/Signer/OpenSSL
    |
    */
    'algo' => env('JWT_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | Required Claims
    |--------------------------------------------------------------------------
    |
    | Specify claims that must exist in any token.
    | TokenInvalidException is thrown if any are missing.
    |
    */
    'required_claims' => [
        'iss',
        'iat',
        'exp',
        'nbf',
        'sub',
        'jti',
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistent Claims
    |--------------------------------------------------------------------------
    |
    | Specify claim keys to be persisted when refreshing a token.
    | `sub` and `iat` are always persisted automatically.
    |
    */
    'persistent_claims' => [
        // 'foo',
        // 'bar',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lock Subject
    |--------------------------------------------------------------------------
    |
    | Determines whether a `prv` claim is added to the token.
    | Helps prevent token impersonation across multiple authentication models.
    |
    | Disable if you only have a single authentication model.
    |
    */
    'lock_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | Leeway
    |--------------------------------------------------------------------------
    |
    | Adds "leeway" to timestamp claims to allow for slight clock skew
    | on your servers. Applies to `iat`, `nbf`, and `exp`.
    |
    | Specify in seconds.
    |
    */
    'leeway' => (int) env('JWT_LEEWAY', 0),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Enabled
    |--------------------------------------------------------------------------
    |
    | Enable to allow invalidation of tokens via a blacklist.
    | Set to false if not needed.
    |
    */
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Grace Period
    |--------------------------------------------------------------------------
    |
    | Prevents parallel request failures when multiple requests
    | use the same token by setting a grace period (seconds).
    |
    */
    'blacklist_grace_period' => (int) env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    /*
    |--------------------------------------------------------------------------
    | Show Blacklisted Token Option
    |--------------------------------------------------------------------------
    |
    | If true, blacklisted token exceptions are logged in Laravel.
    |
    */
    'show_black_list_exception' => env('JWT_SHOW_BLACKLIST_EXCEPTION', true),

    /*
    |--------------------------------------------------------------------------
    | Cookies Encryption
    |--------------------------------------------------------------------------
    |
    | By default, Laravel encrypts cookies. If you disable decryption,
    | add the cookie name to the $except array in the EncryptCookies
    | middleware.
    |
    | Set to true to decrypt cookies.
    |
    */
    'decrypt_cookies' => false,

    /*
    |--------------------------------------------------------------------------
    | Cookie Key Name
    |--------------------------------------------------------------------------
    |
    | Name of the cookie used to store the JWT.
    |
    */
    'cookie_key_name' => env('JWT_COOKIE_NAME', 'jwt_token'),
    'access_cookie' => env('JWT_ACCESS_COOKIE', 'access_token'),
    'refresh_cookie' => env('JWT_REFRESH_COOKIE', 'refresh_token'),

    /*
    |--------------------------------------------------------------------------
    | Cookie Settings
    |--------------------------------------------------------------------------
    |
    | Customize cookie behavior for JWT storage.
    |
    */
    'cookie_path' => env('JWT_COOKIE_PATH', '/'),
    'cookie_domain' => env('JWT_COOKIE_DOMAIN', null),
    'cookie_secure' => env('JWT_COOKIE_SECURE', env('APP_ENV') !== 'local'),
    'cookie_http_only' => env('JWT_COOKIE_HTTPONLY', true),
    'cookie_raw' => env('JWT_COOKIE_RAW', false),
    'cookie_same_site' => env('JWT_COOKIE_SAMESITE', 'Strict'),

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    |
    | Define the various providers used in the package.
    |
    */
    'providers' => [
        'jwt' => PHPOpenSourceSaver\JWTAuth\Providers\JWT\Lcobucci::class,
        'auth' => PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate::class,
        'storage' => PHPOpenSourceSaver\JWTAuth\Providers\Storage\Illuminate::class,
    ],
];
