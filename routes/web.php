<?php
require_once __DIR__ . '/../app/Controllers/UsuarioController.php';
require_once __DIR__ . '/../app/Controllers/TurmaController.php';
require_once __DIR__ . '/../app/Controllers/GeneroController.php';
require_once __DIR__ . '/../app/Controllers/AlunoController.php';

$usuarioController = new UsuarioController();
$turmaController = new TurmaController();
$generoController = new GeneroController();
$alunoController = new AlunoController();
$caminhoBase = '/bookbox';

$uriRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$caminho = str_replace($caminhoBase, '', $uriRequisicao);
$metodo = $_SERVER['REQUEST_METHOD'];

rotear($caminho, $metodo, $usuarioController, $turmaController, $generoController, $alunoController);

function rotear($caminho, $metodo, $usuarioController, $turmaController, $generoController, $alunoController)
{
    $rotas = [
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
            '/modals/cadastro_emprestimo' => fn() => carregarPagina('modals/emprestimos/cadastro_emprestimo'),
            '/modals/cadastro_aluno' => fn() => carregarPagina('modals/alunos/cadastro_aluno'),
            '/modals/cadastro_livro' => fn() => carregarPagina('modals/livros/cadastro_livro'),
            '/modals/cadastro_genero' => fn() => carregarPagina('modals/generos/cadastro_genero'),
            '/modals/cadastro_turma' => fn() => carregarPagina('modals/turmas/cadastro_turma'),
            '/popups/filtro' => fn() => carregarPagina('popups/filtro'),
            '/popups/genero_nao_cadastrado' => fn() => carregarPagina('popups/genero_nao_cadastrado'),
            '/popups/confirmar_finalizacao_emprestimo' => fn() => carregarPagina('popups/confirmar_finalizacao_emprestimo'),
            '/popups/confirmacao_exclusao_aluno' => fn() => carregarPagina('popups/confirmacao_exclusao_aluno'),
        ],
        'POST' => [
            '/api/login' => fn() => $usuarioController->login(),
            '/api/usuarios/cadastrar' => fn() => $usuarioController->cadastro(),
            '/api/turmas/cadastrar' => fn() => $turmaController->cadastrar(),
            '/api/generos/cadastrar' => fn() => $generoController->cadastrar(),
            '/api/alunos/cadastrar' => fn() => $alunoController->cadastrar(),
            '/logout' => fn() => session_destroy(),
        ],
        'PUT' => [],
        'PATCH' => [
            '/api/turmas/editar' => fn() => $turmaController->editar(),
            '/api/generos/editar' => fn() => $generoController->editar(),
            '/api/alunos/editar' => fn() => $alunoController->editar()
        ],
        'DELETE' => []
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
