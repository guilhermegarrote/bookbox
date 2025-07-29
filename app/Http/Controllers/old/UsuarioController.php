<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/UsuarioModel.php';
require_once __DIR__ . '/../Utils/utils.php';

use App\Helpers\JwtHelper;

class UsuarioController
{
    private $model;

    /**
     * Construtor da classe, responsável por inicializar o modelo de usuário e estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct()
    {
        $this->model = new UsuarioModel(Database::conectar());
    }

    /**
     * Método responsável por cadastrar usuario.
     * @return void
     */
    public function cadastro()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(["erro" => "Dados inválidos."]);
            http_response_code(400);
            exit();
        }

        $nome = isset($data['nome']) ? trim($data['nome']) : '';
        $email = isset($data['email']) ? trim($data['email']) : '';
        $senha = isset($data['senha']) ? $data['senha'] : '';
        $senhaConfirmada = isset($data['senhaConfirmada']) ? $data['senhaConfirmada'] : '';

        $erros = [];

        if (!$this->validarEstruturaNome($nome)) {
            $erros['nome'] = 'O nome não é válido. Por favor, tente novamente.';
        }

        if (!$this->validarEstruturaSenha($senha) && !$this->validarEstruturaSenha($senhaConfirmada)) {
            $erros['senha'] = 'A senha deve ter de 8 a 16 caracteres, no mínimo 1 número, 1 caractere especial e não deve conter espaços.';
        }

        if ($senha !== $senhaConfirmada) {
            $erros['senhaConfirmada'] = 'As senhas não coincidem. Por favor, tente novamente.';
        }

        if (!$this->validarEstruturaEmail($email)) {
            $erros['email'] = 'O e-mail fornecido não é válido. Por favor, tente novamente.';
        }

        if (!empty($erros)) {
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        $cadastroDeuCerto = $this->model->cadastrar($nome, $email, $senha);

        if ($cadastroDeuCerto) {
            echo json_encode([
                "mensagem" => "Cadastro realizado com sucesso!",
                "redirecionar" => "/bookbox/login"
            ]);
            exit();
        } else {
            echo json_encode(["erro" => "Erro ao cadastrar usuário. Tente novamente."]);
            http_response_code(500);
            exit();
        }
    }

    public function redefinirSenha() {}

    /**
     * Método responsável por verificar o login.
     * @return void
     */
    public function login()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(["erro" => "Dados inválidos."]);
            http_response_code(400);
            exit();
        }

        $email = isset($data['email']) ? trim($data['email']) : '';
        $senha = isset($data['senha']) ? $data['senha'] : '';

        $erros = [];

        if (empty($email)) {
            $erros['email'] = 'O email é obrigatório.';
        } elseif (!$this->validarEstruturaEmail($email)) {
            $erros['email'] = 'Email inválido.';
        }

        if (empty($senha)) {
            $erros['senha'] = 'A senha é obrigatória.';
        }

        if (!empty($erros)) {
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        $usuario = $this->model->verificarLogin($email, $senha);

        if ($usuario) {
            $token = JwtHelper::criarToken(bin2hex($usuario['usuId']));

            $_SESSION['usuario_id'] = bin2hex($usuario['usuId']);

            echo json_encode([
                "token" => $token,
                "redirecionar" => "/bookbox/painel"
            ]);
            exit();
        } else {
            echo json_encode(["erro" => "Email ou senha incorretos."]);
            http_response_code(401);
            exit();
        }
    }

    /**
     * Método responsável por retornar se existem usuários cadastrados no banco.
     * @return boolean
     */
    public function existeUsuarios()
    {
        return $this->model->existeUsuarios();
    }

    /**
     * Método responsável por editar usuário.
     * @return void
     */
    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nome = $_POST['nome'] ?? null;
            $email = $_POST['email'] ?? null;
            $senha = $_POST['senha'] ?? null;
            $senhaConfirmada = $_POST['senhaConfirmada'] ?? null;

            $erros = [];

            if (empty($id)) {
                $erros['id'] = 'O ID do usuário é obrigatório.';
            }

            if ($nome !== null && !$this->validarEstruturaNome($nome)) {
                $erros['nome'] = 'O nome fornecido não é válido. Por favor, tente novamente.';
            }

            if ($email !== null && !$this->validarEstruturaEmail($email)) {
                $erros['email'] = 'O e-mail fornecido não é válido. Por favor, tente novamente.';
            }

            if ($senha !== null && !$this->validarEstruturaSenha($senha)) {
                $erros['senha'] = 'A senha deve ter de 8 a 16 caracteres, conter no mínimo 1 número, 1 caractere especial, e não deve conter espaços.';
            }

            if ($senha !== null && $senhaConfirmada !== null && $senha !== $senhaConfirmada) {
                $erros['senhaConfirmada'] = 'As senhas não são iguais. Por favor, tente novamente.';
            }

            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $sucesso = $this->model->editar($id, $nome, $email, $senha);

            if ($sucesso) {
                echo json_encode([
                    "mensagem" => "Dados do usuário atualizados com sucesso!",
                    "redirecionar" => "/bookbox/usuarios"
                ]);
                exit();
            } else {
                $erros['geral'] = 'Erro ao atualizar dados do usuário. Tente novamente.';
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
     * Método responsável por validar o estrutura do e-mail.
     * @param string $email
     * @return boolean
     */
    private static function validarEstruturaEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Método responsável por verificar se a estrutura da senha segue os requisitos mínimos.
     * @param string $senha
     * @return boolean
     */
    private static function validarEstruturaSenha($senha)
    {
        if (
            preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&(),.?\":{}|<>])[A-Za-z\d!@#$%^&(),.?\":{}|<>]{8,16}$/", $senha)
        ) {
            return true;
        }
        return false;
    }
}
