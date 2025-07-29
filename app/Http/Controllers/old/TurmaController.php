<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/TurmaModel.php';
require_once __DIR__ . '/../Utils/utils.php';

class TurmaController
{
    private $turmaModel;

    public function __construct()
    {
        $this->turmaModel = new TurmaModel(Database::conectar());
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

        $curso = trim($data['curso'] ?? '');
        $regime = trim($data['regime'] ?? '');
        $dataInicio = trim($data['dataInicio'] ?? '');
        $dataFim = trim($data['dataFim'] ?? '');

        $erros = [];

        if (!$this->validarEstruturaCurso($curso)) {
            $erros['curso'] = 'O nome do curso fornecido não é válido. Por favor, tente novamente.';
        }

        if (!in_array($regime, ['Anual', 'Semestral'])) {
            $erros['regime'] = 'O regime fornecido não é válido. Por favor, selecione Anual ou Semestral.';
        }

        $inicioValido = $this->validarData($dataInicio);
        $fimValido = $this->validarData($dataFim);

        if (!$inicioValido) {
            $erros['dataInicio'] = 'A data de início fornecida não é válida. Use o formato AAAA-MM-DD.';
        }

        if (!$fimValido) {
            $erros['dataFim'] = 'A data de fim fornecida não é válida. Use o formato AAAA-MM-DD.';
        }

        if ($inicioValido && $fimValido) {
            if (!$this->validarOrdemDatas($dataInicio, $dataFim)) {
                $erros['dataFim'] = 'A data de fim não pode ser anterior à data de início.';
            } elseif (!$this->validarIntervaloRegime($dataInicio, $dataFim, $regime)) {
                $erros['dataFim'] = 'A data de fim deve ter no mínimo um período completo e no máximo 10 anos de duração.';
            } 
        }

        if (!empty($erros)) {
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        if ($this->turmaModel->buscarTurma(null, $curso, $regime, $dataInicio, $dataFim, null)) {
            echo json_encode(["erro" => "Já existe uma turma cadastrada com esses dados."]);
            http_response_code(409);
            exit();
        }

        if ($this->turmaModel->cadastrar($curso, $regime, $dataInicio, $dataFim)) {
            echo json_encode([
                "mensagem" => "Turma cadastrada com sucesso!",
                "redirecionar" => "/bookbox/turmas"
            ]);
            exit();
        } else {
            echo json_encode(["erro" => 'Erro ao cadastrar a turma. Tente novamente.']);
            http_response_code(500);
            exit();
        }
    }

    /**
     * Método responsável por editar os dados de uma turma.
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

        $id = isset($data['id']) ? trim($data['id']) : null;
        $curso = isset($data['curso']) ? trim($data['curso']) : null;
        $horario = isset($data['horario']) ? trim($data['horario']) : null;
        $regime = isset($data['regime']) ? trim($data['regime']) : null;
        $dataInicio = isset($data['dataInicio']) ? trim($data['dataInicio']) : null;
        $dataFim = isset($data['dataFim']) ? trim($data['dataFim']) : null;

        $erros = [];

        if (empty($id)) {
            $erros['id'] = 'O ID da turma é obrigatório.';
        } else {
            $turmaAtual = $this->turmaModel->buscarPorId($id);
            if (!$turmaAtual) {
                $erros['id'] = 'Turma não encontrada.';
            }
        }

        if (!empty($curso) && !$this->validarEstruturaCurso($curso)) {
            $erros['curso'] = 'O nome do curso fornecido não é válido. Por favor, tente novamente.';
        }

        if (!empty($horario) && !in_array($horario, ['Matutino', 'Vespertino', 'Noturno'])) {
            $erros['horario'] = 'O horário fornecido não é válido. Por favor, selecione Matutino, Vespertino ou Noturno.';
        }

        if (!empty($regime) && !in_array($regime, ['Anual', 'Semestral'])) {
            $erros['regime'] = 'O regime fornecido não é válido. Por favor, selecione Anual ou Semestral.';
        }

        if (!empty($dataInicio) && !$this->validarData($dataInicio)) {
            $erros['dataInicio'] = 'A data de início fornecida não é válida. Use o formato AAAA-MM-DD.';
        }

        if (!empty($dataFim) && !$this->validarData($dataFim)) {
            $erros['dataFim'] = 'A data de fim fornecida não é válida. Use o formato AAAA-MM-DD.';
        }


        if (empty($erros) && ($dataInicio !== null || $dataFim !== null || $regime !== null)) {

            if (!$turmaAtual) {
                $turmaAtual = $this->turmaModel->buscarPorId($id);
            }

            if ($dataInicio === null) {
                $dataInicio = $turmaAtual['turDataInicio'] ?? null;
            }

            if ($dataFim === null) {
                $dataFim = $turmaAtual['turDataFim'] ?? null;
            }

            if ($regime === null) {
                $regime = $turmaAtual['turRegime'] ?? null;
            }

            if ($dataInicio && $dataFim && $regime) {
                if (!$this->validarOrdemDatas($dataInicio, $dataFim)) {
                    $erros['dataFim'] = 'A data de fim não pode ser anterior à data de início.';
                } elseif (!$this->validarIntervaloRegime($dataInicio, $dataFim, $regime)) {
                    $erros['dataFim'] = $regime === 'Anual'
                        ? 'Para regime Anual, a data de fim deve ser pelo menos 1 ano após a data de início.'
                        : 'Para regime Semestral, a data de fim deve ser pelo menos 6 meses após a data de início.';
                }
            }
        }


        if (!empty($erros)) {
            echo json_encode(["erro" => $erros]);
            http_response_code(400);
            exit();
        }

        $atualizacao = $this->turmaModel->editar($id, $curso, $horario, $regime, $dataInicio, $dataFim);

        if ($atualizacao) {
            echo json_encode([
                "mensagem" => "Turma atualizada com sucesso!",
                "redirecionar" => "/bookbox/turmas"
            ]);
            exit();
        } else {
            echo json_encode(["erro" => "Erro ao atualizar a turma. Tente novamente."]);
            http_response_code(500);
            exit();
        }
    }

    private static function validarOrdemDatas($dataInicio, $dataFim)
    {
        $inicio = new DateTime($dataInicio);
        $fim = new DateTime($dataFim);
        return $fim >= $inicio;
    }

    private static function validarIntervaloRegime($dataInicio, $dataFim, $regime)
    {
        $inicio = new DateTime($dataInicio);
        $fim = new DateTime($dataFim);
        $intervalo = $inicio->diff($fim);

        if ($regime === 'Anual') {
            return $intervalo->y >= 1 && $intervalo->y <= 10;
        } elseif ($regime === 'Semestral') {
            if ($intervalo->y > 10 || ($intervalo->y === 10 && $intervalo->m > 0)) {
                return false;
            }
            return $intervalo->y > 0 || $intervalo->m >= 6;
        }

        return false;
    }

    public function validarEstruturaCurso($curso)
    {
        return preg_match('/^[\p{L}\s]{3,100}$/u', trim($curso));
    }

    private static function validarData($data)
    {
        $dataObj = DateTime::createFromFormat('Y-m-d', $data);
        return $dataObj && $dataObj->format('Y-m-d') === $data;
    }
}
