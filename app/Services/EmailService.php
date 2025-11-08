<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Service responsible for sending emails using PHPMailer.
 */
class EmailService
{
    /**
     * PHPMailer instance already configured via AppServiceProvider.
     */
    protected PHPMailer $mailer;

    /**
     * EmailService constructor.
     *
     * @param PHPMailer $mailer configured PHPMailer instance injected via ServiceProvider
     */
    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Generate a numeric recovery code and send it via email to the specified recipient.
     *
     * This method clears any previous recipients or attachments, embeds the application logo,
     * and sends both HTML and plain-text versions of the email.
     *
     * @param string $to recipient email address
     * @param string $name recipient full name
     *
     * @throws Exception if the email fails to send
     *
     * @return string the generated recovery code
     */
    public function sendRecoveryCode(string $to, string $name): string
    {
        $code = (string) rand(100000, 999999);

        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->addAddress($to, $name);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Password Recovery Code';

            $logoPath = public_path('images/logo/logotype-light.png');

            if (file_exists($logoPath)) {
                $this->mailer->addEmbeddedImage($logoPath, 'logo_cid');
            }

            $this->mailer->Body = View::make('emails.recovery-code', [
                'name' => $name,
                'code' => $code,
            ])->render();

            $this->mailer->AltBody = "Olá {$name}, seu código de recuperação é: {$code}";

            $this->mailer->send();
        } catch (Exception $e) {
            Log::error('Failed to send recovery code email: ' . $e->getMessage());

            throw $e;
        }

        return $code;
    }
}
