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
     * @param string $Isbn
     * @param string $titulo
     * @param string $autor
     * @param string $generoId
     * @param string $editora
     * @return bool
     */
    public function cadastrar($Isbn, $titulo, $autor, $generoId, $editora)
    {
        $uuidBin = hex2bin(str_replace('-', '', gerarUuid()));

        $query = "INSERT INTO tblivros (livId, livIsbn, livTitulo, livAutor, fkGenId, livEditora) 
        VALUES (:uuid, :Isbn, :titulo, :autor, :generoId, :editora)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uuid", $uuidBin, PDO::PARAM_LOB);
        $stmt->bindParam(":Isbn", $Isbn);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":autor", $autor);
        $stmt->bindParam(":generoId", $generoId);
        $stmt->bindParam(":editora", $editora);
        return $stmt->execute();
    }

    /**
 * Método responsável por editar livro.
 * @param string $id
 * @param string|null $Isbn
 * @param string|null $titulo
 * @param string|null $autor
 * @param string|null $generoId
 * @param string|null $editora
 * @return bool
 */
public function editar($id, $Isbn, $titulo, $autor, $generoId, $editora)
{
    $campos = [];
    $params = [];

    if ($Isbn !== null) {
        $campos[] = "livISBN = :Isbn";
        $params[':Isbn'] = $Isbn;
    }
    if ($titulo !== null) {
        $campos[] = "livTitulo = :titulo";
        $params[':titulo'] = $titulo;
    }
    if ($autor !== null) {
        $campos[] = "livAutor = :autor";
        $params[':autor'] = $autor;
    }
    if ($generoId !== null) {
        $campos[] = "fkGenId = :generoId";
        $params[':generoId'] = $generoId;
    }
    if ($editora !== null) {
        $campos[] = "livEditora = :editora";
        $params[':editora'] = $editora;
    }

    
    $query = "UPDATE tblivros SET " . implode(", ", $campos) . " WHERE livId = :id";
        $stmt = $this->db->prepare($query);

        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor);
        }

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

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

    /**
     * Método responsável por pesquisar livro pelo ISBN.
     * @param string $isbn
     * @return bool
     */
    public function buscaIsbn($isbn)
    {
        $query = "SELECT * FROM tblivros WHERE livIsbn = :isbn";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":isbn", $isbn);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Método responsável por pesquisar livro já cadastrado.
     * @param string $id
     * @param string $titulo
     * @param string $autor
     * @param string $editora
     * @return bool
     */
   public function buscaLivro($titulo, $autor, $editora)
{
    $query = "SELECT livTitulo, livAutor, livEditora FROM tblivros WHERE livTitulo = :titulo AND livAutor = :autor AND livEditora = :editora";

    $stmt = $this->db->prepare($query);
    $stmt->execute([
        ':titulo' => $titulo,
        ':autor' => $autor,
        ':editora' => $editora
    ]);

    // Retorna true se encontrar pelo menos 1 livro
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}


}
