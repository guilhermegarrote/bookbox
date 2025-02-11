<?php
require_once __DIR__ . '/layouts/menutop.php';

if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    // header("Location: /Bookbox/public/login.php");
    exit();
}

$page = $_GET['page'] ?? 'emprestimos';

switch ($page) {
    case 'emprestimos':
        require_once 'pages/emprestimos.php';
        break;
    case 'alunos':
        require_once 'pages/alunos.php';
        break;
    case 'livros':
        require_once 'pages/livros.php';
        break;
    default:
        echo "<h2>Página não encontrada</h2>";
        break;
}
?>