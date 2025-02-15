<?php
require_once __DIR__ . '/../app/Controllers/UsuarioController.php';

$usuarioController = new UsuarioController();

$basePath = '/Bookbox';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = str_replace($basePath, '', $requestUri);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $url === '/login') {
    $usuarioController->login();
    exit();
}

switch ($url) {
    case '/login':
        require_once __DIR__ . '/../resources/views/pages/login.php';
        break;
    case '/':
    case '/painel':
        if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            header("Location: /Bookbox/login");
            exit();
        }
        require_once __DIR__ . '/../resources/views/pages/painel.php';
        break;
    case '/logout':
        session_destroy();
        header("Location: /Bookbox/login");
        exit();
    default:
        http_response_code(404);
        echo "Página não encontrada";
        break;
}
?>