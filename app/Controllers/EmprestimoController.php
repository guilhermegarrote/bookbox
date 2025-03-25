<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/EmprestimoModel.php';
require_once __DIR__ . '/../Models/AlunoModel.php';
require_once __DIR__ . '/../Models/ExemplarModel.php';
require_once __DIR__ . '/../Utils/utils.php';

class EmprestimoController
{
    private $emprestimoModel;
    private $alunoModel;
    private $exemplarModel;

    /**
     * Construtor da classe, responsável por inicializar os modelos e estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct()
    {
        $db = Database::conectar();
        $this->emprestimoModel = new EmprestimoModel($db);
        $this->alunoModel = new AlunoModel($db);
        $this->exemplarModel = new ExemplarModel($db);
    }

    /**
     * Método responsável por cadastrar um empréstimo.
     * @return void
     */
    public function cadastro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cpf = $_POST['cpf'];
            $exemplarId = $_POST['exemplarId'];
    
            $erros = [];

            if (empty($cpf)) {
                $erros['cpf'] = 'O campo "CPF do Estudante" é obrigatório.';
            }
    
            if (empty($exemplarId)) {
                $erros['exemplarId'] = 'O campo "Exemplar" é obrigatório.';
            }
    
            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }
    
            $alunoId = $this->alunoModel->buscarAlunoPorCpf($cpf);
            if (!$alunoId) {
                $erros['cpf'] = 'O aluno informado não está cadastrado. Verifique e tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }
    
            $exemplarExiste = $this->exemplarModel->buscaExemplar($exemplarId);
            if (!$exemplarExiste) {
                $erros['exemplarId'] = 'O exemplar informado não está cadastrado. Por favor, verifique e tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $quantidadeLivrosEmprestados = $this->emprestimoModel->contarEmprestimosPorAluno($alunoId);
            if ($quantidadeLivrosEmprestados >= 3) {
                echo "Você não pode emprestar mais de 3 livros.";
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }
    
            $cadastroDeuCerto = $this->emprestimoModel->cadastrar($alunoId, $exemplarId);
            if ($cadastroDeuCerto) {
                echo json_encode([
                    "mensagem" => "Empréstimo realizado com sucesso!",
                    "redirecionar" => "/bookbox/emprestimos"
                ]);
                exit();
            } else {
                $erros['geral'] = 'Erro ao cadastrar empréstimo. Tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(500);
                exit();
            }
        } else {
            require_once __DIR__ . '/../resources/views/emprestimos/cadastro.php';
        }
    }
}
