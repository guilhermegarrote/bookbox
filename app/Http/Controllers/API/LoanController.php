<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Helpers\Validators;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\LoanStoreRequest;
use App\Jobs\Email\SendLoanExtendJob;
use App\Jobs\Email\SendLoanReceiptJob;
use App\Jobs\PrintLoanReceiptJob;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\View\Copy as ViewCopy;
use App\Models\View\Loan as ViewLoan;
use App\Models\View\Student as ViewStudent;
use App\Traits\HasPaginationSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

/**
 * Controller responsible for managing loans (CRUD operations).
 *
 * Provides endpoints to list, create, retrieve, extend, and finalize loans.
 */
class LoanController extends Controller
{
    use HasPaginationSettings;

    /**
     * Retrieve a paginated list of loans with optional search terms and filters.
     *
     * This endpoint processes the incoming request, applies search keywords,
     * filters, and pagination rules, and returns a structured JSON payload
     * containing the resulting dataset, pagination cursors, and filter metadata
     * used to build the dynamic UI.
     *
     * @param Request $request The HTTP request containing query parameters for search, filtering, ordering, and pagination settings
     *
     * @throws \Throwable If an unexpected exception occurs while building the query or generating the paginated response
     *
     * @return JsonResponse A JSON response containing: - `data`: the paginated list of loans - `pagination`: cursor-based pagination metadata - `filterData`: aggregated metadata for dynamic filter components
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $this->buildLoanQuery($request);

            $filterData = ViewLoan::getFilterData(clone $query);
            $data_sidebar = ViewLoan::getSidebarData(clone $query);

            $loans = $this->paginateWithSettings(
                $query->select([
                    'id',
                    'name',
                    'number',
                    'title',
                    'author',
                    'loan_due_date',
                    'loan_returned_date',
                ]),
                $request,
            );

            return response()->json([
                'data' => $loans->items(),
                'pagination' => [
                    'next_cursor' => $loans->nextCursor()?->encode(),
                    'has_more' => $loans->nextCursor() !== null,
                ],
                'filterData' => $filterData,
                'data_sidebar' => $data_sidebar,
            ]);
        } catch (\Throwable $e) {
            $this->logError('Erro ao listar empréstimos.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao listar empréstimos.');
        }
    }

    /**
     * Create a new loan for a student and a book copy.
     *
     * Steps:
     * - Validates the request
     * - Checks student, book, and copy existence and availability
     * - Checks borrow permissions
     * - Creates the loan record in a transaction
     * - Dispatches asynchronous printing of receipt
     *
     * @param LoanStoreRequest $request validated request containing 'cpf', 'isbn', and 'copy_number'
     *
     * @return JsonResponse HTTP response indicating success or failure
     */
    public function store(LoanStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $student = ViewStudent::where('cpf_hash', hash('sha256', $data['cpf'], true))->lockForUpdate()->first();

            if (!$student) {
                return $this->notFoundResponse('Aluno não encontrado.');
            }

            $book = Book::where('isbn', $data['isbn'])->first();

            if (!$book) {
                return $this->notFoundResponse('Livro não encontrado.');
            }

            $copy = ViewCopy::where('book_id', Utils::convertUuidToBinary($book->id))
                ->where('number', $data['copy_number'])
                ->lockForUpdate()
                ->first();

            if (!$copy) {
                return $this->notFoundResponse('Exemplar não encontrado.');
            }

            if (!$copy->available) {
                return $this->validationErrorResponse(['message' => 'Exemplar não disponível para empréstimo.']);
            }

            if (!$student->can_borrow) {
                return $this->validationErrorResponse(['message' => 'Aluno não autorizado a realizar empréstimos.']);
            }

            $dueDays = Setting::where('key', 'default_due_days')->value('value');
            $dueDate = Carbon::today()->addDays((int) $dueDays);

            $loan = Loan::create([
                'student_id' => Utils::convertUuidToBinary($student->id),
                'copy_id' => Utils::convertUuidToBinary($copy->id),
                'due_date' => $dueDate,
            ]);

            DB::commit();

            dispatch(new PrintLoanReceiptJob($loan->id, JWTAuth::user()->name ?? 'Desconhecido'));
            dispatch(new SendLoanReceiptJob($loan->id));

            return $this->createdResponse();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Erro ao criar empréstimo.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao criar empréstimo.');
        }
    }

    /**
     * Retrieve a specific loan by its UUID.
     *
     * @param string $id UUID of the loan
     *
     * @return JsonResponse loan details in JSON format
     */
    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $loan = ViewLoan::findOrFail($binaryId);

            return $this->successResponse($loan->toArray());
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Empréstimo não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar empréstimo.', $e, ['loan_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao buscar empréstimo.');
        }
    }

    /**
     * Find a loan by its barcode.
     *
     * Validates the provided barcode and returns
     * basic loan information if found.
     *
     * @param string $barcode Loan barcode
     *
     * @return JsonResponse HTTP response containing loan data or error message
     */
    public function findByBarcode(string $barcode): JsonResponse
    {
        try {
            if (!Validators::validateLoanCode($barcode)) {
                return $this->badRequestResponse(["barcode_code" => "Código de barras inválido."]);
            }

            $loan = ViewLoan::where('barcode_code', $barcode)->first();

            if (!$loan) {
                return $this->notFoundResponse('Empréstimo não encontrado.');
            }

            return $this->successResponse([
                'loanId' => $loan->id,
                'bookId' => $loan->book_id,
                'studentId' => $loan->student_id,
            ]);
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar empréstimo por código de barras.', $e, ['barcode' => $barcode]);
            return $this->internalErrorResponse($e, 'Erro interno ao consultar o empréstimo.');
        }
    }

    /**
     * Extend a loan by a configurable number of days.
     *
     * @param string $id UUID of the loan
     *
     * @return JsonResponse HTTP response indicating success or failure
     */
    public function extend(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $loan = Loan::findOrFail($binaryId);

            if ($loan->returned_date !== null) {
                return $this->badRequestResponse(['message' => 'Empréstimo já finalizado.']);
            }

            $extensionDays = Setting::where('key', 'extension_days')->value('value');
            $loan->due_date = Carbon::parse($loan->due_date)->addDays((int) $extensionDays);
            $loan->save();

            dispatch(new PrintLoanReceiptJob($loan->id, JWTAuth::user()->name ?? 'Desconhecido'));
            dispatch(new SendLoanExtendJob($loan->id));

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Empréstimo não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao estender empréstimo.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao estender empréstimo.');
        }
    }

    /**
     * Finalize a loan by setting the returned date to today.
     *
     * @param string $id UUID of the loan
     *
     * @return JsonResponse HTTP response indicating success or failure
     */
    public function finalize(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $loan = Loan::findOrFail($binaryId);

            if ($loan->returned_date !== null) {
                return $this->badRequestResponse(['message' => 'Empréstimo já finalizado.']);
            }

            $loan->update([
                'returned_date' => Carbon::today(),
            ]);

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Empréstimo não encontrado.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Erro ao finalizar empréstimo.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao finalizar empréstimo.');
        }
    }

    /**
     * Finalize a loan and mark the copy as available again.
     *
     * @return JsonResponse HTTP response indicating success or failure
     */
    private function buildLoanQuery(Request $request): Builder
    {
        $query = ViewLoan::query();

        if ($request->filled('search')) {
            $query = $this->applySearch($query, $request->search);
        }

        $query = $this->applyFilters($query, $request);

        return $this->applySorting($query, $request);
    }

    /**
     * Apply search conditions for email, CPF, phone, ISBN, or barcode.
     *
     * @param Builder $query the Eloquent query builder
     * @param string $search the search string to filter loans
     *
     * @return Builder modified query builder with applied search conditions
     */
    private function applySearch(Builder $query, string $search): Builder
    {
        $search = trim($search);
        $numericSearch = preg_replace('/\D/', '', $search);

        if (Validators::validateEmail($search)) {
            $query->where('email_hash', hash('sha256', $search, true));
        } elseif (Validators::validateCpf($numericSearch)) {
            $query->where('cpf_hash', hash('sha256', $numericSearch, true));
        } elseif (Validators::validatePhoneNumber($numericSearch)) {
            $query->where('phone_hash', hash('sha256', $numericSearch, true));
        } elseif (Validators::validateIsbn($numericSearch)) {
            $query->where('isbn', $numericSearch);
        } elseif (Validators::validateLoanCode($search)) {
            $query->where('barcode_code', $search);
        } else {
            $query->where(fn($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%"));
        }

        return $query;
    }

    /**
     * Apply filtering conditions based on genre, publisher, course, period, term, and active loans.
     *
     * @param Builder $query the Eloquent query builder
     * @param Request $request the HTTP request containing filter parameters
     *
     * @return Builder modified query builder with applied filters
     */
    private function applyFilters(Builder $query, Request $request): Builder
    {
        return $query->when($request->filled('genre_name'), fn($q) => $q->where('genre_name', $request->genre_name))
            ->when($request->filled('publisher'), fn($q) => $q->where('publisher', $request->publisher))
            ->when($request->filled('course'), fn($q) => $q->where('course', $request->course))
            ->when($request->filled('period'), fn($q) => $q->where('period', $request->period))
            ->when($request->filled('term'), fn($q) => $q->where('term', $request->term))
            ->when($request->filled('active'), function ($q) use ($request) {
                if ($request->active) {
                    $q->whereNull('loan_returned_date');
                } else {
                    $q->whereNotNull('loan_returned_date');
                }
            })
        ;
    }

    /**
     * Apply sorting based on allowed columns and direction.
     *
     * @param Builder $query the Eloquent query builder
     * @param Request $request the HTTP request containing sorting parameters
     *
     * @return Builder modified query builder with applied sorting
     */
    private function applySorting(Builder $query, Request $request): Builder
    {
        $sortable = ['title', 'author', 'name', 'number', 'loan_due_date'];
        $sort = \in_array($request->input('sort'), $sortable, true) ? $request->input('sort') : 'loan_due_date';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $idDirection = $direction === 'desc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $direction)
            ->orderBy('id', $idDirection)
        ;
    }
}
