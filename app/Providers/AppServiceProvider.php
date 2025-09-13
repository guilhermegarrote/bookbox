<?php

namespace App\Providers;

use App\Services\EmailService;
use Illuminate\Support\ServiceProvider;
use PHPMailer\PHPMailer\PHPMailer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PHPMailer::class, function () {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = config('mail.mailers.smtp.host');
            $mail->SMTPAuth   = true;
            $mail->Username   = config('mail.mailers.smtp.username');
            $mail->Password   = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption') ?? 'tls';
            $mail->Port       = config('mail.mailers.smtp.port');

            $mail->setFrom(
                config('mail.from.address'),
                config('mail.from.name')
            );

            return $mail;
        });

        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService($app->make(PHPMailer::class));
        });
    }

    public function boot(): void
    {
        //
    }
}
