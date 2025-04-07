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

    public function cadastro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $isbn = $_POST['isbn'];
            $numeroExemplar = $_POST['numeroExemplar'];
            $disponibilidade = $_POST['disponibilidade'];

            $erros = $this->validarDadosExemplar($isbn);

            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $livId = $this->exemplarModel->pesquisaLivroPorIsbn($isbn);
            if (!$livId) {
                $erros['isbn'] = 'O livro com o ISBN informado não está cadastrado. Verifique o ISBN e tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $exemplarExiste = $this->exemplarModel->pesquisarExemplar($numeroExemplar, $livId);
            if ($exemplarExiste) {
                $erros['exemplarId'] = 'O exemplar informado já está cadastrado.';
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
        } else {
            require_once __DIR__ . '/../resources/views/exemplares/cadastro.php';
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
                $erros['livro'] = 'O livro com o ISBN informado não está cadastrado.';
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $exemplarExiste = $this->exemplarModel->pesquisarExemplar($exemplarId, $livId);
            if (!$exemplarExiste) {
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

    private function validarDadosExemplar($isbn)
    {
        $erros = [];

        if (empty($isbn)) {
            $erros['isbn'] = 'O campo "ISBN" é obrigatório.';
        } elseif (!preg_match("/^\d{9}(\d{3})?$/", $isbn)) {
            $erros['isbn'] = 'O ISBN informado é inválido.';
        }

        if (!empty($erros)) {
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        return $erros;
    }
}
