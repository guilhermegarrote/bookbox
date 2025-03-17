<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookbox</title>

    <link rel="stylesheet" href="public/css/style.css">
</head>

<?php
require_once __DIR__ . '/../layouts/menutop.php';

$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'emprestimos';

switch ($pagina) {
    case 'alunos':
        require_once 'alunos.php';
        break;
    case 'livros':
        require_once 'livros.php';
        break;
    case 'emprestimos':
    default:
        require_once 'emprestimos.php';
        break;
}
?>

<body>
    <div id="painel"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="public/js/painel.js"></script>
</body>