<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/LivroModel.php';
require_once __DIR__ . '/../Models/GeneroModel.php';
require_once __DIR__ . '/../Models/ExemplarModel.php';
require_once __DIR__ . '/../Utils/utils.php';

class LivroController
{
    private $livroModel;
    private $generoModel;
    private $exemplarModel;

    /**
     * Construtor da classe, responsável por inicializar os modelos e estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct()
    {
        $db = Database::conectar();
        $this->livroModel = new LivroModel($db);
        $this->generoModel = new GeneroModel($db);
        $this->exemplarModel = new ExemplarModel($db);
    }

    /**
     * Método responsável por cadastrar aluno.
     * @return void
     */
    public function cadastro() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $Ibsn = $_POST['Ibsn'];
            $titulo = $_POST['titulo'];
            $autor = $_POST['autor'];
            $generoId = $_POST['generoId'];
            $editora = $_POST['editora'];

            $erros = [];

            if (empty($Ibsn)) {
                $erros['Ibsn'] = 'O código IBSN é obrigatório.';
            } elseif (!$this->validarEstruturaIbsn($Ibsn)) {
                $erros['Ibsn'] = 'O código IBSN fornecido não é válido. Por favor, tente novamente.';
            }

           if (empty($titulo)) {
                $erros['titulo'] = 'O título é obrigatório.';
             }elseif (!$this->validarEstruturaTitulo($titulo)) {
                $erros['titulo'] = 'O título fornecido não é válido. Por favor, tente novamente.';
            }

            if (empty($autor)) {
                $erros['autor'] = 'O nome do autor é obrigatório.';
            }elseif (!$this->validarEstruturaAutor($autor)) {
                $erros['autor'] = 'O nome do autor fornecido não é válido. Por favor, tente novamente.';
            }

            if (empty($generoId)) {
                $erros['generoId'] = 'O gênero é obrigatório.';
            }

            if (empty($editora)) {
                $erros['editora'] = 'A editora é obrigatória.';
            }elseif (!$this->validarEstruturaEditora($editora)) {
                $erros['editora'] = 'A editora fornecida não é válida. Por favor, tente novamente.';
            }

            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $ibsnExiste = $this->livroModel->buscaIbsn($Ibsn);
            if (!$ibsnExiste) {
                $erros['Ibsn'] = 'O IBSN informado não está cadastrado. Por favor, verifique e tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $livroExiste = $this->livroModel->buscaLivro($titulo, $autor, $editora);
            if (!$livroExiste) {
                $erros['Ibsn'] = 'O livro informado já está cadastrado. Por favor, verifique.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $cadastroDeuCerto = $this->livroModel->cadastrar($Ibsn, $titulo, $autor, $generoId, $editora);
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
        } else {
            require_once __DIR__ . '/../resources/views/alunos/cadastro.php';
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
        $Ibsn = $data['Ibsn'] ?? null;
        $titulo = $data['titulo'] ?? null;
        $autor = $data['autor'] ?? null;
        $generoId = $data['generoId'] ?? null;
        $editora = $data['editora'] ?? null;
        

        $erros = [];

        if (empty($id)) {
            $erros['id'] = 'O ID do livro é obrigatório.';
        }

        if ($Ibsn !== null && !$this->validarEstruturaIbsn($Ibsn)) {
            $erros['Ibsn'] = 'O IBSN fornecido não é válido. Por favor, tente novamente.';
        }

        if ($titulo !== null && !$this->validarEstruturaTitulo($titulo)) {
            $erros['titulo'] = 'O título fornecido não é válido. Por favor, tente novamente.';
        }

        if ($autor !== null && !$this->validarEstruturaAutor($autor)) {
            $erros['autor'] = 'O Autor fornecido não é válido. Por favor, tente novamente.';
        }

        if ($generoId !== null && !$this->$generoId) {
            $erros['generoId'] = 'O gênero fornecido não é válido. Por favor, tente novamente.';
        }

        if ($editora !== null && !$this->validarEstruturaEditora($editora)) {
            $erros['editora'] = 'A editora fornecida não é válida. Por favor, tente novamente.';
        }


        if (!empty($erros)) {
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        $sucesso = $this->livroModel->editar($id, $Ibsn, $titulo, $autor, $generoId, $editora);
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
     * Método responsável por verificar se o IBSN segue os requisitos mínimos.
     * @param string $Ibsn
     * @return boolean
     */
    private static function validarEstruturaIbsn($Ibsn)
    {
        if (preg_match("/^(?:\d{9}[\dX]|\d{13})$/", trim($Ibsn))) {
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
        if (preg_match("/^[\p{L}\p{N}\p{P}\p{Zs}]+$/", trim($titulo))) {
            $palavras = explode(" ", trim($titulo));
            if (count($palavras) >= 2 && strlen($titulo) >= 3 && strlen($titulo) <= 255) {
                return true;
            }
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
        if (preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/", trim($autor))) {
            $palavras = explode(" ", trim($autor));
            if (count($palavras) >= 2 && strlen($autor) >= 3 && strlen($autor) <= 300) {
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
        if (preg_match("/^[A-Za-z0-9\s\.\-]+$/", trim($editora))) {
            $palavras = explode(" ", trim($editora));
            if (count($palavras) >= 2 && strlen($editora) >= 3 && strlen($editora) <= 300) {
                return true;
            }
        }

        return false;
    }

}
