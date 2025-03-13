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
     * @param string $livId
     * @param int $numero
     * @return bool
     */
    public function cadastrar($livId, $numero)
    {

        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

        $query = "INSERT INTO tbexemplares (fkLivId, exNumero) 
        VALUES (:livId, :numero)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":livId", $livId);
        $stmt->bindParam(":numero", $numero);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar exemplar.
     * @param string $id
     * @param string $livId
     * @param int $numero
     * @return bool
     * @return bool
     */

    public function editar($id, $livId, $numero)
    {
        $query = "UPDATE tbexemplares SET ";

        if ($livId) {
            $query .= "fkLivId = :livId, ";
        }
        if ($numero) {
            $query .= "exNumero = :numero, ";
        }
        $query .= " WHERE exId = :id";

        $stmt = $this->db->prepare($query);

        if ($livId) {
            $stmt->bindParam(":livId", $livId);
        }
        if ($numero) {
            $stmt->bindParam(":numero", $numero);
        }
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir exemplar.
     * @param string $id
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
