<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * This service provider is responsible for registering all the event listeners
 * for the application. Events provide a simple observer pattern implementation
 * that allows your application to subscribe and listen for various actions
 * or state changes in your system.
 *
 * Example usage:
 * - When a user registers, fire a UserRegistered event.
 * - Attach a listener that sends a welcome email to the new user.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * This array maps event classes to their respective listener classes.
     * When an event is fired, all registered listeners for that event are executed.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        // 'App\Events\EventName' => [
        //     'App\Listeners\EventListener',
        // ],
    ];

    /**
     * Register any events and listeners for your application.
     *
     * This method is called during the bootstrapping of the service provider.
     * You may register additional events here if they are not listed in $listen.
     */
    public function boot(): void
    {
        parent::boot();

        // Additional dynamic event registration can be done here.
    }
}
