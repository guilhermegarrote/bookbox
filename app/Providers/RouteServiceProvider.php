<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * This service provider is responsible for defining how routes are loaded
 * in the application, including API and web routes. It also defines rate
 * limiting rules for the application.
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services, such as route definitions and rate limits.
     */
    public function boot(): void
    {
        $this->routes(function (): void {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'))
            ;

            Route::middleware('web')
                ->group(base_path('routes/web.php'))
            ;
        });

        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(50)->by($request->ip());
        });
    }
}
