<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/GeneroModel.php';
require_once __DIR__ . '/../Utils/utils.php';

class GeneroController
{
    private $GeneroModel;

    /**
     * Construtor da classe, responsável por inicializar o modelo de gênero e estabelecer a conexão com o banco de dados.
     * @return void
     */
    public function __construct()
    {
        $db = Database::conectar();
        $this->GeneroModel = new GeneroModel($db);
    }

    /**
     * Método responsável por cadastrar gênero.
     * @return void
     */
    public function cadastro()
    {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nome = $_POST['nome'];
                $cor = $_POST['cor'];

            $erros = [];

            if (!$this->validarEstruturaNome($nome)) {
                $erros['nome'] = 'O nome não é válido. Por favor, tente novamente.';
            }

            if (!$this->validarEstruturaCor($cor)) {
                $erros['senha'] = 'Essa estrutura de cor não é válida. Por favor, tente novamente.';
            }

            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $generoExiste = $this->GeneroModel->buscaGenero($nome);
            if (!$generoExiste) {
                $erros['nome'] = 'O gênero informado já está cadastrado. Por favor, verifique.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $cadastroDeuCerto = $this->GeneroModel->cadastrar($nome, $cor);

            if ($cadastroDeuCerto) {
                echo json_encode([
                    "mensagem" => "Gênero cadastrado com sucesso!",
                    "redirecionar" => "/bookbox/genero"
                ]);
                exit();
            } else {
                echo json_encode(["erro" => "Erro ao cadastrar gênero. Tente novamente."]);
                http_response_code(500);
                exit();
            }
        }
    }

    /**
     * Método responsável por editar gênero.
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
        $nome = $data['nome'] ?? null;
        $cor = $data['cor'] ?? null;

        $erros = [];

        if (empty($id)) {
            $erros['id'] = 'O ID do gênero é obrigatório.';
        }

        if ($nome !== null && !$this->validarEstruturaNome($nome)) {
            $erros['nome'] = 'O gênero fornecido não é válido. Por favor, tente novamente.';
        }

        if ($cor !== null && !$this->validarEstruturaCor($cor)) {
            $erros['cor'] = 'A cor fornecida não é válida. Por favor, tente novamente.';
        }


        if (!empty($erros)) {
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        $sucesso = $this->GeneroModel->editar($id, $nome, $cor);
        if ($sucesso) {
            echo json_encode([
                "mensagem" => "Gênero atualizado com sucesso!",
                "redirecionar" => "/bookbox/genero"
            ]);
            exit();
        } else {
            $erros['geral'] = 'Erro ao atualizar o gênero. Tente novamente.';
            echo json_encode(["erro" => $erros]);
            http_response_code(500);
            exit();
        }
    }

    /**
     * Método responsável por verificar se o nome segue os requisitos mínimos.
     * @param string $nome
     * @return boolean
     */
    private static function validarEstruturaNome($nome)
    {
        if (preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/", trim($nome))) {
            $palavras = explode(" ", trim($nome));
            if (count($palavras) >= 2 && strlen($nome) >= 3 && strlen($nome) <= 100) {
                return true;
            }
        }

        return false;
    }

    /**
     * Método responsável por verificar se a cor segue os requisitos mínimos.
     * @param string $cor
     * @return boolean
     */
    private static function validarEstruturaCor($cor)
    {
        if (preg_match("/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/", trim($cor))) {
            return true;
        }

        return false;
    }


}
