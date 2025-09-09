<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\LoanStoreRequest;
use App\Models\Loan;
use App\Models\Copy;
use App\Models\Student;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Throwable;

class LoanController extends Controller
{
    /**
     * List all loans.
     */
    /**
     * Display a listing of loans.
     */
    public function index(): JsonResponse
    {
        try {
            $loans = Loan::with(['student', 'copy.book'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return $this->successResponse($loans->toArray());
        } catch (Throwable $e) {
            $this->logError('Erro ao listar empréstimos.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar os empréstimos.');
        }
    }

    /**
     * Create a new loan.
     */
    public function store(LoanStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $cpf = preg_replace('/\D/', '', $request->cpf);
            $cpfHash = hash('sha256', $cpf, true);

            $student = Student::where('cpf_hash', $cpfHash)->first();
            if (!$student) {
                return response()->json(['message' => 'Aluno não encontrado.'], 404);
            }

            $isbn = preg_replace('/[^0-9X]/', '', $request->isbn);
            $book = Book::where('isbn', $isbn)->first();
            if (!$book) {
                return response()->json([
                    'message' => 'Livro não encontrado para o ISBN informado.'
                ], 404);
            }

            $copy = Copy::where('book_id', Utils::convertUuidToBinary($book->id))
                ->where('number', $request->copy_number)
                ->first();

            if (!$copy) {
                return response()->json([
                    'message' => 'Exemplar não encontrado para este livro e número informado.'
                ], 404);
            }

            if (!$copy->available) {
                return response()->json([
                    'message' => 'Exemplar não disponível para empréstimo.'
                ], 422);
            }

            $activeLoans = Loan::where('student_id', $student->id)
                ->where('active', true)
                ->count();

            if ($activeLoans >= 3) {
                return response()->json([
                    'message' => 'O aluno já atingiu o limite de 3 empréstimos ativos.'
                ], 422);
            }

            $dueDate   = Carbon::today()->addDays(14);

            Loan::create([
                'student_id'    => Utils::convertUuidToBinary($student->id),
                'copy_id'       => Utils::convertUuidToBinary($copy->id),
                'due_date'      => $dueDate
            ]);

            $copy->update(['available' => false]);

            DB::commit();

            return $this->createdResponse();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->logError('Erro ao cadastrar empréstimo.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar empréstimo.');
        }
    }

    /**
     * Show a specific loan.
     */
    public function show(string $id): JsonResponse
    {
        $loan = Loan::with(['student', 'copy.book'])->find($id);

        if (!$loan) {
            return response()->json(['message' => 'Empréstimo não encontrado.'], 404);
        }

        return response()->json($loan);
    }

    /**
     * Extend a loan by 7 days.
     */
    public function extend(string $id): JsonResponse
    {
        $loan = Loan::find($id);

        if (!$loan || !$loan->active) {
            return response()->json(['message' => 'Empréstimo não encontrado ou já finalizado.'], 404);
        }

        $loan->due_date = Carbon::parse($loan->due_date)->addDays(7);
        $loan->save();

        return response()->json([
            'message' => 'Empréstimo prorrogado por mais 7 dias.',
            'data' => $loan
        ]);
    }

    /**
     * Finalize a loan and update the return date.
     */
    public function finalize(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $loan = Loan::find($id);

            if (!$loan || !$loan->active) {
                return response()->json(['message' => 'Empréstimo não encontrado ou já finalizado.'], 404);
            }

            $loan->update([
                'returned_date' => Carbon::today(),
                'active'        => false,
            ]);

            // Mark copy as available again
            $loan->copy()->update(['available' => true]);

            DB::commit();

            return response()->json(['message' => 'Empréstimo finalizado com sucesso.']);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro interno ao finalizar empréstimo.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
