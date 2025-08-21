<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\BookStoreRequest;
use App\Http\Requests\Book\BookUpdateRequest;
use App\Models\Book;
use App\Models\View\Book as ViewBook;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookController extends Controller
{
    public function index()
    {
        //
    }

    public function store(BookStoreRequest $request): JsonResponse
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

            // Não pode existir um livro já cadastrado com o mesmo title, author and publisher. Validar isso

            // Novo: cadastrar a quantidade de exemplares que forem passados no parametro numberOfCopies pelo JSON, validar pois não pode ser mais de 32767

            Book::create($bookData);

            return $this->createdResponse();
        } catch (Throwable $e) {
            $this->logError('Erro ao cadastrar livro.', $e, ['data' => $data]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar livro.');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $book = ViewBook::findOrFail($binaryId);

            return $this->successResponse($book->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao buscar livro.', $e, ['book_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar livro.');
        }
    }

    public function update(BookUpdateRequest $request, string $id): JsonResponse
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

            // Não pode existir um livro já cadastrado com o mesmo title, author and publisher. Validar isso

            $book->update($updatedData);

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao atualizar livro.', $e, [
                'book_id' => $id,
                'data' => $data,
            ]);
            return $this->internalErrorResponse($e, 'Erro interno ao atualizar livro.');
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $book = Book::findOrFail($binaryId);

            $hasActiveCopies = DB::table('copies')
                ->where('book_id', $binaryId)
                ->where('available', false)
                ->exists();

            if ($hasActiveCopies) {
                return $this->validationErrorResponse([
                    'book' => 'Não é possível excluir o livro, pois há empréstimos ativos vinculados.'
                ]);
            }

            $book->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao excluir livro.', $e, ['book_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao excluir livro.');
        }
    }
}
