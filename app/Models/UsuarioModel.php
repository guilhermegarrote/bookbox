<?php

require_once __DIR__ . '/../../config/Database.php';

class UsuarioModel
{
    private PDO $db;

    /**
     * Construtor que estabelece a conexão automaticamente.
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Método responsável por cadastrar novo usuário.
     * @param string $nome
     * @param string $email
     * @param string $senha
     * @return bool $resultado
     */
    public function cadastrar($nome, $email, $senha)
    {
        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));
        $email = criptografar($email);
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $query = "INSERT INTO tbusuarios (usuId, usuNome, usuEmail, usuSenha) VALUES (:uuid, :nome, :email, :senha)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":senha", $senhaHash);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar usuário.
     * @param int $id
     * @param string|null $nome
     * @param string|null $email
     * @param string|null $senha
     * @return bool $resultado
     */
    public function editar($id, $nome, $email, $senha)
    {
        $query = "UPDATE tbusuarios SET ";

        if ($nome) {
            $query .= "usuNome = :nome, ";
        }
        if ($email !== null) {
            $email = criptografar($email);
            $query .= "usuEmail = :email, ";
        }
        if ($senha !== null) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $query .= "usuSenha = :senha, ";
        }

        $query .= " WHERE usuId = :id";

        $stmt = $this->db->prepare($query);

        if ($nome !== null) {
            $stmt->bindParam(":nome", $nome);
        }
        if ($email !== null) {
            $stmt->bindParam(":email", $email);
        }
        if ($senha !== null) {
            $stmt->bindParam(":senha", $senhaHash);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir usuário.
     * @param int $id
     * @return bool $resultado
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
     * @param string $email
     * @param string $senha
     * @return array|false $usuario
     */
    public function verificarLogin($email, $senha)
    {
        $email = criptografar($email);

        $query = "SELECT usuNome, usuEmail, usuSenha FROM tbusuarios WHERE usuEmail = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['usuSenha'])) {
            return $usuario;
        }

        return false;
    }

    /**
     * Método responsável por verificar se existem usuarios cadastrados
     * @return boolean $resultado
     */
    public function existeUsuarios()
    {
        $query = "SELECT COUNT(usuId) FROM tbusuarios;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $quantidade = $stmt->fetchColumn();

        return $quantidade > 0;
    }
}
