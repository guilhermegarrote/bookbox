<?php

session_start();

// $usuario_valido = true;

if ($usuario_valido) {
    $_SESSION['usuario_logado'] = true;
    header("Location: dashboard.php");
    exit();
} else {
    echo "Usuário ou senha incorretos!";
}