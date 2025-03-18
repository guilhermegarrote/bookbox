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
     * @param string $curso
     * @param string $horario
     * @param string $regime
     * @param string $dataInicio
     * @param string $dataFim
     * @return bool
     */
    public function cadastrar($periodo, $curso, $horario, $regime, $dataInicio, $dataFim)
    {

        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

        $query = "INSERT INTO tbturmas (turId, turCurso, turHorario, turRegime, turDataInicio, turDataFim) 
        VALUES (:uuid ,:periodo,:curso, :horario, :regime, :dataInicio, :dataFim )";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
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

        if ($curso) {
            $query .= "turCurso = :curso, ";
        }
        if ($horario) {
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
     * @param string $id
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
