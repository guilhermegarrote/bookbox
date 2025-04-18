<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/TurmaModel.php';
require_once __DIR__ . '/../Utils/utils.php';

class TurmaController
{
    private $turmaModel;

    public function __construct()
    {
        $db = Database::conectar();
        $this->turmaModel = new TurmaModel($db);
    }

    public function cadastrar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $curso = $_POST['curso'] ?? null;
            $horario = $_POST['horario'] ?? null;
            $regime = $_POST['regime'] ?? null;
            $dataInicio = $_POST['dataInicio'] ?? null;
            $dataFim = $_POST['dataFim'] ?? null;

            $erros = [];

            if (empty($curso)) {
                $erros['curso'] = 'O curso é obrigatório.';
            } elseif (!$this->validarEstruturaCurso($curso)) {
                $erros['curso'] = 'O nome do curso fornecido não é válido. Por favor, tente novamente.';
            }

            if (empty($horario)) {
                $erros['horario'] = 'O horário é obrigatório.';
            } elseif (!in_array($horario, ['Matutino', 'Vespertino', 'Noturno'])) {
                $erros['horario'] = 'O horário fornecido não é válido. Por favor, selecione Matutino, Vespertino ou Noturno.';
            }

            if (empty($regime)) {
                $erros['regime'] = 'O regime é obrigatório.';
            } elseif (!in_array($regime, ['Anual', 'Semestral'])) {
                $erros['regime'] = 'O regime fornecido não é válido. Por favor, selecione Anual ou Semestral.';
            }

            if (empty($dataInicio)) {
                $erros['dataInicio'] = 'A data de início é obrigatória.';
            } elseif (!$this->validarData($dataInicio)) {
                $erros['dataInicio'] = 'A data de início fornecida não é válida. Use o formato AAAA-MM-DD.';
            }

            if (empty($dataFim)) {
                $erros['dataFim'] = 'A data de fim é obrigatória.';
            } elseif (!$this->validarData($dataFim)) {
                $erros['dataFim'] = 'A data de fim fornecida não é válida. Use o formato AAAA-MM-DD.';
            }

            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $sucesso = $this->turmaModel->cadastrar($curso, $horario, $regime, $dataInicio, $dataFim);
            if ($sucesso) {
                echo json_encode([
                    "mensagem" => "Turma cadastrada com sucesso!",
                    "redirecionar" => "/bookbox/turmas"
                ]);
                exit();
            } else {
                $erros['geral'] = 'Erro ao cadastrar a turma. Tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(500);
                exit();
            }
        }
    }

    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $curso = $_POST['curso'] ?? null;
            $horario = $_POST['horario'] ?? null;
            $regime = $_POST['regime'] ?? null;
            $dataInicio = $_POST['dataInicio'] ?? null;
            $dataFim = $_POST['dataFim'] ?? null;

            $erros = [];

            if (empty($id)) {
                $erros['id'] = 'O ID da turma é obrigatório.';
            }

            if ($curso !== null && !$this->validarEstruturaCurso($curso)) {
                $erros['curso'] = 'O nome do curso fornecido não é válido. Por favor, tente novamente.';
            }

            if ($horario !== null && !in_array($horario, ['Matutino', 'Vespertino', 'Noturno'])) {
                $erros['horario'] = 'O horário fornecido não é válido. Por favor, selecione Matutino, Vespertino ou Noturno.';
            }

            if ($regime !== null && !in_array($regime, ['Anual', 'Semestral'])) {
                $erros['regime'] = 'O regime fornecido não é válido. Por favor, selecione Anual ou Semestral.';
            }

            if ($dataInicio !== null && !$this->validarData($dataInicio)) {
                $erros['dataInicio'] = 'A data de início fornecida não é válida. Use o formato AAAA-MM-DD.';
            }

            if ($dataFim !== null && !$this->validarData($dataFim)) {
                $erros['dataFim'] = 'A data de fim fornecida não é válida. Use o formato AAAA-MM-DD.';
            }

            if (!empty($erros)) {
                echo json_encode(["erro" => $erros]);
                http_response_code(400);
                exit();
            }

            $sucesso = $this->turmaModel->editar($id, $curso, $horario, $regime, $dataInicio, $dataFim);
            if ($sucesso) {
                echo json_encode([
                    "mensagem" => "Turma atualizada com sucesso!",
                    "redirecionar" => "/bookbox/turmas"
                ]);
                exit();
            } else {
                $erros['geral'] = 'Erro ao atualizar a turma. Tente novamente.';
                echo json_encode(["erro" => $erros]);
                http_response_code(500);
                exit();
            }
        }
    }

    private static function validarEstruturaCurso($curso)
    {
        return preg_match('/^[\p{L}\s]{3,100}$/u', trim($curso));
    }

    private static function validarData($data)
    {
        $dataObj = DateTime::createFromFormat('Y-m-d', $data);
        return $dataObj && $dataObj->format('Y-m-d') === $data;
    }
}
