<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/ExemplarModel.php';
require_once __DIR__ . '/../Models/AlunoModel.php';

class ExemplarController
{
    private $exemplarModel;

    public function __construct()
    {
        $db = Database::conectar();
        $this->exemplarModel = new ExemplarModel($db);
    }

    public function cadastrar()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(["erro" => "Dados inválidos."]);
            http_response_code(400);
            exit();
        }

        $isbn = $data['isbn'] ?? null;
        $quantidade = $data['quantidade'] ?? null;

        $erros = [];

            

            $livId = $this->exemplarModel->pesquisaLivroPorIsbn($isbn);
            if (!$livId) {
                $erros['isbn'] = 'O livro com o ISBN informado não está cadastrado. Verifique o ISBN e tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $cadastroDeuCerto = $this->exemplarModel->cadastrar($livId);
            if ($cadastroDeuCerto) {
                echo json_encode([
                    "mensagem" => "Exemplar cadastrado com sucesso!"]);
                exit();
            } else {
                $erros['geral'] = 'Erro ao cadastrar o exemplar. Tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(500);
                exit();
            }
        } 


    public function excluir()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $isbn = $_POST['isbn'];
            $exemplarId = $_POST['exemplarId'];

            $erros = [];

            $livId = $this->exemplarModel->pesquisaLivroPorIsbn($isbn);
            if (!$livId) {
                $erros['isbn'] = 'O livro com o ISBN informado não está cadastrado.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $exemplarNaoExiste = $this->exemplarModel->pesquisarExemplar($exemplarId, $livId);
            if (!$exemplarNaoExiste) {
                $erros['exemplar'] = 'O exemplar não foi encontrado.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $exclusaoDeuCerto = $this->exemplarModel->excluir($exemplarId);
            if ($exclusaoDeuCerto) {
                echo json_encode(["mensagem" => "Exemplar excluído com sucesso!"]);
                exit();
            } else {
                $erros['geral'] = 'Erro ao excluir o exemplar. Tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(500);
                exit();
            }
        } else {
            require_once __DIR__ . '/../resources/views/exemplares/excluir.php';
        }
    }

}
