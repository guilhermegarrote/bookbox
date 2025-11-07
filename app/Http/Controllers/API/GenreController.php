<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Genre\GenreStoreRequest;
use App\Http\Requests\Genre\GenreUpdateRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

/**
 * Controller responsible for managing genres (CRUD operations).
 *
 * Provides endpoints to list, create, retrieve, update, and delete books
 */
class GenreController extends Controller
{
    /**
     * Display a paginated list of all genres.
     *
     * @return JsonResponse a JSON response containing paginated genres or an error message
     */
    public function index(): JsonResponse
    {
        try {
            $genres = Genre::orderBy('name')->paginate(10);

            return $this->successResponse($genres->toArray());
        } catch (\Throwable $e) {
            $this->logError('Erro ao listar gêneros.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao listar os gêneros.');
        }
    }

    /**
     * Store a newly created genre in the database.
     *
     * @param GenreStoreRequest $request the validated request containing 'name' and 'color_hex' fields
     *
     * @return JsonResponse a response with 201 Created on success or 500 Internal Error on failure
     */
    public function store(GenreStoreRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'color_hex']);
            Genre::create($data);

            return $this->createdResponse();
        } catch (\Throwable $e) {
            $this->logError('Erro ao cadastrar gênero.', $e, ['data' => $request->all()]);

            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar gênero.');
        }
    }

    /**
     * Display a specific genre by its UUID.
     *
     * The UUID is first converted to binary using {@see Utils::convertUuidToBinary()} before lookup.
     *
     * @param string $id the UUID of the genre to retrieve
     *
     * @return JsonResponse the genre data or an appropriate error response
     */
    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $genre = Genre::with('books')->findOrFail($binaryId);

            return $this->successResponse($genre->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Gênero não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar gênero.', $e, ['genre_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao buscar gênero.');
        }
    }

    /**
     * Update a genre record.
     *
     * Applies partial updates using only non-null and non-empty fields from the validated request.
     *
     * @param GenreUpdateRequest $request the validated request data
     * @param string $id the UUID of the genre to update
     *
     * @return JsonResponse 204 No Content on success, or an error response
     */
    public function update(GenreUpdateRequest $request, string $id): JsonResponse
    {
        $data = array_filter($request->validated(), static fn ($v) => $v !== null && $v !== '');

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $genre = Genre::findOrFail($binaryId);

            $genre->update($data);

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Gênero não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao atualizar gênero.', $e, [
                'genre_id' => $id,
                'data' => $data,
            ]);

            return $this->internalErrorResponse($e, 'Erro interno ao atualizar gênero.');
        }
    }

    /**
     * Remove a genre from the database.
     *
     * Before deleting, checks if any {@see Book} is linked to the genre.
     * If so, returns a conflict response.
     *
     * @param string $id the UUID of the genre to delete
     *
     * @return JsonResponse a response indicating success or the nature of the error
     */
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
        } catch (\Throwable $e) {
            $this->logError('Erro ao excluir gênero.', $e, ['genre_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao excluir gênero.');
        }
    }
}
