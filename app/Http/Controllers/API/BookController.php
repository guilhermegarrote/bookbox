<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\BookClassStoreRequest;
use App\Http\Requests\Book\BookClassUpdateRequest;
use App\Models\Book;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
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
    public function store(BookClassStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $bookData = array_filter(
                array_intersect_key($data, array_flip(['isbn', 'title', 'author', 'genre_id', 'publisher'])),
                fn($v) => $v !== null && $v !== ''
            );

            $bookExists = Book::where('isbn', $bookData['isbn'])->exists();

            if ($bookExists) {
                return $this->conflictResponse(['book' => 'Já existe um livro com este ISBN.']);
            }

            Book::create($bookData);

            return $this->createdResponse();
        } catch (Exception $e) {
            $this->logError('Erro ao cadastrar livro.', $e, ['data' => $data]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar livro.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $book = Book::findOrFail($binaryId);

            return $this->successResponse($book->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Exception $e) {
            $this->logError('Erro ao buscar livro.', $e, ['book_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar livro.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookClassUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $book = Book::findOrFail($binaryId);

            $updatedData = array_merge(
                [
                    'isbn' => $book->isbn,
                    'title' => $book->title,
                    'author' => $book->author,
                    'genre_id' => $book->genre_id,
                    'publisher' => $book->publisher,
                ],
                array_filter(
                    array_intersect_key($data, array_flip(['isbn', 'title', 'author', 'genre_id', 'publisher'])),
                    fn($v) => $v !== null && $v !== ''
                )
            );

            $duplicateBookExists = Book::where('isbn', $updatedData['isbn'])
                ->where('id', '!=', $binaryId)
                ->exists();

            if ($duplicateBookExists) {
                return $this->conflictResponse(['book' => 'Já existe outro livro com este ISBN.']);
            }

            $book->update($updatedData);

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Exception $e) {
            $this->logError('Erro ao atualizar livro.', $e, [
                'book_id' => $id,
                'data' => $data,
            ]);
            return $this->internalErrorResponse($e, 'Erro interno ao atualizar livro.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $book = Book::findOrFail($binaryId);

            $hasActiveLoans = DB::table('loans')
                ->where('book_id', $binaryId)
                ->where('active', true)
                ->exists();

            if ($hasActiveLoans) {
                return $this->validationErrorResponse([
                    'book' => 'Não é possível excluir o livro, pois há empréstimos ativos vinculados.'
                ]);
            }

            $book->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Exception $e) {
            $this->logError('Erro ao excluir livro.', $e, ['book_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao excluir livro.');
        }
    }
}
