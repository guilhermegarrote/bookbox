<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Helpers\Validators;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\BookStoreRequest;
use App\Http\Requests\Book\BookUpdateRequest;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Genre;
use App\Models\View\Book as ViewBook;
use App\Services\CopyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * Display a paginated list of books with optional search and filters.
     *
     * @param Request $request the HTTP request containing query parameters for search, filter, and pagination
     *
     * @return JsonResponse JSON response containing HTML table, pagination markup, and filter data
     */
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
                'total_copies',
            ])->paginate($perPage)->appends($request->all());

            $html = view('pages.books.partials.table', compact('books'))->render();
            $paginationHtml = view('vendor.pagination.custom', ['paginator' => $books])->render();
            $filterData = ViewBook::getFilterData($query);

            return response()->json([
                'html' => $html,
                'paginationHtml' => $paginationHtml,
                'filterData' => $filterData,
            ]);
        } catch (\Throwable $e) {
            $this->logError('Erro ao listar livros.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao listar livros.');
        }
    }

    /**
     * Store a new book and its copies in the database.
     *
     * @param BookStoreRequest $request validated request containing book data and number of copies
     * @param CopyService $copyService service to handle copy creation
     *
     * @return JsonResponse JSON response indicating success or failure
     */
    public function store(BookStoreRequest $request, CopyService $copyService): JsonResponse
    {
        /** @var Request $request */
        $bookData = $request->only(['isbn', 'title', 'author', 'publisher']);
        $numberCopies = (int) $request->input('number_copies');

        $genre = Genre::where('name', $request->input('genre_name'))->first();

        if (!$genre) {
            return $this->validationErrorResponse(['genre_name' => 'Gênero não encontrado.']);
        }

        $bookData['genre_id'] = Utils::convertUuidToBinary($genre->id);

        try {
            DB::transaction(function () use ($bookData, $numberCopies, $copyService) {
                $book = Book::create($bookData);
                $copyService->storeCopies(Utils::convertUuidToBinary($book->id), $numberCopies);
            });

            return $this->createdResponse();
        } catch (\Throwable $e) {
            $this->logError('Erro ao criar livro.', $e, ['data' => $bookData]);

            return $this->internalErrorResponse($e, 'Erro interno ao criar livro.');
        }
    }

    /**
     * Retrieve details of a single book by its UUID.
     *
     * @param string $id book UUID
     *
     * @return JsonResponse JSON response with book data or error message
     */
    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $book = ViewBook::findOrFail($binaryId);

            return $this->successResponse($book->toArray());
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar livro.', $e, ['book_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao buscar livro.');
        }
    }

    /**
     * Find a book by ISBN and return its available copies.
     *
     * @param string $isbn the ISBN to search for
     *
     * @return JsonResponse JSON response with book data and available copies
     */
    public function findByIsbn(string $isbn): JsonResponse
    {
        try {
            $cleanIsbn = preg_replace('/[^0-9Xx]/', '', trim($isbn));

            if (!Validators::validateIsbn($cleanIsbn)) {
                return $this->badRequestResponse(['isbn' => 'ISBN inválido.']);
            }

            $book = ViewBook::where('isbn', $cleanIsbn)->first();

            if (!$book) {
                return $this->notFoundResponse('Livro não encontrado.');
            }

            $availableCopies = Copy::where('book_id', Utils::convertUuidToBinary($book->id))
                ->whereDoesntHave('loans', fn ($query) => $query->whereNull('returned_date'))
                ->get()
                ->map(fn ($copy) => ['number' => $copy->number])
            ;

            return $this->successResponse([
                'book' => $book,
                'available_copies' => $availableCopies,
            ]);
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar livro pelo ISBN.', $e, ['isbn' => $isbn]);

            return $this->internalErrorResponse($e, 'Erro interno ao buscar livro.');
        }
    }

    /**
     * Update a book's details.
     *
     * @param BookUpdateRequest $request validated request containing updated data
     * @param string $id book UUID
     *
     * @return JsonResponse JSON response indicating success, conflict, or error
     */
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
                    fn ($v) => $v !== null && $v !== '',
                ),
            );

            $duplicate = Book::where('title', $updatedData['title'])
                ->where('author', $updatedData['author'])
                ->where('publisher', $updatedData['publisher'])
                ->where('id', '!=', $binaryId)
                ->exists()
            ;

            if ($duplicate) {
                return $this->conflictResponse(['book' => 'Já existe um livro com este título, autor e editora.']);
            }

            $book->update($updatedData);

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao atualizar livro.', $e, ['book_id' => $id, 'data' => $data]);

            return $this->internalErrorResponse($e, 'Erro interno ao atualizar livro.');
        }
    }

    /**
     * Delete a book if no active copies exist.
     *
     * @param string $id book UUID
     *
     * @return JsonResponse JSON response indicating success or validation failure
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $book = Book::findOrFail($binaryId);

            $hasActiveCopies = DB::table('vw_copies')
                ->where('book_id', $binaryId)
                ->where('available', false)
                ->exists()
            ;

            if ($hasActiveCopies) {
                return $this->validationErrorResponse([
                    'book' => 'Não é possível deletar o livro pois existem cópias ativas.',
                ]);
            }

            $book->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao deletar livro.', $e, ['book_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao deletar livro.');
        }
    }

    /**
     * Build a query for books including search, filters, and sorting.
     *
     * @param Request $request the HTTP request containing query parameters
     *
     * @return Builder the query builder instance with applied filters
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
     * @param Builder $query the query builder instance
     * @param string $search search string
     *
     * @return Builder modified query builder
     */
    private function applyBookSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);
        $numericSearch = preg_replace('/\D/', '', $search);
        $isIsbn = Validators::validateIsbn($numericSearch);

        if ($isIsbn) {
            $query->where('isbn', $numericSearch);
        } else {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                ;
            });
        }

        return $query;
    }

    /**
     * Apply sorting based on allowed columns.
     *
     * @param Builder $query the query builder instance
     * @param Request $request the HTTP request containing sort parameters
     *
     * @return Builder modified query builder with sorting applied
     */
    private function applyBookSorting(Builder $query, Request $request): Builder
    {
        $sortable = ['title', 'author', 'genre_name', 'publisher'];
        $sort = \in_array($request->input('sort'), $sortable, true) ? $request->input('sort') : 'title';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sort, $direction);
    }
}
