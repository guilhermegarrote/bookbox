<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/LivroModel.php';
require_once __DIR__ . '/../Models/GeneroModel.php';
require_once __DIR__ . '/../Models/ExemplarModel.php';
require_once __DIR__ . '/../Utils/utils.php';

class LivroController
{
    private $LivroModel;
    private $GeneroModel;
    private $ExemplarModel;

    /**
     * Construtor da classe, responsável por inicializar os modelos e estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct()
    {
        $db = Database::conectar();
        $this->LivroModel = new LivroModel($db);
        $this->GeneroModel = new GeneroModel($db);
        $this->ExemplarModel = new ExemplarModel($db);
    }

    /**
     * Método responsável por cadastrar livro.
     * @return void
     */
    public function cadastrar()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(["erro" => "Dados inválidos."]);
            http_response_code(400);
            exit();
        }

        $Isbn = isset($data['isbn']) ? trim($data['isbn']) : '';
        $titulo = isset($data['titulo']) ? trim($data['titulo']) : '';
        $autor = isset($data['autor']) ? trim($data['autor']) : '';
        $genero = isset($data['genero']) ? trim($data['genero']) : '';
        $editora = isset($data['editora']) ? trim($data['editora']) : '';

        $erros = [];

        if (empty($Isbn)) {
            $erros['Isbn'] = 'O código ISBN é obrigatório.';
        } elseif (!$this->validarEstruturaIsbn($Isbn)) {
            $erros['Isbn'] = 'O código ISBN fornecido não é válido. Por favor, tente novamente.';
        }

        if (empty($titulo)) {
            $erros['titulo'] = 'O título é obrigatório.';
        } elseif (!$this->validarEstruturaTitulo($titulo)) {
            $erros['titulo'] = 'O título fornecido não é válido. Por favor, tente novamente.';
        }

        if (empty($autor)) {
            $erros['autor'] = 'O nome do autor é obrigatório.';
        } elseif (!$this->validarEstruturaAutor($autor)) {
            $erros['autor'] = 'O nome do autor fornecido não é válido. Por favor, tente novamente.';
        }

        if (empty($genero)) {
            $erros['genero'] = 'O gênero é obrigatório.';
        }

        if (empty($editora)) {
            $erros['editora'] = 'A editora é obrigatória.';
        } elseif (!$this->validarEstruturaEditora($editora)) {
            $erros['editora'] = 'A editora fornecida não é válida. Por favor, tente novamente.';
        }

        if (!empty($erros)) {
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        $genero = $this->GeneroModel->buscaGenero($genero);
        if (!$genero) {
            $erros['genero'] = 'O gênero informado não está cadastrado. Por favor, verifique.';
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        $livroExiste = $this->LivroModel->buscaLivro($titulo, $autor, $editora);
        if ($livroExiste) {
            $erros['isbn'] = 'O livro informado já está cadastrado. Por favor, verifique.';
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        $cadastroDeuCerto = $this->LivroModel->cadastrar($Isbn, $titulo, $autor, $genero['genId'], $editora);
        if ($cadastroDeuCerto) {
            echo json_encode([
                "mensagem" => "Livro cadastrado com sucesso!",
                "redirecionar" => "/bookbox/livros"
            ]);
            exit();
        } else {
            $erros['geral'] = 'Erro ao cadastrar livro. Tente novamente.';
            echo json_encode(["erro" => $erros]);
            http_response_code(500);
            exit();
        }
    }

    /**
     * Método responsável por editar livro.
     * @return void
     */
    public function editar()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(["erro" => "Dados inválidos."]);
            http_response_code(400);
            exit();
        }

        $id = $data['id'] ?? null;
        $Isbn = $data['Isbn'] ?? null;
        $titulo = $data['titulo'] ?? null;
        $autor = $data['autor'] ?? null;
        $genero = $data['genero'] ?? null;
        $editora = $data['editora'] ?? null;


        $erros = [];

        if (empty($id)) {
            $erros['id'] = 'O ID do livro é obrigatório.';
        }

        if ($Isbn !== null && !$this->validarEstruturaIsbn($Isbn)) {
            $erros['Isbn'] = 'O ISBN fornecido não é válido. Por favor, tente novamente.';
        }

        if ($titulo !== null && !$this->validarEstruturaTitulo($titulo)) {
            $erros['titulo'] = 'O título fornecido não é válido. Por favor, tente novamente.';
        }

        if ($autor !== null && !$this->validarEstruturaAutor($autor)) {
            $erros['autor'] = 'O Autor fornecido não é válido. Por favor, tente novamente.';
        }

        $genero = $this->GeneroModel->buscaGenero($genero);
        if (!$genero) {
            $erros['genero'] = 'O gênero informado não está cadastrado. Por favor, verifique.';
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        if ($editora !== null && !$this->validarEstruturaEditora($editora)) {
            $erros['editora'] = 'A editora fornecida não é válida. Por favor, tente novamente.';
        }


        if (!empty($erros)) {
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        $sucesso = $this->LivroModel->editar($id, $Isbn, $titulo, $autor, $genero['genId'], $editora);
        if ($sucesso) {
            echo json_encode([
                "mensagem" => "Livro atualizado com sucesso!",
                "redirecionar" => "/bookbox/genero"
            ]);
            exit();
        } else {
            $erros['geral'] = 'Erro ao atualizar os dados do livro. Tente novamente.';
            echo json_encode(["erro" => $erros]);
            http_response_code(500);
            exit();
        }
    }

    /**
     * Método responsável por verificar se o ISBN segue os requisitos mínimos.
     * @param string $Isbn
     * @return boolean
     */
    private static function validarEstruturaIsbn($Isbn)
    {
        if (preg_match("/^(?:\d{9}[\dX]|\d{13})$/", trim($Isbn))) {
            return true;
        }

        return false;
    }

    /**
     * Método responsável por verificar se o titulo segue os requisitos mínimos.
     * @param string $titulo
     * @return boolean
     */
    private static function validarEstruturaTitulo($titulo)
    {
        $titulo = trim($titulo);
        if (preg_match("/^[\p{L}\p{N}\p{P}\p{Zs}]+$/u", $titulo)) {
            return true;
        }
        return false;
    }

   /**
 * Método responsável por verificar se o nome do autor segue os requisitos mínimos.
 * @param string $autor
 * @return boolean
 */
private static function validarEstruturaAutor($autor)
{
    $autor = trim($autor);
    if (preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ'\-\. ]+$/", $autor)) {
        $palavras = array_filter(explode(" ", $autor));
        if (count($palavras) >= 2) {
            return true;
        }
    }

    return false;
}


    /**
     * Método responsável por verificar se o nome da editora segue os requisitos mínimos.
     * @param string $editora
     * @return boolean
     */
    private static function validarEstruturaEditora($editora)
    {

        if (preg_match("/^[\p{L}0-9\s\.\-&]+$/u", trim($editora))) {
            return true;
        }

        return false;
    }
}
