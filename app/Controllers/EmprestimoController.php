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

            if (empty($cpf)) {
                adicionarMensagemSessao('cpf', 'O campo "CPF do Estudante" é obrigatório.');
            }

            if (empty($exemplarId)) {
                adicionarMensagemSessao('exemplarId', 'O campo "Exemplar" é obrigatório.');
            }
            if (!empty($_SESSION['errors'])) {
                exit();
            }


            $alunoId = $this->alunoModel->buscaAlunoPorCpf($cpf);
            if (!$alunoId) {
                adicionarMensagemSessao('cpf', 'O aluno informado não está cadastrado. Verifique e tente novamente.');
                exit();
            }

            $exemplarExiste = $this->exemplarModel->buscaExemplar($exemplarId);
            if (!$exemplarExiste) {
                adicionarMensagemSessao('exemplarId', 'O exemplar informado não está cadastrado. Por favor, verifique e tente novamente.');
                exit();
            }
            
            $cadastroDeuCerto = $this->emprestimoModel->cadastrar($alunoId, $exemplarId);
            if ($cadastroDeuCerto) {
                header("Location: /bookbox/emprestimos");
                exit();
            } else {
                adicionarMensagemSessao('geral', 'Erro ao cadastrar empréstimo. Tente novamente.');
                exit();
            }
        } else {
            require_once __DIR__ . '/../resources/views/emprestimos/cadastro.php';
        }
    }
}
