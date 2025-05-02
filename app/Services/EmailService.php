<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

class EmailService
{
    private $apiEmail;   
    private $codigo;     

    public function __construct()
    {

        $this->codigo = rand(100000, 999999);

        $configuracao = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', getenv('BREVO_API_KEY'));

        $this->apiEmail = new TransactionalEmailsApi(
            new Client(),
            $configuracao
        );
    }

    public function enviarCodigo($email)
    {
        $emailRecuperacao = new SendSmtpEmail([
            'subject' => 'Recuperação de Senha',
            'sender' => ['name' => 'Bookbox', 'email' => 'bookboxsystem@gmail.com'],
            'to' => [[ 'email' => $email]],
            'htmlContent' => "<html><body>
                                <p>Olá, {$email},</p>
                                <p>Seu código de recuperação de senha é: <strong>{$this->codigo}</strong></p>
                                <p>Use este código para redefinir sua senha. Ele é válido por tempo limitado.</p>
                              </body></html>",
            'params' => ['codigo' => $this->codigo]
        ]);

        try {
            $resultado = $this->apiEmail->sendTransacEmail($emailRecuperacao);
            print_r($resultado);
        } catch (Exception $e) {
            echo 'Erro ao enviar e-mail: ', $e->getMessage(), PHP_EOL;
        }
    }


    public function getCodigo()
    {
        return $this->codigo;
    }
}

$recuperacao = new EmailService();
$codigo = $recuperacao->getCodigo();

