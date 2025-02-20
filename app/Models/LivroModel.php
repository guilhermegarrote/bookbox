<?php

require_once __DIR__ . '/../../config/database.php';

class LivroModel
{
    private $db;

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
     * @param int $cod
     * @param string $titulo
     * @param string $subtitulo
     * @param string $autor
     * @param string $editora
     * @param string $exemplar
     * @param bool  $disponibilidade
     * @param string $generoId
     * @return bool
     */
    public function cadastrar($cod, $titulo, $subtitulo, $autor, $editora, $exemplar, $disponibilidade, $generoId)
    {
        $query = "INSERT INTO tblivros (livCod, livTitulo, livSubtitulo, livAutor, livEditora,
        livExemplar, livDisponibilidade, fkGeneroId) VALUES (:cod, :titulo, :subtitulo, :autor, :editora, :exemplar, 
        :disponibilidade, :generoId)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cod", $cod);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":subtitulo", $subtitulo);
        $stmt->bindParam(":autor", $autor);
        $stmt->bindParam(":editora", $editora);
        $stmt->bindParam(":exemplar", $exemplar);
        $stmt->bindParam(":disponibilidade", $disponibilidade);
        $stmt->bindParam(":generoId", $generoId);
        return $stmt->execute();
    }

    /**
     * Método responsável por editar usuário.
     * $cod, $titulo, $subtitulo,$autor,$editora, $exemplar, $disponibilidade , $generoId
     * @param int $id
     * @param int|null $cod
     * @param string|null $titulo
     * @param string|null $subtitulo
     * @param string|null $autor
     * @param string|null $editora
     * @param string|null $exemplar
     * @param bool|null $disponibilidade
     * @param string|null $generoId
     * @return bool
     */
    public function editar($id, $cod, $titulo, $subtitulo, $autor, $editora, $exemplar, $disponibilidade, $generoId)
    {
        $query = "UPDATE tblivros SET ";

        if ($cod) {
            $query .= "livCod = :cod, ";
        }
        if ($titulo) {
            $query .= "livTitulo = :titulo, ";
        }
        if ($subtitulo) {
            $query .= "livSubtitulo = :subtitulo, ";
        }
        if ($autor) {
            $query .= "livAutor = :autor, ";
        }
        if ($editora) {
            $query .= "livEditora = :editora, ";
        }
        if ($exemplar) {
            $query .= "ivExemplar = :exemplar, ";
        }
        if ($disponibilidade) {
            $query .= "livDisponibilidade = :disponibilidade, ";
        }
        if ($generoId) {
            $query .= "fkGeneroId = :generoId, ";
        }

        $query .= " WHERE livId = :id";

        $stmt = $this->db->prepare($query);

        if ($cod) {
            $stmt->bindParam(":cod", $cod);
        }
        if ($titulo) {
            $stmt->bindParam(":titulo", $titulo);
        }
        if ($subtitulo) {
            $stmt->bindParam(":subtitulo", $subtitulo);
        }
        if ($autor) {
            $stmt->bindParam(":autor", $autor);
        }
        if ($editora) {
            $stmt->bindParam(":editora", $editora);
        }
        if ($exemplar) {
            $stmt->bindParam(":exemplar", $exemplar);
        }
        if ($disponibilidade) {
            $stmt->bindParam(":disponibilidade", $disponibilidade);
        }
        if ($generoId) {
            $stmt->bindParam(":generoId", $generoId);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Método responsável por mudar a disponibilidade do livro.
     * @param int $id
     * @param bool $disponibilidade
     * @return bool
     */
    public function mudarDisponibilidade($id, $disponibilidade)
    {
        $query = "UPDATE tblivros SET livDisponibilidade = :disponibilidade WHERE livId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":disponibilidade", $disponibilidade, PDO::PARAM_BOOL);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Método responsável por excluir livro.
     * @param int $id
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