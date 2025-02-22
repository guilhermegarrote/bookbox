<?php

require_once __DIR__ . '/../../config/database.php';

class CursoModel
{
    private $db

    /**
     * Construtor da classe, responsável por estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct($db)
    {
        $this->db = $db;
    }
 
/**
 * Método responsável por cadastrar cursos.
 * @param string $nome
 * @param string $periodo
 * @return bool 
 */

 public function cadastrar($nome, $periodo)
 {
   $query = "INSERT INTO tbCursos (curNome, curPeriodo) VALUES (:nome, :periodo)";
   $stmt = $this->db->prepare($query);
   $stmt->bindParam(":nome", $nome);
   $stmt->bindParam(":periodo", $periodo);
   return $stmt->execute();
 }

 /**
  * Método responsável por editar cursos.
  * @param int $id
  * @param string|null $nome
  * @param string|null $periodo
  * @return bool
*/

public function editar($id, $nome, $periodo)
{
    $query = "UPDATE tbAlunos SET "; 
    
    if ($nome) {
        $query .= "curNome = :nome, ";
    }
    if ($periodo) {
        $query .= "curPeriodo = :periodo, ";
    }

    $query .= " WHERE  curId= :id";

    $stmt = $this->db->prepare($query);

    if ($nome) {
        $stmt->bindParam(":nome", $nome);
    }
    if ($periodo) {
        $stmt->bindParam(":periodo", $periodo);
    }

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    return $stmt->execute();
}

/**
     * Método responsável por excluir curso.
     * @param int $id
     * @return bool
     */

     public function excluir($id)
     {
         $query = "DELETE FROM tbCursos WHERE curId = :id";
         $stmt = $this->db->prepare($query);
         $stmt->bindParam(":id", $id);
         return $stmt->execute();
     }


}