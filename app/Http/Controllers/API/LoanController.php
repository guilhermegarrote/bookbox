<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Helpers\Validators;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\LoanStoreRequest;
use App\Models\Loan;
use App\Models\Book;
use App\Models\View\Copy as ViewCopy;
use App\Models\View\Loan as ViewLoan;
use App\Models\View\Student as ViewStudent;
use App\Services\ThermalPrinterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class LoanController extends Controller
{
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

            $filterData = ViewLoan::getFilterData($query);

            return response()->json([
                'html' => $html,
                'paginationHtml' => $paginationHtml,
                'filterData' => $filterData,
            ]);
        } catch (Throwable $e) {
            $this->logError('Erro ao listar empréstimos.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar os empréstimos.');
        }
    }

    public function store(LoanStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $student = ViewStudent::where('cpf_hash', hash('sha256', $data['cpf'], true))->first();
            if (!$student) {
                return $this->notFoundResponse('Estudante não encontrado.');
            }

            $book = Book::where('isbn', $data['isbn'])->first();
            if (!$book) {
                return $this->notFoundResponse('Livro não encontrado.');
            }

            $copy = ViewCopy::where('book_id', Utils::convertUuidToBinary($book->id))
                ->where('number', $data['copy_number'])
                ->first();
            if (!$copy) {
                return $this->notFoundResponse('Exemplar não encontrado.');
            }

            if (!$copy->available) {
                return $this->validationErrorResponse(['message' => 'Exemplar não está disponível para empréstimo']);
            }

            if (!$student->can_borrow) {
                return $this->validationErrorResponse(['message' => 'Aluno não tem permissão para emprestar.']);
            }

            $dueDate = Carbon::today()->addDays(config('loans.default_due_days', 14));

            $loan = Loan::create([
                'student_id' => Utils::convertUuidToBinary($student->id),
                'copy_id'    => Utils::convertUuidToBinary($copy->id),
                'due_date'   => $dueDate,
            ]);

            DB::commit();

            $printer = new ThermalPrinterService("192.168.0.50", 9100);
            $printer->printLoanReceipt($loan);

            return $this->createdResponse();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Erro ao realizar empréstimio.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao realizar empréstimo');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $loan = ViewLoan::findOrFail($binaryId);

            return $this->successResponse($loan->toArray());
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Empréstimo não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao buscar Empréstimo.', $e, ['loan_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar Empréstimo.');
        }
    }

    /**
     * Extend a loan by a configurable number of days.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function extend(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $loan = Loan::findOrFail($binaryId);

            if ($loan->returned_date != null) {
                return $this->badRequestResponse(['message' => 'Empréstimo já foi finalizado.']);
            }

            $loan->due_date = Carbon::parse($loan->due_date)->addDays(config('loans.extension_days', 7));
            $loan->save();

            $printer = new ThermalPrinterService("192.168.0.50", 9100);
            $printer->printLoanReceipt($loan);

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Empréstimo não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao estender o empréstimo.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao estender empréstimo.');
        }
    }

    /**
     * Finalize a loan and mark the copy as available again.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function finalize(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $loan = Loan::findOrFail($binaryId);

            if ($loan->returned_date != null) {
                return $this->badRequestResponse(['message' => 'Empréstimo já foi finalizado.']);
            }

            $loan->update([
                'returned_date' => Carbon::today(),
            ]);

            return $this->noContentResponse();
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Empréstimo não encontrado.');
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Erro ao finalizar empréstimo.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao finalizar empréstimo.');
        }
    }

    /**
     * Build the loans query with search, filters, and sorting.
     *
     * @param Request $request
     * @return Builder
     */
    private function buildLoanQuery(Request $request): Builder
    {
        $query = ViewLoan::query();

        if ($request->filled('search')) {
            $query = $this->applySearch($query, $request->search);
        }

        $query = $this->applyFilters($query, $request);
        $query = $this->applySorting($query, $request);

        return $query;
    }

    /**
     * Apply search filters based on email, CPF, phone, ISBN, or fallback text search.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    private function applySearch($query, string $search)
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
        } else {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Apply filters for genre, publisher, course, period and term.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    private function applyFilters($query, Request $request)
    {
        return $query->when($request->filled('genre'), fn($q) => $q->where('genre_name', $request->genre))
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
            });
    }

    /**
     * Apply sorting based on allowed columns and direction.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    private function applySorting($query, Request $request)
    {
        $sortable = ['title', 'author', 'name', 'loan_due_date'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'title';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sort, $direction);
    }
}
