<?php

require_once __DIR__ . '/../../config/database.php';

class EmprestimoModel
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

        $query = "INSERT INTO tbemprestimos (empId, fkAluId, fkExId, empDataDevolucao) VALUES (:uuid, :alunoId, :exemplarId, curdate() + 14)";
        $stmt = $this->db->prepare(query: $query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":alunoId", $alunoId);
        $stmt->bindParam(":exemplarId", $exemplarId);
    }

    /**
     * Método responsável por prolongar empréstimo.
     * @param string $id
     * @return bool
     */
    public function prolongarEmprestimo($id)
    {
        $query = "UPDATE tbemprestimos 
                  SET empDataDevolucao = CURDATE() + 14 , empAtivo = TRUE 
                  WHERE empId = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método responsável por finalizar empréstimo.
     * @param string $id
     * @return bool
     */
    public function finalizar($id)
    {
        $query = "UPDATE tbemprestimos SET empAtivo = FALSE WHERE empId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}