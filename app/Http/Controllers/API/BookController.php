<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Helpers\Validators;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\BookStoreRequest;
use App\Http\Requests\Book\BookUpdateRequest;
use App\Http\Requests\Copy\CopyStoreRequest;
use App\Models\Book;
use App\Models\View\Book as ViewBook;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            $query = ViewBook::query();

            $query->when($request->filled('isbn'), callback: fn($q) => $q->where('isbn', $request->isbn));
            $query->when($request->filled('title'), fn($q) => $q->where('title', $request->title));
            $query->when($request->filled('publisher'), fn($q) => $q->where('publisher', $request->publisher));


            if ($request->filled('search')) {
                $search = trim($request->search);
                $numericSearch = preg_replace('/\D/', '', $search);

                $searchIsbn = Validators::validateIsbn($search);
   

                $query->when($searchIsbn, fn($q) => $q->where('isbn' ( $search, true)))
                    ->unless(
                        $searchIsbn,
                        fn($q) => $q->where('name', 'like', "%{$search}%")
                    );
            }

            $sortable = ['name', 'formatted_class_name'];
            $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'name';
            $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
            $perPage = max(5, min((int) $request->input('perPage', 10), 250));

            $books = $query
                ->select([
                    'isbn',
                    'title',
                    'author',
                    'genre_id',
                    'publisher',
                ])
                ->orderBy($sort, $direction)
                ->paginate($perPage)
                ->appends($request->all());

            $html = view('pages.books.partials.table', compact('books'))->render();
            $paginationHtml = view('vendor.pagination.custom', ['paginator' => $books])->render();

            return response()->json([
                'html' => $html,
                'paginationHtml' => $paginationHtml,
            ]);
        } catch (Throwable $e) {
            $this->logError('Erro ao listar livros.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar os livros.');
        }
    }
    

    public function store(BookStoreRequest $request, CopyStoreRequest $copyStoreRequest): JsonResponse
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

            $duplicateTitleAuthorPublisher = Book::where('title', $bookData['title'])
                ->where('author', $bookData['author'])
                ->where('publisher', $bookData['publisher'])
                ->exists();

            if ($duplicateTitleAuthorPublisher) {
                return $this->conflictResponse(['book' => 'Já existe um livro com este título, autor e editora.']);
            }

            $numberOfCopies =  (int) $data['numberOfCopies'];

            DB::beginTransaction();

            $book = Book::create($bookData);

            $copyStoreRequest->storeCopies($book->id, $numberOfCopies);


            DB::commit();

            return $this->createdResponse();
        } catch (Throwable $e) {
            DB::rollBack();
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

            $duplicateTitleAuthorPublisher = Book::where('title', $updatedData['title'])
                ->where('author', $updatedData['author'])
                ->where('publisher', $updatedData['publisher'])
                ->where('id', '!=', $binaryId)
                ->exists();

            if ($duplicateTitleAuthorPublisher) {
                return $this->conflictResponse(['book' => 'Já existe um livro com este título, autor e editora.']);
            }

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
