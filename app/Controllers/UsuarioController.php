<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/UsuarioModel.php';

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
     * Método responsável por retornar todos os usuarios.
     * @return void
     */
    public function listar()
    {
        $usuarios = $this->model->listarTodos();
    }

    /**
     * Método responsável por verificar login.
     * @return void
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['usuNome'];
            $senha = $_POST['usuSenha'];

            $usuario = $this->model->verificarLogin($nome, $senha);

            if (!empty($usuario) && is_array($usuario)) {
                $_SESSION['usuario_logado'] = true;
                $_SESSION['usuario_id'] = $usuario['usuId'];
                $_SESSION['usuario_nome'] = $usuario['usuNome'];

                header("Location: /Bookbox/painel");
                exit();
            } else {
                $_SESSION['erro_login'] = 'Usuário ou senha incorretos!';
                header("Location: /Bookbox/login");
                exit();
            }
        } else {
            require_once __DIR__ . '/../resources/views/usuarios/login.php';
        }
    }
}
