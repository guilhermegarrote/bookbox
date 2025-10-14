<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Genre\GenreStoreRequest;
use App\Http\Requests\Genre\GenreUpdateRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Throwable;

class GenreController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $genres = Genre::orderBy('name')->paginate(10);

            return $this->successResponse($genres->toArray());
        } catch (Throwable $e) {
            $this->logError('Erro ao listar gêneros.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar os gêneros.');
        }
    }

    public function store(GenreStoreRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'color_hex']);
            Genre::create($data);
            return $this->createdResponse();
        } catch (Throwable $e) {
            $this->logError('Erro ao cadastrar gênero.', $e, ['data' => $request->all()]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar gênero.');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $genre = Genre::with('books')->findOrFail($binaryId);

            return $this->successResponse($genre->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Gênero não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao buscar gênero.', $e, ['genre_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar gênero.');
        }
    }

    public function update(GenreUpdateRequest $request, string $id): JsonResponse
    {
        $data = array_filter($request->validated(), fn($v) => $v !== null && $v !== '');

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $genre = Genre::findOrFail($binaryId);

            $genre->update($data);

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Gênero não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao atualizar gênero.', $e, [
                'genre_id' => $id,
                'data' => $data,
            ]);
            return $this->internalErrorResponse($e, 'Erro interno ao atualizar gênero.');
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $genre = Genre::findOrFail($binaryId);

            $relatedBooks = Book::where('genre_id', $binaryId)->exists();

            if ($relatedBooks) {
                return $this->conflictResponse(['books' => 'Existem livros usando esse gênero.']);
            }

            $genre->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Gênero não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao excluir gênero.', $e, ['genre_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao excluir gênero.');
        }
    }
}
