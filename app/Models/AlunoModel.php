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
     * @param string $cpf
     * @param string $email
     * @param string $telefone
     * @param string $turmaId
     * @param bool $bloqueio
     * @return bool
     */
    public function cadastrar($nome, $email, $telefone, $turmaId, $bloqueio)
    {

        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

        $query = "INSERT INTO tbalunos (aluNome, aluCpf, aluEmail, aluTelefone, fkTurId, aluBloqueio) VALUES (:nome, :cpf, :email, :telefone, :turmaId, :bloqueio)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":cpf", $cpf);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":telefone", $telefone);
        $stmt->bindParam(":turmaId", $turmaId);
        $stmt->bindParam(":bloqueio", $bloqueio);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar aluno.
     * @param int $id
     * @param string|null $nome
     * @param string|null $cpf
     * @param string|null $email
     * @param string|null $telefone
     * @param string|null $turmaId
     * @param string|null $bloqueio
     * @return bool
     */
    public function editar($id, $nome, $cpf, $email, $telefone, $turmaId, $bloqueio)
    {
        $query = "UPDATE tbalunos SET ";

        if ($nome) {
            $query .= "aluNome = :nome, ";
        }
        if ($cpf) {
            $query .= "aluCpf = :cpf, ";
        }
        if ($email) {
            $query .= "aluEmail = :Email, ";
        }
        if ($telefone) {
            $query .= "aluTelefone = :telefone, ";
        }
        if ($turmaId) {
            $query .= "fkTurId = :turmaId, ";
        }
        if ($bloqueio) {
            $query .= "aluBloqueio = :bloqueio ";
        }

        $query .= " WHERE aluId= :id";

        $stmt = $this->db->prepare($query);

        if ($nome) {
            $stmt->bindParam(":nome", $nome);
        }
        if ($cpf) {
            $stmt->bindParam(":cpf", $cpf);
        }
        if ($email) {
            $stmt->bindParam(":email", $email);
        }
        if ($telefone) {
            $stmt->bindParam(":telefone", $telefone);
        }
        if ($turmaId) {
            $stmt->bindParam(":turmaId", $turmaId);
        }
        if ($bloqueio) {
            $stmt->bindParam(":bloqueio", $bloqueio);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir aluno.
     * @param string $id
     * @return bool
     */
    public function excluir($id)
    {
        $query = "DELETE FROM tbalunos WHERE aluId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
