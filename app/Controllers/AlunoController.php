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
        $this->AlunoModel = new AlunoModel($db);
        $this->TurmaModel = new TurmaModel($db);
    }

    /**
     * Método responsável por cadastrar aluno.
     * @return void
     */
    public function cadastrar() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = json_decode(file_get_contents("php://input"), true);
    
            $nome = $dados['nome'] ?? null;
            $cpf = $dados['cpf'] ?? null;
            $email = $dados['email'] ?? null;
            $telefone = $dados['telefone'] ?? null;
            $periodo = $dados['periodo'] ?? null;
            $curso = $dados['curso'] ?? null;
    
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
            
            if ($this->AlunoModel->existeCpf($cpf)) {
                $erros['cpf'] = 'Já existe um aluno cadastrado com este CPF.';
                echo json_encode(["erro" => $erros]);
                http_response_code(409);
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
 * Método responsável por editar os dados de um aluno.
 * @return void
 */
public function editar()
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo json_encode(["erro" => "Dados inválidos."]);
        http_response_code(400);
        exit();
    }

    $id = isset($data['id']) ? trim($data['id']) : null;
    $nome = isset($data['nome']) ? trim($data['nome']) : '';
    $cpf = isset($data['cpf']) ? trim($data['cpf']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $telefone = isset($data['telefone']) ? trim($data['telefone']) : '';


    $erros = [];

    if (empty($id)) {
        $erros['id'] = 'O ID do aluno é obrigatório.';
    }

    if (!empty($nome) && !$this->validarEstruturaNome($nome)) {
        $erros['nome'] = 'O nome fornecido não é válido. Por favor, tente novamente.';
    }

    if (!empty($cpf) && !$this->validarEstruturaCpf($cpf)) {
        $erros['cpf'] = 'O CPF fornecido não é válido. Por favor, tente novamente.';
    }

    if (!empty($email) && !$this->validarEstruturaEmail($email)) {
        $erros['email'] = 'O e-mail fornecido não é válido. Por favor, tente novamente.';
    }

    if (!empty($telefone) && !$this->validarEstruturaTelefone($telefone)) {
        $erros['telefone'] = 'O telefone fornecido não é válido. Por favor, tente novamente.';
    }

    if (!empty($erros)) {
        echo json_encode(["erro" => $erros]);
        http_response_code(400);
        exit();
    }

    $atualizacao = $this->AlunoModel->editar($id, $nome, $cpf, $email, $telefone);

    if ($atualizacao) {
        echo json_encode([
            "mensagem" => "Dados do aluno atualizados com sucesso!",
            "redirecionar" => "/bookbox/aluno"
        ]);
        exit();
    } else {
        echo json_encode(["erro" => "Erro ao atualizar os dados do aluno. Tente novamente."]);
        http_response_code(500);
        exit();
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
