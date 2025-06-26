<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Controllers/UsuarioController.php';
require_once __DIR__ . '/../app/Controllers/TurmaController.php';
require_once __DIR__ . '/../app/Controllers/GeneroController.php';
require_once __DIR__ . '/../app/Controllers/AlunoController.php';
require_once __DIR__ . '/../app/Controllers/ExemplarController.php';
require_once __DIR__ . '/../app/Controllers/LivroController.php';

use App\Middleware\JwtMiddleware;

$usuarioController = new UsuarioController();
$turmaController = new TurmaController();
$generoController = new GeneroController();
$alunoController = new AlunoController();
$exemplarController = new ExemplarController();
$livroController = new LivroController();
$caminhoBase = '/bookbox';

$uriRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$caminho = str_replace($caminhoBase, '', $uriRequisicao);
$metodo = $_SERVER['REQUEST_METHOD'];

rotear($caminho, $metodo, $usuarioController, $turmaController, $generoController, $alunoController, $exemplarController, $livroController);

function rotear($caminho, $metodo, $usuarioController, $turmaController, $generoController, $alunoController, $exemplarController, $livroController)
{
    $id = null;
    if ($metodo === 'PATCH' || $metodo === 'DELETE') {
        $entidades = "turmas|generos|alunos";
        $acoes = "editar|excluir";

        $regex = '#^(/api/(' . $entidades . ')/(' . $acoes . '))/([0-9a-fA-F]{32})$#';

        if (preg_match($regex, $caminho, $matches)) {
            $rotaSemId = $matches[1];
            $id = $matches[4];
            $caminho = $rotaSemId;
        }
    }

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
                // exigeAutenticacao();
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
            '/modals/cadastro_exemplar' => fn() => carregarPagina('modals/exemplares/cadastro_exemplar'),
            '/modals/configuracao' => fn() => carregarPagina('modals/configuracao'),
            '/popups/filtro' => fn() => carregarPagina('popups/filtro'),
            '/popups/genero_nao_cadastrado' => fn() => carregarPagina('popups/genero_nao_cadastrado'),
            '/popups/confirmacao_finalizacao_emprestimo' => fn() => carregarPagina('popups/confirmacao_finalizacao_emprestimo'),
            '/popups/confirmacao_exclusao_aluno' => fn() => carregarPagina('popups/confirmacao_exclusao_aluno'),
            '/pages/redefinicao-senha/email.php' => fn() => carregarPagina('pages/redefinicao-senha/email'),
            '/pages/redefinicao-senha/codigo.php' => fn() => carregarPagina('pages/redefinicao-senha/codigo'),
            '/pages/redefinicao-senha/nova-senha.php' => fn() => carregarPagina('pages/redefinicao-senha/nova-senha'),
            '/popups/filtro_emprestimo' => fn() => carregarPagina('popups/filtroEmprestimo'),
            '/popups/filtro_aluno' => fn() => carregarPagina('popups/filtroAluno'),

        ],
        'POST' => [
            '/api/login' => function () use ($usuarioController) {
                $usuarioController->login();
            },
            '/api/usuarios/cadastrar' => fn() => $usuarioController->cadastro(),
            '/api/turmas/cadastrar' => fn() => $turmaController->cadastrar(),
            '/api/generos/cadastrar' => fn() => $generoController->cadastrar(),
            '/api/alunos/cadastrar' => fn() => $alunoController->cadastrar(),
            '/api/exemplares/cadastrar' => fn() => $exemplarController->cadastrar(),
            '/api/livros/cadastrar' => fn() => $livroController->cadastrar(),
            '/logout' => fn() => session_destroy(),
        ],
        'PUT' => [],
        'PATCH' => [
            '/api/turmas/editar' => function () use ($turmaController, $id) {
                $turmaController->editar($id);
            },
            '/api/generos/editar' => function () use ($generoController, $id) {
                $generoController->editar($id);
            },
            '/api/alunos/editar' => function () use ($alunoController, $id) {
                $alunoController->editar($id);
            },
            '/api/livros/editar' => function () use ($livroController, $id) {
                $livroController->editar($id);
            }  
        ],
        'DELETE' => [
            '/api/turmas/excluir' => function () use ($turmaController, $id) {
                $turmaController->excluir($id);
            },
            '/api/alunos/excluir' => function () use ($alunoController, $id) {
                $alunoController->excluir($id);
            },
            '/api/livros/excluir' => function () use ($livroController, $id) {
                $livroController->excluir($id);
            }
        ]
    ];

    if (isset($rotas[$metodo][$caminho])) {
        $rotas[$metodo][$caminho]();
    } else {
        carregarPagina('pages/404'); 
        exit();
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
