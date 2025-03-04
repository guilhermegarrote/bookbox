<?php

require_once __DIR__ . '/../../config/database.php';

class AlunoModel
{
    private PDO $db;

    /**
     * Construtor da classe, responsável por estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Método responsável por cadastrar novo aluno.
     * @param string $nome
     * @param string $email
     * @param string $telefone
     * @param bool $statusAluno
     * @param int $salaId
     * @return bool
     */

    public function cadastrar($nome, $email, $telefone, $satus, $salaId)
    {
        $query = "INSERT INTO tbAlunos (aluNome, aluEmail, aluTelefone, aluStatus, fdSalaId) VALUES (:nome, :email, :telefone, :statusAluno, :salaId)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":telefone", $telefone);
        $stmt->bindParam(":statusAluno", $statusAluno);
        $stmt->bindParam(":salaId", $salaId);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar aluno.
     * @param int $id
     * @param string|null $nome
     * @param string|null $email
     * @param string|null $telefone
     * @param string|null $statusAluno
     * @param string|null $salaId
     * @return bool
     */

    public function editar($id, $nome, $email, $telefone, $statusAluno, $salaId)
    {
        $query = "UPDATE tbAlunos SET ";

        if ($nome) {
            $query .= "aluNome = :nome, ";
        }
        if ($email) {
            $query .= "aluEmail = :Email, ";
        }
        if ($telefone) {
            $query .= "aluTelefone = :telefone, ";
        }
        if ($statusAluno) {
            $query .= "aluStatus = :statusAluno, ";
        }
        if ($salaId) {
            $query .= "fkSalaId = :salaId ";
        }

        $query .= " WHERE aluId= :id";

        $stmt = $this->db->prepare($query);

        if ($nome) {
            $stmt->bindParam(":nome", $nome);
        }
        if ($email) {
            $stmt->bindParam(":email", $email);
        }
        if ($telefone) {
            $stmt->bindParam(":telefone", $telefone);
        }
        if ($statusAluno) {
            $stmt->bindParam(":statusAluno", $statusAluno);
        }
        if ($salaId) {
            $stmt->bindParam(":salaId", $salaId);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir aluno.
     * @param int $id
     * @return bool
     */

    public function excluir($id)
    {
        $query = "DELETE FROM tbAlunos WHERE aluId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
