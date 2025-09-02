<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Genre\GenreStoreRequest;
use App\Http\Requests\Genre\GenreUpdateRequest;
use App\Models\Genre;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class Genrecontroller extends Controller
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
    public function store(GenreStoreRequest $request): JsonResponse
{
    DB::beginTransaction();

    try {
        $data = $request->only(['name', 'color_hex']);

        $genre = Genre::create($data);

        DB::commit();

        return $this->createdResponse([
            'message' => 'Gênero criado com sucesso!',
            'data' => $genre,
        ]);
    } catch (Throwable $e) {
        DB::rollBack();
        $this->logError('Erro ao cadastrar gênero.', $e, ['data' => $request->all()]);
        return $this->internalErrorResponse($e, 'Erro interno ao cadastrar gênero.');
    }
}


    public function update(GenreUpdateRequest $request, string $id): JsonResponse
{
    $data = $request->validated();

    try {
        // Converte o UUID para BINARY(16) caso o banco use esse formato
        $binaryId = Utils::convertUuidToBinary($id);
        $genre = Genre::findOrFail($binaryId);

        // Remove o # do color_hex se enviado
        if (isset($data['color_hex'])) {
            $data['color_hex'] = strtoupper(preg_replace('/^#/', '', trim($data['color_hex'])));
        }

        // Mescla os dados antigos com os novos enviados
        $updatedData = array_merge(
            [
                'name' => $genre->name,
                'color_hex' => $genre->color_hex,
            ],
            array_filter(
                array_intersect_key($data, array_flip(['name', 'color_hex'])),
                fn($v) => $v !== null && $v !== ''
            )
        );

        $genre->update($updatedData);

         return $this->createdResponse();

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Gênero não encontrado.',
        ], 404);
    } catch (Throwable $e) {
        $this->logError('Erro ao atualizar gênero.', $e, [
            'genre_id' => $id,
            'data' => $data,
        ]);
        return response()->json([
            'message' => 'Erro interno ao atualizar gênero.',
        ], 500);
    }
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $genre = Genre::find($id);

        if (!$genre) {
            return response()->json([
                'message' => 'Gênero não encontrado.',
            ], 404);
        }

        $genre->delete();

        return response()->json([
            'message' => 'Gênero removido com sucesso!',
        ]);
    }
}