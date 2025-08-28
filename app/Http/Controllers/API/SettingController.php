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
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

            /* Pesquisar e implementar uma forma de validação flexivel para os diferentes tipos de configurações. Por pertencerem a mesma entidade (Settings), não podemos colocar uma validação fixa como valor máximo da configuração 500, 10, etc; pois cada configuração tem um limite e minimo diferente.
             */

            $setting->update($data);

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
