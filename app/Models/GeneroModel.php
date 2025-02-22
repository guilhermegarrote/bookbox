<?php

require_once __DIR__ . '/../../config/database.php';

class GeneroModel
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
     * Método responsável por adiconar novo genêro.
     * @param string $nome
     * @param string $cor
     * @return bool
     */
    public function adicionar($nome, $cor)
    {
        $query = "INSERT INTO tbgeneros (genNome, genCor) VALUES (:nome, :cor)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":cor", $cor);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar gênero.
     * @param int $id
     * @param string|null $nome
     * @param string|null $cor
     * @return bool
     */
    public function editar($id, $nome, $cor)
    {
        $query = "UPDATE tbgeneros SET ";

        if ($nome) {
            $query .= "genNome = :nome";
        }
        if ($cor) {
            $query .= "genCor = :cor";
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
     * @param int $id
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
