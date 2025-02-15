<?php
require_once __DIR__ . '/../layouts/menutop.php';

$page = $_GET['page'] ?? '';

switch ($page) {
    case 'alunos':
        require_once 'alunos.php';
        break;
    case 'livros':
        require_once 'livros.php';
        break;
    default:
        require_once 'emprestimos.php';
        break;
}
?>