<?php

namespace App\Services;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use App\Http\Traits\ErrorLoggerTrait;
use Exception;
use RuntimeException;

class EmailService
{
    use ErrorLoggerTrait;

    private TransactionalEmailsApi $emailApi;

    public function __construct(TransactionalEmailsApi $emailApi)
    {
        $this->emailApi = $emailApi;
    }

    public function sendCode(string $email, string $userName): string
    {
        $code = $this->generateCode();

        $html = view('emails.password_reset', [
            'email' => $email,
            'userName' => $userName,
            'code' => $code
        ])->render();

        $path = public_path('images/logo/logotype-light.png');

        if (!file_exists($path)) {
            throw new \Exception("Logo não encontrada em: $path");
        }

        // Converte a imagem em Base64
        $logoBase64 = base64_encode(file_get_contents($path));

        // Monta o e-mail
        $recoveryEmail = new SendSmtpEmail([
            'subject' => 'Recuperação de Senha',
            'sender' => [
                'name' => config('mail.from.name'),
                'email' => config('mail.from.address')
            ],
            'to' => [['email' => $email]],
            'htmlContent' => $html,
            'params' => ['code' => $code],
            'inlineImageActivation' => true,
            'inlineImages' => [[
                'name' => 'logotype-light.png',  // nome do arquivo
                'content' => $logoBase64,        // imagem em Base64
                'contentId' => 'bookbox_logo'    // deve bater com o "cid:" no HTML
            ]]
        ]);


        try {
            $this->emailApi->sendTransacEmail($recoveryEmail);
        } catch (Exception $e) {
            $this->logError('Erro ao enviar e-mail para ' . $email, $e);

            throw new RuntimeException('Não foi possível enviar o e-mail de recuperação.');
        }

        return $code;
    }

    private function generateCode(): string
    {
        return (string) rand(100000, 999999);
    }
}
