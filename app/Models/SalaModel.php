<?php

require_once __DIR__ . '/../../config/database.php';

class SalaModel
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
     * Método responsável por cadastrar nova sala.
     * @param string $serieModulo
     * @param string $curId
     * @return bool
     */
    public function cadastrar($serieModulo, $curId)
    {
        $query = "INSERT INTO tbsalas (salSerieModulo, fkCurId) VALUES (:serieModulo, :curId)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":serieModulo", $serieModulo);
        $stmt->bindParam(":curId", $curId);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar sala.
     * @param int $id
     * @param string|null $serieModulo
     * @param string|null $curId
     * @return bool
     */
    public function editar($id, $serieModulo, $fkCurId)
    {
        $query = "UPDATE tbsalas SET ";

        if ($serieModulo) {
            $query .= "salSerieModulo = :serieModulo, ";
        }

        if ($curId) {
            $query .= "fkCurId = :curId, ";
        }

        $query .= " WHERE salId = :id";

        $stmt = $this->db->prepare($query);

        if ($serieModulo) {
            $stmt->bindParam(":serieModulo", $serieModulo);
        }

        if ($curId) {
            $stmt->bindParam(":curId", $curId);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir sala.
     * @param int $id
     * @return bool
     */
    public function excluir($id)
    {
        $query = "DELETE FROM tbsalas WHERE salId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

}
