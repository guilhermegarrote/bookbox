<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/UsuarioModel.php';
require_once __DIR__ . '/../Utils/utils.php';

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $senhaConfirmada = $_POST['senhaConfirmada'];

            if (!$this->validarEstruturaNome($nome)) {
                adicionarMensagemSessao('nome', 'O nome não é valido. Por favor, tente novamente.');
            }

            if (!$this->validarEstruturaSenha($senha)) {
                adicionarMensagemSessao('senha', 'A senha não atende os requisitos. Deve ter de 8 a 16 caracteres, no minimo 1 numero, 1 caracter especial, não deve conter espaços em branco.');
            }

            if ($senha !== $senhaConfirmada) {
                adicionarMensagemSessao('senha', 'As senhas não coincidem. Por favor, tente novamente.');
            }

            if (!$this->validarEstruturaEmail($email)) {
                adicionarMensagemSessao('email', 'O e-mail fornecido não é válido. Por favor, tente novamente.');
            }

            if (empty($_SESSION['errors'])) {
                $cadastroDeuCerto = $this->model->cadastrar($nome, $email, $senha);
                if ($cadastroDeuCerto) {
                    header("Location: /bookbox/login");
                    exit();
                } else {
                    adicionarMensagemSessao('geral', 'Erro ao cadastrar usuário. Tente novamente.');
                }
            }

            header("Location: /bookbox/cadastro");
            exit();
        } else {
            require_once __DIR__ . '/../resources/views/usuarios/cadastro.php';
        }
    }

    /**
     * Método responsável por verificar o login.
     * @return void
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

            if (empty($email)) {
                adicionarMensagemSessao('email', 'O email é obrigatório.');
            }

            if (!$this->validarEstruturaEmail($email)) {
                adicionarMensagemSessao('email', 'Email inválido.');
            }

            if (empty($senha)) {
                adicionarMensagemSessao('senha', 'A senha é obrigatória.');
            } else {
                $usuario = $this->model->verificarLogin($email, $senha);

                if ($usuario) {
                    $_SESSION['usuario_logado'] = true;
                    $_SESSION['usuario_nome'] = $usuario['usuNome'];
                    header("Location: /bookbox/painel");
                    exit();
                } else {
                    adicionarMensagemSessao('senha', 'Email ou senha inválidos.');
                }
            }

            header("Location: /bookbox/login");
            exit();
        } else {
            require_once __DIR__ . '/../resources/views/usuarios/login.php';
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
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            adicionarMensagemSessao('email', 'O e-mail fornecido tem formato inválido.');
            return false;
        }
        return true;
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
