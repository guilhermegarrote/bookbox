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
        $cpf = criptografar($cpf);
        $email = criptografar($email);

        $query = "INSERT INTO tbalunos (aluId, aluNome, aluCpf, aluEmail, aluTelefone) VALUES (:uuid, :nome, :cpf, :email, :telefone)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":cpf", var: $cpf);
        $stmt->bindParam(":email", $email);

        if ($telefone === null) {
            $stmt->bindValue(":telefone", $telefone, PDO::PARAM_NULL);
        } else {
            $telefone = criptografar($telefone);
            $stmt->bindParam(":telefone", $telefone, PDO::PARAM_STR);
        }

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
        $uuid = str_replace(['-', '0x'], '', strtolower(trim($id)));

        if (!ctype_xdigit($uuid) || strlen($uuid) !== 32) {
            throw new Exception("ID inválido.");
        }

        $uuidBin = hex2bin($uuid);

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

        if (empty($campos)) {
            throw new Exception("Nenhum campo foi fornecido para atualizar.");
        }

        $query = "UPDATE tbalunos SET " . implode(", ", $campos) . " WHERE aluId = :id";

        $stmt = $this->db->prepare($query);

        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor);
        }

        $stmt->bindValue(':id', $uuidBin, PDO::PARAM_LOB);

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
     * Método responsável por buscar todas as informações de um aluno pelo CPF.
     * @param string $cpf
     * @return array|false 
     */
    public function buscarAlunoPorCpf($cpf)
    {
        $query = "SELECT * FROM vwalunos WHERE aluCpf = :cpf";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cpf", $cpf, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado : false;
    }
    public function existeCpf($cpf)
    {
        $cpf = criptografar($cpf);

        $query = "SELECT 1 FROM tbalunos WHERE aluCpf = :cpf LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();

        return $stmt->fetchColumn() !== false;
    }
}
