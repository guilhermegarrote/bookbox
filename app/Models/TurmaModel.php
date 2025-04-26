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
    public function cadastrar($curso, $horario, $regime, $dataInicio, $dataFim)
    {
        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

        $query = "INSERT INTO tbturmas (turId, turCurso, turHorario, turRegime, turDataInicio, turDataFim) 
        VALUES (:uuid, :curso, :horario, :regime, :dataInicio, :dataFim)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":curso", $curso);
        $stmt->bindParam(":horario", $horario);
        $stmt->bindParam(":regime", $regime);
        $stmt->bindParam(":dataInicio", $dataInicio);
        $stmt->bindParam(":dataFim", $dataFim);
        return $stmt->execute();
    }

    public function editar($id, $curso, $horario, $regime, $dataInicio, $dataFim)
    {
        $campos = [];
        $parametros = [':id' => $id];
    
        if ($curso !== null) {
            $campos[] = "turCurso = :curso";
            $parametros[':curso'] = $curso;
        }
        if ($horario !== null) {
            $campos[] = "turHorario = :horario";
            $parametros[':horario'] = $horario;
        }
        if ($regime !== null) {
            $campos[] = "turRegime = :regime";
            $parametros[':regime'] = $regime;
        }
        if ($dataInicio !== null) {
            $campos[] = "turDataInicio = :dataInicio";
            $parametros[':dataInicio'] = $dataInicio;
        }
        if ($dataFim !== null) {
            $campos[] = "turDataFim = :dataFim";
            $parametros[':dataFim'] = $dataFim;
        }
    
        if (empty($campos)) {
            return false;
        }
    
        $query = "UPDATE tbturmas SET " . implode(", ", $campos) . " WHERE turId = :id";
    
        $stmt = $this->db->prepare($query);
    
        foreach ($parametros as $param => $valor) {
            $stmt->bindValue($param, $valor);
        }
    
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

    /**
     * Método responsável por pesquisar curso.
     * @param string $curso
     * @return bool
     */
    public function buscaCurso($curso)
    {
        $query = "SELECT * FROM tbturmas WHERE turCurso = :curso";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":curso", $curso);
        return $stmt->execute();
    }

}
