<?php

require_once __DIR__ . '/../../config/database.php';

class CursoModel
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
     * Método responsável por cadastrar empréstimo
     * @param string $dtInicial
     * @param string $dtFinal
     * @param int $alunoId
     * @param int $livroId
     * @return bool
     */

    public function cadastrar($dtInicial, $dtFinal, $alunoId, $livroId)
    {
        $query = "INSERT INTO tbEmprestimos (empDataInicial, empDataFinal, fkAlunoId, fkLivroId) VALUES (:dtInicial, :dtFinal, :alunoId, livroId)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":dtInicial", $dtInicial);
        $stmt->bindParam(":dtFinal", $dtFinal);
        $stmt->bindParam(":alunoId", $alunoId);
        $stmt->bindParam(":livroId", $livroId);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar empréstimo.
     * @param int $id
     * @param string|null $dtInicial
     * @param string|null $dtFinal
     * @param string|null $alunoId
     * @param string|null $livroId
     * @return bool
     */

    public function editar($id, $dtInicial, $dtFinal, $alunoId, $livroId)
    {
        $query = "UPDATE tbEmprestimos SET ";

        if ($dtInicial) {
            $query .= "dtInicial = :dtInicial, ";
        }
        if ($dtFinal) {
            $query .= "dtFinal = :dtFinal, ";
        }
        if ($alunoId) {
            $query .= "alunoId = :alunoId, ";
        }
        if ($livroId) {
            $query .= "livroId = :livroId, ";
        }

        $query .= " WHERE  empId= :id";

        $stmt = $this->db->prepare($query);

        if ($dtInicial) {
            $stmt->bindParam(":dtInicial", $dtInicial);
        }
        if ($dtFinal) {
            $stmt->bindParam(":dtFinal", $dtFinal);
        }
        if ($alunoId) {
            $stmt->bindParam(":alunoId", $alunoId);
        }
        if ($livroId) {
            $stmt->bindParam(":livroId", $livroId);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir empréstimo.
     * @param int $id
     * @return bool
     */

    public function excluir($id)
    {
        $query = "DELETE FROM tbEmprestimos WHERE empId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
