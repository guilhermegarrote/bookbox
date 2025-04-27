<?php
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

/**
 * Transforma um uuid binário para seu formato original em texto
 * @param string $uuidBinario
 * @return string $uuid
 */
function binarioParaUuid($uuidBin) {
    $hex = bin2hex($uuidBin);
    return substr($hex, 0, 8) . '-' .
           substr($hex, 8, 4) . '-' .
           substr($hex, 12, 4) . '-' .
           substr($hex, 16, 4) . '-' .
           substr($hex, 20);
}