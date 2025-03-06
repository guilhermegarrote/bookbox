<?php

require_once __DIR__ . '/../../config/database.php';

class TurmaModel
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
     * Método responsável por cadastrar nova turma. 	
     * @param string $id
     * @param int $periodo
     * @param string $curso
     * @param string  $horario
     * @param string $regime
     * @param string $dataInicio
     * @param string $dataFim
     * @return bool
     */
    public function cadastrar($id, $periodo, $curso, $horario, $regime, $dataInicio, $dataFim)
    {
        $query = "INSERT INTO tbturmas (turId, turPeriodo, turCurso, turHorario, turRegime, turDataInicio, turDataFim) 
        VALUES (:id,:periodo,:curso, :horario, :regime,:dataInicio, :dataFim )";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":periodo", $periodo);
        $stmt->bindParam(":curso", $curso);
        $stmt->bindParam(":horario", $horario);
        $stmt->bindParam(":regime", $regime);
        $stmt->bindParam(":dataInicio", $dataInicio);
        $stmt->bindParam(":dataFim", $dataFim);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar turma.
     * @param string $id
     * @param int|null $periodo
     * @param string|null $curso
     * @param string|null $horario
     * @param string|null $regime
     * @param string|null $dataInicio
     * @param string|null $dataFim
     * @return bool
     */

    public function editar($id, $periodo, $curso, $horario, $regime, $dataInicio, $dataFim)
    {
        $query = "UPDATE tbturmas SET ";

        if ($id) {
            $query .= "turId = :id, ";
        }
        if ($periodo) {
            $query .= "turPeriodo = :periodo, ";
        }
        if ($curso) {
            $query .= "turCurso = :curso, ";
        }
        if ($horar) {
            $query .= "turHorario = :horario, ";
        }
        if ($regime) {
            $query .= "turRegime = :regime, ";
        }
        if ($dataInicio) {
            $query .= "turDataInicio = :dataInicio, ";
        }
        if ($dataFim) {
            $query .= "turDataFim = :dataFim, ";
        }
        $query .= " WHERE turId = :id";

        $stmt = $this->db->prepare($query);

        if ($id) {
            $stmt->bindParam(":id", $id);
        }
        if ($periodo) {
            $stmt->bindParam(":periodo", $periodo);
        }
        if ($curso) {
            $stmt->bindParam(":curso", $curso);
        }
        if ($horario) {
            $stmt->bindParam(":horario", $horario);
        }
        if ($regime) {
            $stmt->bindParam(":regime", $regime);
        }
        if ($dataInicio) {
            $stmt->bindParam(":dataInicio", $dataInicio);
        }
        if ($dataFim) {
            $stmt->bindParam(":dataFim", $dataFim);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir turma.
     * @param int $string
     * @return bool
     */
    public function excluir($id)
    {
        $query = "DELETE FROM tbturmas WHERE turId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
