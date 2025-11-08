<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\CustomUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

/**
 * Registers authentication and authorization services for the application.
 *
 * Specifically, this provider registers a custom user provider to support
 * binary UUID identifiers in users tables, allowing Laravel's authentication
 * system to correctly retrieve users by their UUIDs.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * Each entry maps a model class to a corresponding policy class.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * This method registers a custom user provider under the "custom" key.
     * The custom provider supports binary UUIDs, ensuring that users can be
     * authenticated correctly when their primary key is stored as binary.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Auth::provider('custom', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });
    }
}
