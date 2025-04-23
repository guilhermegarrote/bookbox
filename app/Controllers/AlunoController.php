<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/AlunoModel.php';
require_once __DIR__ . '/../Models/TurmaModel.php';
require_once __DIR__ . '/../Utils/utils.php';

class AlunoController
{
    private $AlunoModel;
    private $TurmaModel;

    /**
     * Construtor da classe, responsável por inicializar o modelo de aluno e estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct()
    {
        $db = Database::conectar();
        $this->TurmaModel = new AlunoModel($db);
        $this->TurmaModel = new TurmaModel($db);
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
            $periodo = $_POST['periodo'];
            $curso = $_POST['curso'];

            $erros = [];

            if (empty($nome)) {
                $erros['nome'] = 'O nome é obrigatório.';
            } elseif (!$this->validarEstruturaNome($nome)) {
                $erros['nome'] = 'O nome fornecido não é válido. Por favor, tente novamente.';
            }

           if (empty($cpf)) {
                $erros['cpf'] = 'O cpf é obrigatório.';
             }elseif (!$this->validarEstruturaCpf($cpf)) {
                $erros['cpf'] = 'O CPF fornecido não é válido. Por favor, tente novamente.';
            }

            if (empty($email)) {
                $erros['email'] = 'O email é obrigatório.';
            }elseif (!$this->validarEstruturaEmail($email)) {
                $erros['email'] = 'O e-mail fornecido não é válido. Por favor, tente novamente.';
            }

            if (empty($telefone)) {
                $erros['telefone'] = 'O telefone é obrigatório.';
            }elseif (!$this->validarEstruturaTelefone($telefone)) {
                $erros['telefone'] = 'O telefone fornecido não é válido. Por favor, tente novamente.';
            }

            if (empty($periodo)) {
                $erros['periodo'] = 'O período é obrigatório.';
            }

            if (empty($curso)) {
                $erros['curso'] = 'O curso é obrigatório.';
            }

            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $cursoExiste = $this->TurmaModel->buscaCurso($curso);
            if (!$cursoExiste) {
                $erros['curso'] = 'O curso informado não está cadastrado. Por favor, verifique e tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $cadastroDeuCerto = $this->AlunoModel->cadastrar($nome, $cpf, $email, $telefone, $periodo, $curso);
            if ($cadastroDeuCerto) {
                echo json_encode([
                    "mensagem" => "Aluno cadastrado com sucesso!",
                    "redirecionar" => "/bookbox/alunos"
                ]);
                exit();
            } else {
                $erros['geral'] = 'Erro ao cadastrar aluno. Tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(500);
                exit();
            }
        }
    } 

    /**
     * Método responsável por editar aluno.
     * @return void
     */
    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nome = $_POST['nome'] ?? null;
            $cpf = $_POST['cpf'] ?? null;
            $email = $_POST['email'] ?? null;
            $telefone = $_POST['telefone'] ?? null;
            $periodo = $_POST['periodo'] ?? null;
            $curso = $_POST['curso'] ?? null;

            $erros = [];

            if (empty($id)) {
                $erros['id'] = 'O ID do aluno é obrigatório.';
            }

            if ($nome !== null && !$this->validarEstruturaNome($nome)) {
                $erros['nome'] = 'O nome fornecido não é válido. Por favor, tente novamente.';
            }

            if ($cpf !== null && !$this->validarEstruturaCpf($cpf)) {
                $erros['cpf'] = 'O CPF fornecido não é válido. Por favor, tente novamente.';
            }

            if ($email !== null && !$this->validarEstruturaEmail($email)) {
                $erros['email'] = 'O e-mail fornecido não é válido. Por favor, tente novamente.';
            }

            if ($telefone !== null && !$this->validarEstruturaTelefone($telefone)) {
                $erros['telefone'] = 'O telefone fornecido não é válido. Por favor, tente novamente.';
            }

            if ($periodo !== null && !$this->$periodo) {
                $erros['período'] = 'O período fornecido não é válido. Por favor, tente novamente.';
            }

            if ($curso !== null && !$this->$curso) {
                $erros['curso'] = 'O curso fornecido não é válido. Por favor, tente novamente.';
            }

            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $sucesso = $this->AlunoModel->editar($nome, $cpf, $email, $telefone, $periodo, $curso);
            if ($sucesso) {
                echo json_encode([
                    "mensagem" => "Dados do aluno atualizados com sucesso!",
                    "redirecionar" => "/bookbox/aluno"
                ]);
                exit();
            } else {
                $erros['geral'] = 'Erro ao atualizar dados do aluno. Tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(500);
                exit();
            }
        }
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
            return true;
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
            return true;
        }

        return false;
    }

    /**
     * Método responsável por validar a estrutura do e-mail.
     * @param string $email
     * @return boolean
     */
    private static function validarEstruturaEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

}
