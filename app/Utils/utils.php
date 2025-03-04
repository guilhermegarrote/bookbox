<?php

// ===============================
// Funções de Sessão
// ===============================
/**
 * Adiciona um erro (ou mensagem) na sessão.
 *
 * @param string $campo O campo onde o erro ocorreu.
 * @param string $mensagem A mensagem de erro.
 * @param string $tipo O tipo da mensagem ('erro', 'aviso', 'informacao').
 * @param bool $unico Se for true, evitar duplicação de mensagens para o mesmo campo.
 */
function adicionarMensagemSessao($campo, $mensagem, $tipo = 'erro', $unico = false)
{
    if (empty($campo) || empty($mensagem)) {
        throw new InvalidArgumentException("Campo e mensagem são obrigatórios.");
    }

    $tiposValidos = ['erro', 'aviso', 'informacao'];
    if (!in_array($tipo, $tiposValidos)) {
        throw new InvalidArgumentException("Tipo de erro inválido. Os tipos válidos são: 'erro', 'aviso', 'informacao'.");
    }

    if (!isset($_SESSION['errors'])) {
        $_SESSION['errors'] = [];
    }

    if (!isset($_SESSION['errors'][$campo])) {
        $_SESSION['errors'][$campo] = [];
    }

    if ($unico && in_array($mensagem, $_SESSION['errors'][$campo])) {
        return;
    }

    $_SESSION['errors'][$campo][] = [
        'mensagem' => $mensagem,
        'tipo' => $tipo
    ];
}


/**
 * Exibe os erros armazenados na sessão.
 * @return void
 */
function exibirErros()
{
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $campo => $mensagens) {
            foreach ($mensagens as $mensagem) {
                echo "<div class='alert alert-danger'>Erro no campo '$campo': $mensagem</div>";
            }
        }
    }
}

/**
 * Limpa os erros armazenados na sessão.
 * @return void
 */
function limparErrosSessao()
{
    unset($_SESSION['errors']);
}

// ===============================
// Funções de criptografia
// ===============================
/**
 * Retorna a criptografia do parametro passado
 * @param string $dado
 * @return string $dadoCriptografado
 */
function criptografar($dado)
{
    $chave = getenv('ENCRYPTION_KEY');
    $metodo = "AES-256-CBC";
    $vetor_inicializacao = substr(hash('sha256', $chave), 0, 16);

    return openssl_encrypt($dado, $metodo, $chave, 0, $vetor_inicializacao);
}

/**
 * Retorna o dado da criptografia passada
 * @param string $dadoCriptografado
 * @return string $dado
 */
function descriptografar($dadoCriptografado)
{
    $chave = getenv('ENCRYPTION_KEY');
    $metodo = "AES-256-CBC";

    $vetor_inicializacao = substr(hash('sha256', $chave), 0, 16);

    return openssl_decrypt($dadoCriptografado, $metodo, $chave, 0, $vetor_inicializacao);
}

/**
 * Gera um UUID
 * @return string $uuid
 */
function gerarUuid() {
    $uuid = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );

    return $uuid;
}