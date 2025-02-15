<?php

require_once __DIR__ . '/../../config/database.php';

class UsuarioModel
{
    private $db;

    /**
     * Construtor da classe, responsável por estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Método responsável por retornar todos os usuários.
     * @return void
     */
    public function listarTodos()
    {
        $query = "SELECT usuId, usuNome FROM tbusuarios";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método responsável por buscar o usuario com o id passado por parâmetro.
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id)
    {
        $query = "SELECT usuId, usuNome FROM tbusuarios WHERE usuId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Método responsável por cadastrar novo usuário.
     * @param string $nome
     * @param string $senha
     * @return bool
     */
    public function cadastrar($nome, $senha)
    {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $query = "INSERT INTO tbusuarios (usuNome, usuSenha) VALUES (:nome, :senha)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":senha", $senhaHash);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar usuário.
     * @param int $id
     * @param string|null $nome
     * @param string|null $senha
     * @return bool
     */
    public function editar($id, $nome, $senha)
    {
        $query = "UPDATE tbusuarios SET ";

        if ($nome) {
            $query .= "usuNome = :nome, ";
        }

        if ($senha) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $query .= "usuSenha = :senha, ";
        }

        $query .= " WHERE usuId = :id";

        $stmt = $this->db->prepare($query);

        if ($nome) {
            $stmt->bindParam(":nome", $nome);
        }

        if ($senha) {
            $stmt->bindParam(":senha", $senhaHash);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir usuário.
     * @param int $id
     * @return bool
     */
    public function excluir($id)
    {
        $query = "DELETE FROM tbusuarios WHERE usuId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    /**
     * Método responsável por verificar login.
     * @param string $nome
     * @param string $senha
     * @return array/false
     */
    public function verificarLogin($nome, $senha)
    {
        $query = "SELECT usuId, usuNome, usuSenha FROM tbusuarios WHERE usuNome = :nome";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":nome", $nome);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['usuSenha'])) {
            return $usuario;
        }

        return false;
    }
}
