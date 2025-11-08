<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureRecoveryCodeIsValid;
use App\Http\Middleware\EnsureRecoveryCodeWasSent;
use App\Http\Middleware\EnsureUsersExist;
use App\Http\Middleware\JwtCookieMiddleware;
use App\Http\Middleware\PreventRegistrationIfUsersExist;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfCodeAlreadySent;
use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate as JWTAuthenticate;

/**
 * Application bootstrap and configuration.
 */
return Application::configure(basePath: \dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => MiddlewareAuthenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'auth.jwt' => JWTAuthenticate::class,
            'auth.jwt.cookie' => JwtCookieMiddleware::class,
            'guest' => RedirectIfAuthenticated::class,

            'code.sent' => EnsureRecoveryCodeWasSent::class,
            'code.valid' => EnsureRecoveryCodeIsValid::class,
            'code.not.sent' => RedirectIfCodeAlreadySent::class,

            'prevent.registration' => PreventRegistrationIfUsersExist::class,
            'users.exist' => EnsureUsersExist::class,

            'can' => Authorize::class,
            'password.confirm' => RequirePassword::class,
            'verified' => EnsureEmailIsVerified::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'cache.headers' => SetCacheHeaders::class,
        ]);

        $middleware->web([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->api([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            'throttle:api',
            SubstituteBindings::class,
        ]);
    })
    ->withProviders([
        App\Providers\RouteServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\AppServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        // Place for global exception handling customization if needed
    })
    ->create()
;
