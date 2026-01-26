<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * Middleware responsible for encrypting and decrypting cookies.
 *
 * This middleware ensures that all cookies are automatically encrypted
 * before being sent to the browser and decrypted when received by the application.
 *
 * However, some cookies may need to remain unencrypted — for example,
 * when handled by third-party services or authentication mechanisms
 * such as JWT-based authentication.
 */
class EncryptCookies extends Middleware
{
    /**
     * The names of cookies that should not be encrypted.
     *
     * Any cookie listed here will be sent and received in plain text,
     * bypassing Laravel's automatic cookie encryption.
     *
     * @var array<int, string>
     */
    protected $except = [];

    /**
     * Create a new EncryptCookies middleware instance.
     *
     * The cookie names are resolved at runtime using the application
     * configuration, since PHP does not allow function calls (such as
     * config()) in property default values.
     */
    public function __construct()
    {
        $this->except = [
            config('jwt.access_cookie'),
            config('jwt.refresh_cookie'),
        ];
    }
}
