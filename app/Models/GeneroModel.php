<?php

require_once __DIR__ . '/../../config/database.php';

class GeneroModel
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
     * Método responsável por adiconar novo genêro.
     * @param string $nome
     * @param string $cor
     * @return bool
     */
    public function cadastrar($nome, $cor)
    {

        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

        $query = "INSERT INTO tbgeneros (genNome, genCorHex) VALUES (:nome, :cor)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":cor", $cor);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar gênero.
     * @param string $id
     * @param string|null $nome
     * @param string|null $cor
     * @return bool
     */
    public function editar($nome, $cor)
    {
        $query = "UPDATE tbgeneros SET ";

        if ($nome) {
            $query .= "genNome = :nome";
        }
        if ($cor) {
            $query .= "genCorHex = :cor";
        }

        $query .= " WHERE genId = :id";
        $stmt = $this->db->prepare($query);

        if ($nome) {
            $stmt->bindParam(":nome", $nome);
        }
        if ($cor) {
            $stmt->bindParam(":cor", $cor);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Método responsável por excluir genêro.
     * @param string $id
     * @return bool
     */
    public function excluir($id)
    {
        $query = "DELETE FROM tbgeneros WHERE genId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
