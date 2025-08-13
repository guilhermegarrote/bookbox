<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $setting = Setting::findOrFail($binaryId);

            $updatedData = array_filter(
                array_intersect_key($data, array_flip(['key', 'value'])),
                fn($v) => $v !== null && $v !== ''
            );

            $duplicateKeyExists = Setting::where('key', $updatedData['key'] ?? $setting->key)
                ->where('id', '!=', $binaryId)
                ->exists();

            if ($duplicateKeyExists) {
                return $this->conflictResponse(['key' => 'Já existe outra configuração com esta chave.']);
            }

            $setting->update($updatedData);

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
