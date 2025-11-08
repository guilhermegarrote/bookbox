<?php

declare(strict_types=1);

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\TrustProxies;

/**
 * The application's HTTP kernel.
 *
 * This class defines the global middleware stack that runs during every HTTP request.
 * It acts as the main entry point for handling requests and responses.
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string> list of global middleware classes
     */
    protected $middleware = [
        /**
         * Trust proxies and correctly handle forwarded headers (e.g., X-Forwarded-For).
         */
        TrustProxies::class,

        /**
         * Handle Cross-Origin Resource Sharing (CORS) configuration.
         */
        HandleCors::class,

        /**
         * Prevent the application from receiving requests while in maintenance mode.
         */
        PreventRequestsDuringMaintenance::class,

        /**
         * Validate the size of incoming POST requests.
         */
        ValidatePostSize::class,

        /**
         * Automatically trim whitespace from all input strings.
         */
        TrimStrings::class,

        /**
         * Convert all empty input strings to null values.
         */
        ConvertEmptyStringsToNull::class,
    ];
}
