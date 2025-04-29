<?php

require_once __DIR__ . '/../../config/Database.php';

class AlunosTurmasModel
{
    private PDO $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Método responsável por cadastrar relação entre aluno e turma.
     * @param string $fkAluId
     * @param string $fkTurId
     * @return bool
     */
    public function cadastrar($fkAluId, $fkTurId)
    {
        $query = "INSERT INTO tbalunos_turmas (fkAluId, fkTurId) VALUES (:fkAluId, :fkTurId)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fkAluId', $fkAluId, PDO::PARAM_LOB);
        $stmt->bindParam(':fkTurId', $fkTurId, PDO::PARAM_LOB);

        return $stmt->execute();
    }

    /**
     * Método responsável por editar relação entre aluno e turma.
     * @param string $id
     * @param string|null $fkAluId
     * @param string|null $fkTurId
     * @return bool
     */
    public function editar($id, $fkAluId, $fkTurId)
    {
        $query = "UPDATE tbalunos_turmas SET ";

        if ($fkAluId !== null) {
            $query .= "fkAluId = :fkAluId, ";
        }
        if ($fkTurId !== null) {
            $query .= "fkTurId = :fkTurId, ";
        }
        $query = rtrim($query, ', ');

        $query .= " WHERE aluTurId = :id";

        $stmt = $this->db->prepare($query);

        if ($fkAluId !== null) {
            $stmt->bindParam(":fkAluId", $fkAluId);
        }
        if ($fkTurId !== null) {
            $stmt->bindParam(":fkTurId", $fkTurId);
        }

        $stmt->bindParam(":aluTurId", $id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir relação entre aluno e turma.
     * @param string $aluTurId
     * @return bool
     */
    public function excluir($aluTurId)
    {
        $query = "DELETE FROM tbalunos_turmas WHERE aluTurId = :aluTurId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':aluTurId', $aluTurId, PDO::PARAM_LOB);
        return $stmt->execute();
    }
}
