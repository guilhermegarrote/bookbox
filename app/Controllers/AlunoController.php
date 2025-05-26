<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Controllers/TurmaController.php';
require_once __DIR__ . '/../Models/AlunoModel.php';
require_once __DIR__ . '/../Models/AlunoTurmaModel.php';
require_once __DIR__ . '/../Models/EmprestimoModel.php';
require_once __DIR__ . '/../Models/TurmaModel.php';
require_once __DIR__ . '/../Utils/utils.php';

class AlunoController
{
    private $alunoModel;
    private $alunoTurmaModel;
    private $emprestimoModel;
    private $turmaModel;
    private $turmaController;

    /**
     * Construtor da classe, responsável por inicializar o model de aluno e estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct()
    {
        $db = Database::conectar();
        $this->alunoModel = new alunoModel($db);
        $this->alunoTurmaModel = new AlunoTurmaModel($db);
        $this->emprestimoModel = new EmprestimoModel($db);
        $this->turmaModel = new TurmaModel($db);
        $this->turmaController = new TurmaController();
    }

    /**
     * Método responsável por cadastrar aluno.
     * @return void
     */
    public function cadastrar()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(["erro" => "Não foi possível processar os dados enviados. Verifique o formato da requisição."]);
            http_response_code(400);
            exit();
        }

        $nome = trim($data['nome'] ?? '');
        $cpf = preg_replace('/\D/', '', $data['cpf'] ?? '');
        $email = trim($data['email'] ?? '');
        $telefone = preg_replace('/\D/', '', $data['telefone'] ?? '');
        $curso = trim($data['curso'] ?? '');
        $regime = trim($data['regime'] ?? '');
        $periodo = preg_replace('/\D/', '', $data['periodo'] ?? '');

        $erros = [];
        if (!$this->validarEstruturaNome($nome)) {
            $erros['nome'] = 'O nome informado é inválido. Certifique-se de incluir nome e sobrenome.';
        }

        if (!$this->validarCpf($cpf)) {
            $erros['cpf'] = 'O CPF informado é inválido. Verifique e tente novamente.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'O e-mail informado é inválido. Verifique o formato (ex: nome@dominio.com).';
        }

        if (strlen($telefone) != 11) {
            $erros['telefone'] = 'O telefone deve conter 11 dígitos, incluindo DDD. Ex: 11999998888.';
        }

        if (!$this->turmaController->validarEstruturaCurso($curso)) {
            $erros['curso'] = 'O curso informado não foi reconhecido. Verifique a lista de cursos disponíveis.';
        }

        $REGIMES_VALIDOS = ['Anual', 'Semestral'];
        if (!in_array($regime, $REGIMES_VALIDOS)) {
            $erros['regime'] = 'Regime inválido. Os valores aceitos são: Anual ou Semestral.';
        }

        if ($periodo <= 0 || $periodo > 20) {
            $erros['periodo'] = 'O período informado deve ser um número entre 1 e 20.';
        }

        if (!empty($erros)) {
            http_response_code(400);
            echo json_encode(["erro" => $erros]);
            exit();
        }

        if ($this->alunoModel->buscarAluno(null, null, $cpf, null, null)) {
            http_response_code(409);
            echo json_encode(["erro" => ["cpf" => "Este CPF já está vinculado a outro aluno."]]);
            exit();
        }

        if ($this->alunoModel->buscarAluno(null, null, null, $email, null)) {
            http_response_code(409);
            echo json_encode(["erro" => ["email" => "Este e-mail já está vinculado a outro aluno."]]);
            exit();
        }

        if ($this->alunoModel->buscarAluno(null, null, null, null, $telefone)) {
            http_response_code(409);
            echo json_encode(["erro" => ["telefone" => "Este telefone já está vinculado a outro aluno."]]);
            exit();
        }

        $turma = $this->turmaModel->buscarTurma(null, $curso, $regime, null, null, $periodo);
        if (!$turma) {
            http_response_code(409);
            echo json_encode(["erro" => ["turma" => "Não foi possível localizar a turma com os dados fornecidos (curso, regime, período)."]]);
            exit();
        }

        if ($this->alunoModel->cadastrar($nome, $cpf, $email, $telefone)) {
            $aluno = $this->alunoModel->buscarAluno(null, null, $cpf, null, null);

            if ($this->alunoTurmaModel->cadastrar($aluno['aluId'], $turma['turId'])) {
                http_response_code(201);
                exit();
            } else {
                $this->alunoModel->excluir($aluno['aluId']);

                http_response_code(500);
                echo json_encode(["erro" => "Ocorreu um erro interno ao cadastrar o aluno. Tente novamente mais tarde."]);
                exit();
            }
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Ocorreu um erro interno ao cadastrar o aluno. Tente novamente mais tarde."]);
            exit();
        }
    }

    /**
     * Método responsável por editar os dados de um aluno.
     * @param id $string
     * @return void
     */
    public function editar($id)
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(["erro" => "Não foi possível processar os dados enviados. Verifique o formato da requisição."]);
            http_response_code(400);
            exit();
        }

        $id =  converterUuidHexParaBinario($id ?? '');
        $nome = trim($data['nome'] ?? '');
        $cpf = preg_replace('/\D/', '', $data['cpf'] ?? '');
        $email = trim($data['email'] ?? '');
        $telefone = preg_replace('/\D/', '', $data['telefone'] ?? '');
        $curso = trim($data['curso'] ?? '');
        $regime = trim($data['regime'] ?? '');
        $periodo = preg_replace('/\D/', '', $data['periodo'] ?? '');

        $erros = [];
        if (empty($id)) {
            $erros['id'] = 'O ID do aluno é obrigatório.';
        }

        if (!empty($nome) && !$this->validarEstruturaNome($nome)) {
            $erros['nome'] = 'O nome informado é inválido. Certifique-se de incluir nome e sobrenome.';
        }

        if (!empty($cpf) && !$this->validarCpf($cpf)) {
            $erros['cpf'] = 'O CPF informado é inválido. Verifique e tente novamente.';
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'O e-mail informado é inválido. Verifique o formato (ex: nome@dominio.com).';
        }

        if (!empty($telefone) && strlen($telefone) != 11) {
            $erros['telefone'] = 'O telefone deve conter 11 dígitos, incluindo DDD. Ex: 11999998888.';
        }

        if (!empty($curso) && !$this->turmaController->validarEstruturaCurso($curso)) {
            $erros['curso'] = 'O curso informado não foi reconhecido. Verifique a lista de cursos disponíveis.';
        }

        $REGIMES_VALIDOS = ['Anual', 'Semestral'];
        if (!empty($regime) && !in_array($regime, $REGIMES_VALIDOS)) {
            $erros['regime'] = 'Regime inválido. Os valores aceitos são: Anual ou Semestral.';
        }

        if (!empty($periodo) && ($periodo <= 0 || $periodo > 20)) {
            $erros['periodo'] = 'O período informado deve ser um número entre 1 e 20.';
        }

        if (empty(array_filter([$nome, $cpf, $email, $telefone, $curso, $regime, $periodo]))) {
            $erros['geral'] = 'Nenhum dado foi fornecido para atualização.';
        }

        if (!empty($erros)) {
            http_response_code(400);
            echo json_encode(["erro" => $erros]);
            exit();
        }

        $alunoTurma = $this->alunoTurmaModel->buscarAlunoTurma(null, $id, null);
        if (!$alunoTurma) {
            http_response_code(404);
            echo json_encode(["erro" => ["id" => "Aluno não encontrado."]]);
            exit();
        }

        if (!empty($cpf) && $this->alunoModel->buscarAluno(null, null, $cpf, null, null)) {
            http_response_code(409);
            echo json_encode(["erro" => ["cpf" => "Já existe um aluno cadastrado com este CPF."]]);
            exit();
        }

        if (!empty($email) && $this->alunoModel->buscarAluno(null, null, null, $email, null)) {
            http_response_code(409);
            echo json_encode(["erro" => ["email" => "Já existe um aluno cadastrado com este email."]]);
            exit();
        }

        if (!empty($telefone) && $this->alunoModel->buscarAluno(null, null, null, null, $telefone)) {
            http_response_code(409);
            echo json_encode(["erro" => ["telefone" => "Já existe um aluno cadastrado com este telefone."]]);
            exit();
        }

        $turmaAtualizada = false;

        if ($curso || $regime || $periodo) {
            if ($curso && $regime && $periodo) {
                $turma = $this->turmaModel->buscarTurma(null, $curso, $regime, null, null, $periodo);
                if ($turma) {
                    if ($alunoTurma && $alunoTurma['fkTurId'] != $turma['turId']) {
                        $turmaAtualizada = $this->alunoTurmaModel->editar($alunoTurma['aluTurId'], $turma['turId']);
                    }
                } else {
                    echo json_encode(["erro" => ["turma" => "A turma informada não foi encontrada."]]);
                    http_response_code(409);
                    exit();
                }
            } else {
                echo json_encode(["erro" => ["turma" => "Para alterar a turma, informe curso, regime e período."]]);
                http_response_code(400);
                exit();
            }
        }

        $dadosAtualizados = ($nome || $cpf || $email || $telefone);
        $alunoAtualizado = false;

        if ($dadosAtualizados) {
            $alunoAtualizado = $this->alunoModel->editar($id, $nome, $cpf, $email, $telefone);
            if (!$alunoAtualizado) {
                echo json_encode(["erro" => "Erro ao atualizar os dados do aluno. Tente novamente."]);
                http_response_code(500);
                exit();
            }
        }

        if ($alunoAtualizado || $turmaAtualizada) {
            http_response_code(204);
            exit();
        }

        http_response_code(400);
        echo json_encode(["erro" => "Nenhuma alteração foi realizada."]);
        exit();
    }

    /**
     * Método responsável por excluir aluno.
     * @param string $id
     * @return void
     */
    public function excluir($id)
    {
        $id = converterUuidHexParaBinario($id ?? '');

        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["erro" => ["id" => "O ID do aluno é obrigatório."]]);
            return;
        }

        if (!$this->alunoModel->buscarAluno($id, null, null, null, null)) {
            http_response_code(404);
            echo json_encode(["erro" => ["id" => "Aluno não encontrado."]]);
            return;
        }

        if ($this->emprestimoModel->buscarEmprestimo(null, $id, null, null, null, null, true)) {
            http_response_code(409);
            echo json_encode(["erro" => ["emprestimo" => "Aluno está com emprestimos ativos."]]);
            return;
        }

        if ($this->alunoModel->excluir($id)) {
            http_response_code(204);
            return;
        }

        http_response_code(500);
        echo json_encode(["erro" => "Erro ao excluir o aluno."]);
    }

    /**
     * Método responsável por verificar se o nome segue os requisitos mínimos.
     * @param string $nome
     * @return boolean
     */
    private static function validarEstruturaNome($nome)
    {
        if (preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/", trim($nome))) {
            $palavras = explode(" ", trim($nome));
            if (count($palavras) >= 2 && strlen($nome) >= 3 && strlen($nome) <= 100) {
                return true;
            }
        }

        return false;
    }

    /**
     * Método responsável por verificar se o cpf segue os requisitos mínimos.
     * @param string $cpf
     * @return boolean
     */
    private static function validarCpf($cpf)
    {
        if (strlen($cpf) != 11) return false;

        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += $cpf[$i] * (($t + 1) - $i);
            }
            $resto = ($soma * 10) % 11;
            if ($resto == 10) $resto = 0;
            if ($resto != $cpf[$t]) return false;
        }

        return true;
    }
}