<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Bulk update multiple settings at once.
     *
     * The validation rules are dynamically loaded from `config/settings.php` and may include
     * constraints such as minimum, maximum, or type (string/integer). Each key/value pair in
     * the request must comply with these rules before being persisted.
     *
     * The operation is atomic: either all settings are updated, or none are.
     *
     * @param Request $request The incoming request containing an associative array of settings.
     *                        Example:
     *                        [
     *                          "default_due_days" => 10,
     *                          "extension_days" => 5,
     *                          "max_book_loans" => 3
     *                        ]
     *
     * @return JsonResponse JSON response indicating:
     *                      - 204 No Content on success
     *                      - 400 Bad Request if validation fails
     *                      - 404 Not Found if any key does not exist
     *                      - 500 Internal Server Error on unexpected errors
     *
     * @see config/settings.php Contains validation rules for all configurable keys.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $data = $request->input('settings', []);
        $rules = config('settings');

        if (empty($data)) {
            return $this->badRequestResponse(['settings' => 'Nenhuma configuração enviada.']);
        }

        DB::beginTransaction();

        try {
            foreach ($data as $key => $value) {
                if (!isset($rules[$key])) {
                    return $this->badRequestResponse([$key => 'Configuração inválida.']);
                }

                $rule = $rules[$key];

                switch ($rule['type']) {
                    case 'integer':
                        if (!ctype_digit((string) $value)) {
                            return $this->badRequestResponse([$key => "O valor para {$key} deve ser um número inteiro."]);
                        }

                        $intValue = (int) $value;

                        if (isset($rule['min']) && $intValue < $rule['min']) {
                            return $this->badRequestResponse([$key => "O valor mínimo para {$key} é {$rule['min']}."]);
                        }

                        if (isset($rule['max']) && $intValue > $rule['max']) {
                            return $this->badRequestResponse([$key => "O valor máximo para {$key} é {$rule['max']}."]);
                        }

                        $value = $intValue;
                        break;

                    case 'string':
                        if (!is_string($value)) {
                            return $this->badRequestResponse([$key => "O valor para {$key} deve ser uma string."]);
                        }

                        if (isset($rule['max']) && mb_strlen($value) > $rule['max']) {
                            return $this->badRequestResponse([$key => "O valor máximo para {$key} é {$rule['max']} caracteres."]);
                        }
                        break;

                    default:
                        return $this->badRequestResponse([$key => "Tipo inválido para {$key}."]);
                }

                $setting = Setting::where('key', $key)->first();

                if (!$setting) {
                    return $this->notFoundResponse("Configuração não encontrada: {$key}");
                }

                $setting->update(['value' => $value]);
            }

            DB::commit();
            return $this->noContentResponse();
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->logError('Erro ao atualizar configurações.', $e, ['data' => $data]);

            return response()->json(['error' => 'Erro interno ao atualizar configurações.'], 500);
        }
    }
}
