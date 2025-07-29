<?php

namespace App\Providers;

use App\Services\EmailService;
use Illuminate\Support\ServiceProvider;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TransactionalEmailsApi::class, function () {
            $config = Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', config('services.brevo.key'));
            return new TransactionalEmailsApi(new Client(), $config);
        });

        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService($app->make(TransactionalEmailsApi::class));
        });
    }

    public function boot(): void
    {
        //
    }
}
