<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller responsible for managing application settings (CRUD operations).
 *
 * Provides API endpoints to list, retrieve, and update system configuration values.
 * Each configuration item is validated based on rules defined in `config/settings.php`,
 * ensuring consistency and data integrity across the application.
 *
 * @see config/settings.php Defines validation rules for configurable settings.
 */
class SettingController extends Controller
{
    /**
     * Retrieve a paginated list of all application settings.
     *
     * Fetches system settings ordered by key and returns them in a paginated structure.
     *
     * @return JsonResponse JSON response containing a paginated list of settings on success,
     *                      or an internal server error on failure
     */
    public function index(): JsonResponse
    {
        try {
            $settings = Setting::orderBy('key')->paginate(10);

            return $this->successResponse($settings->toArray());
        } catch (\Throwable $e) {
            $this->logError('Error listing settings.', $e);

            return $this->internalErrorResponse($e, 'Internal error while listing settings.');
        }
    }

    /**
     * Retrieve details of a specific setting by its UUID.
     *
     * Converts the UUID to binary format, fetches the record, and returns its data.
     *
     * @param string $id the UUID of the setting as a string
     *
     * @return JsonResponse JSON response with setting data if found,
     *                      404 Not Found if the setting does not exist,
     *                      or internal error if retrieval fails
     */
    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $setting = Setting::findOrFail($binaryId);

            return $this->successResponse($setting->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Setting not found.');
        } catch (\Throwable $e) {
            $this->logError('Error fetching setting.', $e, ['setting_id' => $id]);

            return $this->internalErrorResponse($e, 'Internal error while fetching setting.');
        }
    }

    /**
     * Update the value of a specific setting, validating it based on predefined rules.
     *
     * The validation rules are dynamically loaded from `config/settings.php` and may include
     * constraints such as minimum, maximum, or type (string/integer). The new value must comply
     * with these validation rules before being persisted.
     *
     * @param Request $request the incoming request containing the new value
     * @param string $id the UUID of the setting
     *
     * @return JsonResponse JSON response indicating success (204 No Content),
     *                      validation error (400 Bad Request),
     *                      not found (404 Not Found),
     *                      or internal error (500 Internal Server Error)
     *
     * @see config/settings.php Contains validation rules for all configurable keys.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->all();

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
                    if (!ctype_digit((string) $value)) {
                        return response()->json(['error' => "O valor para {$key} deve ser um número inteiro."], 400);
                    }

                    $intValue = (int) $value;

                    if (isset($rule['min']) && $intValue < $rule['min']) {
                        return response()->json(['error' => "O valor mínimo para {$key} é {$rule['min']}."], 400);
                    }

                    if (isset($rule['max']) && $intValue > $rule['max']) {
                        return response()->json(['error' => "O valor máximo para {$key} é {$rule['max']}."], 400);
                    }

                    $value = $intValue;
                    break;
                case 'string':
                    if (!\is_string($value)) {
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
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Configuração não encontrada.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao atualizar configuração.', $e, [
                'setting_id' => $id,
                'data' => $data,
            ]);

            return $this->internalErrorResponse($e, 'Erro interno ao atualizar configuração.');
        }
    }
}
