<?php

require_once __DIR__ . '/../../config/database.php';

class LivroModel
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
     * Método responsável por cadastrar novo livro. 	
     * @param string $Ibsn
     * @param string $titulo
     * @param string $autor
     * @param string $generoId
     * @param string $editora
     * @return bool
     */
    public function cadastrar($Ibsn, $titulo, $autor, $generoId, $editora)
    {
        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

        $query = "INSERT INTO tblivros (livId, livIBSN, livTitulo, livAutor, fkGenId, livEditora) 
        VALUES (:uuid, :Ibsn, :titulo, :autor,:generoId, :editora )";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":Ibsn", $Ibsn);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":autor", $autor);
        $stmt->bindParam(":generoId", $generoId);
        $stmt->bindParam(":editora", $editora);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar livro.
     * @param string $id
     * @param string|null $Ibsn
     * @param string|null $titulo
     * @param string|null $autor
     * @param string|null $editora
     * @param string|null $generoId
     * @return bool
     */
    public function editar($id, $Ibsn, $titulo, $autor, $generoId, $editora)
    {
        $query = "UPDATE tblivros SET ";

        if ($Ibsn) {
            $query .= "livIBSN = :Ibsn, ";
        }
        if ($titulo) {
            $query .= "livTitulo = :titulo, ";
        }
        if ($autor) {
            $query .= "livAutor = :autor, ";
        }
        if ($generoId) {
            $query .= "fkGeneroId = :generoId, ";
        }
        if ($editora) {
            $query .= "livEditora = :editora, ";
        }


        $query .= " WHERE livId = :id";

        $stmt = $this->db->prepare($query);

        if ($Ibsn) {
            $stmt->bindParam(":Ibsn", $Ibsn);
        }
        if ($titulo) {
            $stmt->bindParam(":titulo", $titulo);
        }
        if ($autor) {
            $stmt->bindParam(":autor", $autor);
        }
        if ($generoId) {
            $stmt->bindParam(":generoId", $generoId);
        }
        if ($editora) {
            $stmt->bindParam(":editora", $editora);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por excluir livro.
     * @param string $id
     * @return bool
     */
    public function excluir($id)
    {
        $query = "DELETE FROM tblivros WHERE livId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
