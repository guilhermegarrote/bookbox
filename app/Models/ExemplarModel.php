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
 * Método responsável por pesquisar o livro pelo ISBN e retornar o ID do livro.
 * @param string $isbn
 * @return int|false
 */
public function pesquisaLivroPorIsbn($isbn)
{
    $query = "SELECT livId FROM tblivros WHERE livIsbn = :isbn";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":isbn", $isbn);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $livro = $stmt->fetch(PDO::FETCH_ASSOC);
        return $livro['livId'];  
    }

    return false;  
}

/**
 * Método responsável por verificar se o exemplar existe.
 * @param int $numeroExemplar
 * @param string $livId
 * @return bool 
 */
public function pesquisarExemplar($numeroExemplar, $livId)
{
    $queryExemplar = "SELECT * FROM tbexemplares WHERE exNumero = :numeroExemplar AND fkLivId = :livId";
    $stmtExemplar = $this->db->prepare($queryExemplar);
    $stmtExemplar->bindParam(":numeroExemplar", $numeroExemplar);
    $stmtExemplar->bindParam(":livId", $livId);
    
    return $stmtExemplar->execute() && $stmtExemplar->rowCount() > 0;
}


    /**
     * Método responsável por cadastrar novo exemplar. 	
     * @param string $livId
     * @return bool
     */
    public function cadastrar($livId)
{
    $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

    $query = "SELECT COALESCE(MAX(exNumero), 0) + 1 as proxNumero FROM tbexemplares WHERE fkLivId = :livId";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(":livId", $livId);
    $stmt->execute();
    $proxNumero = $stmt->fetchColumn();

    $query = "INSERT INTO tbexemplares (exId, fkLivId, exNumero) VALUES (:uuid, :livId, :numero)";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(":uuid", $uuidBin, PDO::PARAM_LOB);
    $stmt->bindValue(":livId", $livId);
    $stmt->bindValue(":numero", $proxNumero);
    
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
