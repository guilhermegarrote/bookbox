<?php

require_once __DIR__ . '/../../config/Database.php';

class AlunoModel
{
    private PDO $db;

    /**
     * Construtor da classe, responsável por estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct(PDO $db)
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
        $cpf = criptografar($cpf);
        $email = criptografar($email);
        $telefone = $telefone !== null ? criptografar($telefone) : null;

        $query = "INSERT INTO tbalunos (aluId, aluNome, aluCpf, aluEmail, aluTelefone)
              VALUES (:uuid, :nome, :cpf, :email, :telefone)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":uuid", $uuidBin, PDO::PARAM_STR);
        $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
        $stmt->bindValue(":cpf", $cpf, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":telefone", $telefone, $telefone === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método responsável por buscar aluno.
     * @param string|null $id
     * @param string|null $nome
     * @param string|null $cpf
     * @param string|null $email
     * @param string|null $telefone
     * @return array|false 
     */
    public function buscarAluno($id, $nome, $cpf, $email, $telefone)
    {
        $campos = [];
        $params = [];

        if ($id !== null) {
            $campos[] = "aluId = :id";
            $params[':id'] = $id;
        }
        if ($nome !== null) {
            $campos[] = "aluNome = :nome";
            $params[':nome'] = $nome;
        }
        if ($cpf !== null) {
            $campos[] = "aluCpf = :cpf";
            $params[':cpf'] = criptografar($cpf);
        }
        if ($email !== null) {
            $campos[] = "aluEmail = :email";
            $params[':email'] = criptografar($email);
        }
        if ($telefone !== null) {
            $campos[] = "aluTelefone = :telefone";
            $params[':telefone'] = criptografar($telefone);
        }

        $query = "SELECT * FROM tbalunos WHERE " . implode(" AND ", $campos);
        $stmt = $this->db->prepare($query);

        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor, PDO::PARAM_STR);
        }

        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: false;
    }

    /**
     * Método responsável por editar aluno.
     * @param string $id
     * @param string|null $nome
     * @param string|null $cpf
     * @param string|null $email
     * @param string|null $telefone
     * @return bool
     */
    public function editar($id, $nome, $cpf, $email, $telefone)
    {
        $campos = [];
        $params = [];

        if ($nome !== null) {
            $campos[] = "aluNome = :nome";
            $params[':nome'] = $nome;
        }
        if ($cpf !== null) {
            $campos[] = "aluCpf = :cpf";
            $params[':cpf'] = criptografar($cpf);
        }
        if ($email !== null) {
            $campos[] = "aluEmail = :email";
            $params[':email'] = criptografar($email);
        }
        if ($telefone !== null) {
            $campos[] = "aluTelefone = :telefone";
            $params[':telefone'] = criptografar($telefone);
        }

        $query = "UPDATE tbalunos SET " . implode(", ", $campos) . " WHERE aluId = :id";
        $stmt = $this->db->prepare($query);

        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor);
        }

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

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
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
