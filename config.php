<?php
define('APP_RUNNING', true);

require_once __DIR__ . '/app/Utils/utils.php';

function carregarVariaveisDeAmbiente($caminho = __DIR__ . '/.env')
{
    if (!file_exists($caminho)) {
        throw new Exception("Arquivo .env não encontrado: $caminho");
    }

    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($linhas as $linha) {
        if (strpos(trim($linha), '#') === 0 || strpos($linha, '=') === false) {
            continue;
        }

        list($chave, $valor) = array_map('trim', explode('=', $linha, 2));

        putenv("$chave=$valor");
        $_ENV[$chave] = $valor;
    }
}

carregarVariaveisDeAmbiente();
