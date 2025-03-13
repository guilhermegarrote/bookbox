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
     * @param string $alunoId
     * @param string $exemplarId
     * @return bool
     */
    public function cadastrar($alunoId, $exemplarId)
    {

        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

        $query = "INSERT INTO tbemprestimos (fkAluId, fkExId, empDataDevolucao) VALUES (:alunoId, :exemplarId, curdate() + 7)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":alunoId", $alunoId);
        $stmt->bindParam(":exemplarId", $exemplarId);
        return $stmt->execute();
    }


    /**
     * Método responsável por finalizar empréstimo.
     * @param string $id
     * @return bool
     */
    public function finalizar($id)
    {
        $query = "UPDATE tbemprestimos SET empDataDevolucao = curdate() + 7, empAtivo = FALSE";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
