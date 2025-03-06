<?php

require_once __DIR__ . '/../../config/database.php';

class ExemplarModel
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
     * Método responsável por cadastrar novo exemplar. 	
     * @param string $id
     * @param string $livId
     * @param int $numero
     * @return bool
     */
    public function cadastrar($id, $livId, $numero, $disponibilidade)
    {
        $query = "INSERT INTO tbexemplares (exId, fkLivId, exNumero) 
        VALUES (:id,:livId,:numero)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":livId", $livId);
        $stmt->bindParam(":numero", $numero);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar exemplar.
     * @param string $id
     * @param string $livId
     * @param int $numero
     * @param string  $disponibilidade
     * @return bool
     * @return bool
     */

    public function editar($id, $livId, $numero, $disponibilidade)
    {
        $query = "UPDATE tbexemplares SET ";
        if ($id) {
            $query .= "exId = :id, ";
        }
        if ($livId) {
            $query .= "fkLivId = :livId, ";
        }
        if ($numero) {
            $query .= "exNumero = :numero, ";
        }
        if ($disponibilidade) {
            $query .= "exDisponibilidade = :disponibilidade, ";
        }
        $query .= " WHERE exId = :id";

        $stmt = $this->db->prepare($query);

        if ($id) {
            $stmt->bindParam(":id", $id);
        }
        if ($livId) {
            $stmt->bindParam(":livId", $livId);
        }
        if ($numero) {
            $stmt->bindParam(":numero", $numero);
        }
        if ($disponibilidade) {
            $stmt->bindParam(":disponibilidade", $disponibilidade);
        }
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir exemplar.
     * @param int $id
     * @return bool
     */
    public function excluir($id)
    {
        $query = "DELETE FROM tbexemplares WHERE exId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
