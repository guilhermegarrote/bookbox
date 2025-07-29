<?php

// Laravel Core

use App\Http\Middleware\EnsureRecoveryCodeIsValid;
use App\Http\Middleware\EnsureRecoveryCodeWasSent;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Default middlewares (web and api)
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;

// Authentication/authorization middlewares
use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;

// Route middlewares
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Http\Middleware\SetCacheHeaders;

// Custom middlewares
use App\Http\Middleware\EnsureUsersExist;
use App\Http\Middleware\JwtCookieMiddleware;
use App\Http\Middleware\PreventRegistrationIfUsersExist;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfCodeAlreadySent;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate as JWTAuthenticate;

// Application bootstrap
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Aliases for use in routes (e.g. middleware('auth'))
        $middleware->alias([
            // Authentication and security
            'auth' => MiddlewareAuthenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'auth.jwt' => JWTAuthenticate::class,
            'auth.jwt.cookie' => JwtCookieMiddleware::class,
            'code.sent' => EnsureRecoveryCodeWasSent::class,
            'code.valid' => EnsureRecoveryCodeIsValid::class,
            'code.not.sent' => RedirectIfCodeAlreadySent::class,
            'password.confirm' => RequirePassword::class,
            'verified' => EnsureEmailIsVerified::class,
            'guest' => RedirectIfAuthenticated::class,
            'prevent.registration' => PreventRegistrationIfUsersExist::class,
            'users.exist' => EnsureUsersExist::class,

            // Access control and cache
            'can' => Authorize::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'cache.headers' => SetCacheHeaders::class,
        ]);

        // Global middlewares for the web group
        $middleware->web([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        // Global middlewares for the api group
        $middleware->api([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class, // optional in API
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
        // You can add global exception handling here if desired.
    })
    ->create();
