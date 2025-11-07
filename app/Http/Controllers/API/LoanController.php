<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Helpers\Validators;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\LoanStoreRequest;
use App\Jobs\PrintLoanReceiptJob;
use App\Models\Book;
use App\Models\Loan;
use App\Models\View\Copy as ViewCopy;
use App\Models\View\Loan as ViewLoan;
use App\Models\View\Student as ViewStudent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

/**
 * Controller responsible for managing loans (CRUD operations).
 *
 * Provides endpoints to list, create, retrieve, extend, and finalize loans.
 */
class LoanController extends Controller
{
    /**
     * Display a paginated list of loans with optional filters and sorting.
     *
     * @param Request $request The current HTTP request containing filter and pagination data.
     *
     * @return JsonResponse JSON containing HTML for table, pagination, and filter data.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $this->buildLoanQuery($request);

            $perPage = max(5, min((int) $request->input('perPage', 10), 250));

            $loans = $query->select([
                'id',
                'name',
                'number',
                'title',
                'author',
                'loan_due_date',
                'loan_returned_date',
            ])->paginate($perPage)->appends($request->all());

            $html = view('pages.loans.partials.table', compact('loans'))->render();
            $paginationHtml = view('vendor.pagination.custom', ['paginator' => $loans])->render();

            $filterData = Cache::remember("loan_filters", 300, fn () => ViewLoan::getFilterData($query));

            return response()->json([
                'html' => $html,
                'paginationHtml' => $paginationHtml,
                'filterData' => $filterData,
            ]);
        } catch (\Throwable $e) {
            $this->logError('Error listing loans.', $e);

            return $this->internalErrorResponse($e, 'Internal error listing loans.');
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
     * @param LoanStoreRequest $request Validated request containing 'cpf', 'isbn', and 'copy_number'.
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
                return $this->notFoundResponse('Student not found.');
            }

            $book = Book::where('isbn', $data['isbn'])->first();

            if (!$book) {
                return $this->notFoundResponse('Book not found.');
            }

            $copy = ViewCopy::where('book_id', Utils::convertUuidToBinary($book->id))
                ->where('number', $data['copy_number'])
                ->lockForUpdate()
                ->first();

            if (!$copy) {
                return $this->notFoundResponse('Copy not found.');
            }

            if (!$copy->available) {
                return $this->validationErrorResponse(['message' => 'Copy is not available for loan.']);
            }

            if (!$student->can_borrow) {
                return $this->validationErrorResponse(['message' => 'Student is not allowed to borrow.']);
            }

            $dueDate = Carbon::today()->addDays(config('loans.default_due_days', 14));

            $loan = Loan::create([
                'student_id' => Utils::convertUuidToBinary($student->id),
                'copy_id' => Utils::convertUuidToBinary($copy->id),
                'due_date' => $dueDate,
            ]);

            DB::commit();

            $loanData = ViewLoan::where('id', Utils::convertUuidToBinary($loan->id))->first();
            PrintLoanReceiptJob::dispatch($loanData, JWTAuth::user()->name ?? 'Unknown');

            return $this->createdResponse();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Error creating loan.', $e);

            return $this->internalErrorResponse($e, 'Internal error creating loan.');
        }
    }

    /**
     * Retrieve a specific loan by its UUID.
     *
     * @param string $id UUID of the loan.
     *
     * @return JsonResponse Loan details in JSON format.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $loan = ViewLoan::findOrFail($binaryId);

            return $this->successResponse($loan->toArray());
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Loan not found.');
        } catch (\Throwable $e) {
            $this->logError('Error fetching loan.', $e, ['loan_id' => $id]);

            return $this->internalErrorResponse($e, 'Internal error fetching loan.');
        }
    }

    /**
     * Extend a loan by a configurable number of days.
     *
     * @param string $id UUID of the loan.
     *
     * @return JsonResponse HTTP response indicating success or failure.
     */
    public function extend(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $loan = Loan::findOrFail($binaryId);

            if ($loan->returned_date !== null) {
                return $this->badRequestResponse(['message' => 'Loan already finalized.']);
            }

            $loan->due_date = Carbon::parse($loan->due_date)->addDays(config('loans.extension_days', 7));
            $loan->save();

            $loanData = ViewLoan::where('id', Utils::convertUuidToBinary($loan->id))->first();
            PrintLoanReceiptJob::dispatch($loanData, JWTAuth::user()->name ?? 'Unknown');

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Loan not found.');
        } catch (\Throwable $e) {
            $this->logError('Error extending loan.', $e);

            return $this->internalErrorResponse($e, 'Internal error extending loan.');
        }
    }

    /**
     * Finalize a loan and mark the copy as available again.
     *
     * @param string $id UUID of the loan to finalize.
     *
     * @return JsonResponse HTTP response indicating success or failure.
     */
    public function finalize(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $loan = Loan::findOrFail($binaryId);

            if ($loan->returned_date !== null) {
                return $this->badRequestResponse(['message' => 'Loan already finalized.']);
            }

            $loan->update([
                'returned_date' => Carbon::today(),
            ]);

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Loan not found.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Error finalizing loan.', $e);

            return $this->internalErrorResponse($e, 'Internal error finalizing loan.');
        }
    }

    /**
     * Build a query for loans applying search, filters, and sorting.
     *
     * @param Request $request The current HTTP request containing query parameters.
     *
     * @return Builder Eloquent query builder instance.
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
     * @param Builder $query  The Eloquent query builder.
     * @param string  $search The search string to filter loans.
     *
     * @return Builder Modified query builder with applied search conditions.
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
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%"));
        }

        return $query;
    }

    /**
     * Apply filtering conditions based on genre, publisher, course, period, term, and active loans.
     *
     * @param Builder $query   The Eloquent query builder.
     * @param Request $request The HTTP request containing filter parameters.
     *
     * @return Builder Modified query builder with applied filters.
     */
    private function applyFilters(Builder $query, Request $request): Builder
    {
        return $query->when($request->filled('genre'), fn ($q) => $q->where('genre_name', $request->genre))
            ->when($request->filled('publisher'), fn ($q) => $q->where('publisher', $request->publisher))
            ->when($request->filled('course'), fn ($q) => $q->where('course', $request->course))
            ->when($request->filled('period'), fn ($q) => $q->where('period', $request->period))
            ->when($request->filled('term'), fn ($q) => $q->where('term', $request->term))
            ->when($request->filled('active'), function ($q) use ($request) {
                if ($request->active) {
                    $q->whereNull('loan_returned_date');
                } else {
                    $q->whereNotNull('loan_returned_date');
                }
            });
    }

    /**
     * Apply sorting based on allowed columns and direction.
     *
     * @param Builder $query   The Eloquent query builder.
     * @param Request $request The HTTP request containing sorting parameters.
     *
     * @return Builder Modified query builder with applied sorting.
     */
    private function applySorting(Builder $query, Request $request): Builder
    {
        $sortable = ['title', 'author', 'name', 'loan_due_date'];
        $sort = in_array($request->input('sort'), $sortable, true) ? $request->input('sort') : 'title';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sort, $direction);
    }
}
