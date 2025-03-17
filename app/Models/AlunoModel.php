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
     * @param string|null $telefone
     * @return bool
     */
    public function cadastrar($nome, $cpf, $email, $telefone)
    {

        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

        $query = "INSERT INTO tbalunos (aluNome, aluCpf, aluEmail, aluTelefone) VALUES (:nome, :cpf, :email, :telefone)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":cpf", $cpf);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":telefone", $telefone, $telefone ? PDO::PARAM_STR : PDO::PARAM_NULL);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar aluno.
     * @param int $id
     * @param string|null $nome
     * @param string|null $cpf
     * @param string|null $email
     * @param string|null $telefone
     * @return bool
     */
    public function editar($id, $nome, $cpf, $email, $telefone)
    {
        $query = "UPDATE tbalunos SET ";

        if ($nome) {
            $query .= "aluNome = :nome, ";
        }
        if ($cpf) {
            $query .= "aluCpf = :cpf, ";
        }
        if ($email) {
            $query .= "aluEmail = email, ";
        }
        if ($telefone !== null) {
            $query .= "aluTelefone = :telefone, ";
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
        if ($telefone !== null) {
            $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        } else {
            $stmt->bindValue(":telefone", null, PDO::PARAM_NULL);
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

  /**
     * Método responsável por buscar o ID de um aluno pelo CPF.
     * @param string $cpf
     * @return int|false 
     */
    public function buscaAlunoPorCpf($cpf)
    {
        
        $query = "SELECT aluId FROM tbalunos WHERE aluCpf = :cpf";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cpf", $cpf, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? (int) $resultado['aluId'] : false;
    }
}