<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/AlunoModel.php';
require_once __DIR__ . '/../Utils/utils.php';

class AlunoController
{
    private $model;

    /**
     * Construtor da classe, responsável por inicializar o modelo de aluno e estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct()
    {
        $this->model = new AlunoModel(Database::conectar());
    }

    /**
     * Método responsável por cadastrar aluno.
     * @return void
     */
    public function cadastro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            $cpf = $_POST['cpf'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];

            if (!$this->validarEstruturaNome($nome)) {
                adicionarMensagemSessao('nome', 'O nome não é valido. Por favor, tente novamente.');
            }

            if (!$this->validarEstruturaCpf($cpf)) {
                adicionarMensagemSessao('cpf', 'O cpf não atende os requisitos. Tente novamentel.');
            }

            if (!$this->validarEstruturaEmail($email)) {
                adicionarMensagemSessao('email', 'O e-mail fornecido não é válido. Por favor, tente novamente.');
            }

            if (!$this->validarEstruturaTelefone($telefone)) {
                adicionarMensagemSessao('telefone', 'O telefone fornecido não é válido. Por favor, tente novamente.');
            }

            if (empty($_SESSION['errors'])) {
                $cadastroDeuCerto = $this->model->cadastrar($nome, $cpf, $email, $telefone);
                if ($cadastroDeuCerto) {
                    exit();
                } else {
                    adicionarMensagemSessao('geral', 'Erro ao cadastrar aluno. Tente novamente.');
                }
            }

            exit();
        } else {
            /*require_once __DIR__ . '/../resources/views'; */
        }
    }

    /**
     * Método responsável por verificar o cadastro.
     * @return void
     */
    public function cadastro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
            $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
        
            if (empty($nome)) {
                adicionarMensagemSessao('nome', 'O nome é obrigatório.');
            }

            if (!$this->validarEstruturaEmail($nome)) {
                adicionarMensagemSessao('nome', 'Nome inválido.');
            }

            if (empty($cpf)) {
                adicionarMensagemSessao('cpf', 'O cpf é obrigatório.');
            }

            if (!$this->validarEstruturaEmail($cpf)) {
                adicionarMensagemSessao('cpf', 'Cpf inválido.');
            }

            if (empty($email)) {
                adicionarMensagemSessao('email', 'O email é obrigatório.');
            }

            if (!$this->validarEstruturaEmail($email)) {
                adicionarMensagemSessao('email', 'Email inválido.');
            }

            if (empty($telefone)) {
                adicionarMensagemSessao('telefone', 'O telefone é obrigatório.');
            }

            if (!$this->validarEstruturaEmail($telefone)) {
                adicionarMensagemSessao('telefone', 'Telefone inválido.');
            }


            exit();
        } else {
           /* require_once __DIR__ . '/../resources/views/'; */
        }
    }

    /**
     * Método responsável por retornar se existem alunos cadastrados no banco.
     * @return boolean
     */
    public function existeAlunos()
    {
        return $this->model->existeAlunos();
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
    private static function validarEstruturaCpf($cpf)
    {
        if (preg_match("/^\d{3}\.\d{3}\.\d{3}-\d{2}$/", trim($cpf))) {
            $palavras = explode(" ", trim($cpf)); {
                return true;
            }
        }

        return false;
    }


    /**
     * Método responsável por verificar se o telefone segue os requisitos mínimos.
     * @param string $telefone
     * @return boolean
     */
    private static function validarEstruturaTelefone($telefone)
    { 
        if (preg_match("/^\(\d{2}\)\s?\d{4,5}-\d{4}$/", trim($telefone))) {
            $palavras = explode(" ", trim($telefone));{
                return true;
            }
        }

        return false;
    }

    /**
     * Método responsável por validar o estrutura do e-mail.
     * @param string $email
     * @return boolean
     */
    private static function validarEstruturaEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            adicionarMensagemSessao('email', 'O e-mail fornecido tem formato inválido.');
            return false;
        }
        return true;
    }

}


