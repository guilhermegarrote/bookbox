<?php

namespace App\Providers;

use App\Helpers\Utils;
use App\Models\View\StudentSchoolClass;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::bind('student', function ($value) {
            return StudentSchoolClass::where('student_id', Utils::convertUuidToBinary($value))->firstOrFail();
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(50)->by($request->ip());
        });
    }
}
