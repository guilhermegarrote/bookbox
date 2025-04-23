<?php
require_once __DIR__ . '/../app/Controllers/UsuarioController.php';

$usuarioController = new UsuarioController();
$caminhoBase = '/bookbox';

$uriRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$caminho = str_replace($caminhoBase, '', $uriRequisicao);
$metodo = $_SERVER['REQUEST_METHOD'];

rotear($caminho, $metodo, $usuarioController);

function rotear($caminho, $metodo, $usuarioController)
{
    $rotas = [
        'POST' => [
            '/api/usuarios' => fn() => $usuarioController->cadastro(),
            '/api/login' => fn() => $usuarioController->login(),
            '/logout' => fn() => session_destroy(),
        ],
        'GET' => [
            '/login' => function () use ($usuarioController) {
                if (!$usuarioController->existeUsuarios()) {
                    redirecionar('/bookbox/cadastro');
                }
                if (!empty($_SESSION['usuario_logado'])) {
                    redirecionar('/bookbox/painel');
                }
                carregarPagina('pages/login');
            },
            '/' => function () {
                exigeAutenticacao();
                carregarPagina('pages/painel');
            },
            '/painel' => function () {
                exigeAutenticacao();
                carregarPagina('pages/painel');
            },
            '/cadastro' => function () use ($usuarioController) {
                if ($usuarioController->existeUsuarios()) {
                    redirecionar('/bookbox/login');
                }
                carregarPagina('pages/cadastro');
            },
            '/modals/cadastro_emprestimo' => fn() => carregarPagina('modals/cadastro_emprestimo'),
            '/modals/cadastro_aluno' => fn() => carregarPagina('modals/cadastro_aluno'),
            '/modals/cadastro_livro' => fn() => carregarPagina('modals/cadastro_livro'),
        ]
    ];

    if (isset($rotas[$metodo][$caminho])) {
        $rotas[$metodo][$caminho]();
    } else {
        http_response_code(404);
        echo json_encode(["erro" => "Página não encontrada"]);
    }
}

function redirecionar(string $destino)
{
    header("Location: $destino");
    exit();
}

function carregarPagina(string $caminhoView)
{
    require_once __DIR__ . "/../resources/views/$caminhoView.php";
    exit();
}

function exigeAutenticacao()
{
    if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
        redirecionar('/bookbox/login');
    }
}
