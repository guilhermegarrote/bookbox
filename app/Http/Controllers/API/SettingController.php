<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class SettingController extends Controller
{
    /**
     * Display a listing of settings.
     */
    public function index(): JsonResponse
    {
        try {
            $settings = Setting::orderBy('key')->paginate(10);

            return $this->successResponse($settings->toArray());
        } catch (Throwable $e) {
            $this->logError('Erro ao listar configurações.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar as configurações.');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $setting = Setting::findOrFail($binaryId);

            return $this->successResponse($setting->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Configuração não encontrada.');
        } catch (Throwable $e) {
            $this->logError('Erro ao buscar configuração.', $e, ['setting_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar configuração.');
        }
    }

    public function update(SettingUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $binaryId = Utils::convertUuidToBinary($id);

            $setting = Setting::findOrFail($binaryId);

            $rules = config('settings');

            $key = $setting->key;

            if (!isset($rules[$key])) {
                return response()->json(['error' => 'Configuração inválida para validação.'], 400);
            }

            $rule = $rules[$key];
            $value = $data['value'] ?? null;

            if ($value === null) {
                return response()->json(['error' => 'O valor da configuração é obrigatório.'], 400);
            }

            switch ($rule['type']) {
                case 'integer':
                    if (!ctype_digit($value)) {
                        return response()->json(['error' => "O valor para {$key} deve ser um número inteiro."], 400);
                    }
                    $intValue = (int) $value;
                    if (isset($rule['min']) && $intValue < $rule['min']) {
                        return response()->json(['error' => "O valor mínimo para {$key} é {$rule['min']}."], 400);
                    }
                    if (isset($rule['max']) && $intValue > $rule['max']) {
                        return response()->json(['error' => "O valor máximo para {$key} é {$rule['max']}."], 400);
                    }
                    break;

                case 'string':
                    if (!is_string($value)) {
                        return response()->json(['error' => "O valor para {$key} deve ser uma string."], 400);
                    }
                    if (isset($rule['max']) && mb_strlen($value) > $rule['max']) {
                        return response()->json(['error' => "O valor máximo para {$key} é {$rule['max']} caracteres."], 400);
                    }
                    break;

                default:
                    return response()->json(['error' => 'Tipo de configuração inválido.'], 400);
            }

            $setting->update(['value' => $value]);

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Configuração não encontrada.');
        } catch (Throwable $e) {
            $this->logError('Erro ao atualizar configuração.', $e, [
                'setting_id' => $id,
                'data' => $data,
            ]);
            return $this->internalErrorResponse($e, 'Erro interno ao atualizar configuração.');
        }
    }
}
