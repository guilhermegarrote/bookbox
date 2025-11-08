<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\EmailService;
use Illuminate\Support\ServiceProvider;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Registers application services and dependencies.
 * Specifically, it configures PHPMailer and binds EmailService to the container.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This method binds PHPMailer as a singleton in the container with
     * SMTP configuration from the app config, and also binds the
     * EmailService as a singleton, injecting PHPMailer into it.
     */
    public function register(): void
    {
        $this->app->singleton(PHPMailer::class, function () {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption') ?? 'tls';
            $mail->Port = config('mail.mailers.smtp.port');

            $mail->setFrom(
                config('mail.from.address'),
                config('mail.from.name'),
            );

            return $mail;
        });

        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService($app->make(PHPMailer::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
