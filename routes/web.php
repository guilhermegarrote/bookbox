<?php

require_once __DIR__ . '/../app/Controllers/HomeController.php';

$rota = $_GET['url'] ?? 'home';

if ($rota === 'home') {
    $controller = new HomeController();
    $controller->index();
} else {
    echo "Página não encontrada!";
}