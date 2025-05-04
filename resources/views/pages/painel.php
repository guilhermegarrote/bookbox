<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookbox</title>

    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="public/js/filtro.js"></script>
    <script src="public/js/logout.js"></script>
    <script src="public/js/menu-top.js"></script>
    <script src="public/js/tabela.js"></script>
    <script src="public/js/modal.js"></script>
    <script src="public/js/mascaras.js"></script>
</body>