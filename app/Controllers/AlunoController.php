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
    public function validaCamposInvalidos() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            $cpf = $_POST['cpf'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];

            $erros = [];

            if (!$this->validarEstruturaNome($nome)) {
                $erros['nome'] = 'O nome fornecido não é válido. Por favor, tente novamente.';
            }

            if (!$this->validarEstruturaCpf($cpf)) {
                $erros['cpf'] = 'O CPF fornecido não é válido. Por favor, tente novamente.';
            }

            if (!$this->validarEstruturaEmail($email)) {
                $erros['email'] = 'O e-mail fornecido não é válido. Por favor, tente novamente.';
            }

            if (!$this->validarEstruturaTelefone($telefone)) {
                $erros['telefone'] = 'O telefone fornecido não é válido. Por favor, tente novamente.';
            }

            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $cadastroDeuCerto = $this->model->cadastrar($nome, $cpf, $email, $telefone);
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
        } else {
            require_once __DIR__ . '/../resources/views/alunos/cadastro.php';
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
