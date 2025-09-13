<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class EmailService
{
    protected PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host       = config('mail.mailers.smtp.host');
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = config('mail.mailers.smtp.username');
        $this->mailer->Password   = config('mail.mailers.smtp.password');
        $this->mailer->SMTPSecure = config('mail.mailers.smtp.encryption');
        $this->mailer->Port       = config('mail.mailers.smtp.port');

        $this->mailer->setFrom(
            config('mail.from.address'),
            config('mail.from.name')
        );
    }

    /**
     * Generate and send a recovery code.
     *
     * @param string $to   Recipient email
     * @param string $name Recipient name
     * @return string The generated recovery code
     */
    public function sendRecoveyCode(string $to, string $name): string
    {
        $code = (string) rand(100000, 999999);

        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->addAddress($to, $name);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Password Recovery Code';

            $logoPath = public_path('images/logo/logotype-light.png');
            $this->mailer->addEmbeddedImage($logoPath, 'logo_cid');

            $this->mailer->Body = View::make('emails.recovery-code', [
                'name' => $name,
                'code' => $code,
            ])->render();

            $this->mailer->AltBody = "Olá {$name}, seu código de recuperação é: {$code}";

            $this->mailer->send();
        } catch (Exception $e) {
            Log::error("Failed to send recovery code email: " . $e->getMessage());
            throw $e;
        }

        return $code;
    }
}
