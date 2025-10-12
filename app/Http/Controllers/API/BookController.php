<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Helpers\Validators;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\BookStoreRequest;
use App\Http\Requests\Book\BookUpdateRequest;
use App\Services\CopyService;
use App\Models\Book;
use App\Models\Genre;
use App\Models\View\Book as ViewBook;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
            $query = $this->buildBookQuery($request);

            $perPage = max(5, min((int) $request->input('perPage', 10), 250));

            $books = $query->select([
                'id',
                'isbn',
                'title',
                'author',
                'genre_name',
                'publisher',
                'available_copies',
                'total_copies'
            ])->paginate($perPage)->appends($request->all());

            $html = view('pages.books.partials.table', compact('books'))->render();
            $paginationHtml = view('vendor.pagination.custom', ['paginator' => $books])->render();

            $filterData = ViewBook::getFilterData($query);

            return response()->json([
                'html' => $html,
                'paginationHtml' => $paginationHtml,
                'filterData' => $filterData
            ]);
        } catch (Throwable $e) {
            $this->logError('Erro ao listar livros.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar os livros.');
        }
    }

    public function store(BookStoreRequest $request, CopyService $copyService): JsonResponse
    {
        $bookData = $request->only(['isbn', 'title', 'author', 'publisher']);
        $numberOfCopies = (int) $request->input('numberOfCopies');

        $genre = Genre::where('name', $request->input('genre_name'))->first();

        if (!$genre) {
            return $this->validationErrorResponse(['genre_name' => 'Gênero não encontrado.']);
        }

        $bookData['genre_id'] = Utils::convertUuidToBinary($genre->id);

        try {
            DB::transaction(function () use ($bookData, $numberOfCopies, $copyService) {
                $book = Book::create($bookData);
                $copyService->storeCopies(Utils::convertUuidToBinary($book->id), $numberOfCopies);
            });

            return $this->createdResponse();
        } catch (Throwable $e) {
            $this->logError('Erro ao cadastrar livro.', $e, ['data' => $bookData]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar livro.');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $book = ViewBook::findOrFail($binaryId);

            return $this->successResponse($book->toArray());
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao buscar livro.', $e, ['book_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar livro.');
        }
    }

    public function update(BookUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['genre_name'])) {
            $genre = Genre::where('name', $data['genre_name'])->first();
            if (!$genre) {
                return $this->validationErrorResponse(['genre_name' => 'Gênero não encontrado.']);
            }
            $data['genre_id'] = Utils::convertUuidToBinary($genre->id);
        }

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
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao atualizar livro.', $e, ['book_id' => $id, 'data' => $data]);
            return $this->internalErrorResponse($e, 'Erro interno ao atualizar livro.');
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $book = Book::findOrFail($binaryId);

            $hasActiveCopies = DB::table('vw_copies')
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
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao excluir livro.', $e, ['book_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao excluir livro.');
        }
    }

    /**
     * Build book query with search, filters, and sorting.
     *
     * @param Request $request
     * @return Builder
     */
    private function buildBookQuery(Request $request): Builder
    {
        $query = ViewBook::query();

        if ($request->filled('search')) {
            $query = $this->applyBookSearch($query, $request->search);
        }

        if ($request->filled('genre_name')) {
            $query->where('genre_name', $request->genre_name);
        }

        if ($request->filled('publisher')) {
            $query->where('publisher', $request->publisher);
        }

        return $this->applyBookSorting($query, $request);
    }

    /**
     * Apply search filters for books by ISBN, title, or author.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    private function applyBookSearch($query, string $search)
    {
        $search = trim($search);
        $numericSearch = preg_replace('/\D/', '', $search);
        $isIsbn = Validators::validateIsbn($numericSearch);

        if ($isIsbn) {
            $query->where('isbn', $numericSearch);
        } else {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Apply sorting based on allowed columns.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    private function applyBookSorting($query, Request $request)
    {
        $sortable = ['title', 'author', 'genre_name', 'publisher'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'title';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sort, $direction);
    }
}
