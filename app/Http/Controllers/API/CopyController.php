<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Copy\CopyAddRequest;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Loan;
use App\Models\View\Copy as ViewCopy;
use App\Services\CopyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class CopyController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $copies = ViewCopy::orderBy('number', 'asc')
                ->paginate(10);

            return $this->successResponse($copies->toArray());
        } catch (Throwable $e) {
            $this->logError('Erro ao listar exemplares.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar os exemplares.');
        }
    }

    public function add(CopyAddRequest $request, CopyService $copyService, string $id): JsonResponse
    {
        try {
            $binaryBookId = Utils::convertUuidToBinary($id);

            Book::findOrFail('id', $binaryBookId);

            $amount = $request->input('amount', 1);

            $copyService->storeCopies($binaryBookId, $amount);

            return $this->successResponse(['added_copies' => $amount]);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao cadastrar exemplar.', $e, ['book_id' => $id ?? null]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar exemplar.');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $copy = Copy::findOrFail($binaryId);

            return $this->successResponse($copy->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Exemplar não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao buscar exemplar.', $e, ['copy_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao buscar exemplar.');
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $copy = Copy::findOrFail($binaryId);

            if (Loan::where('copy_id', $binaryId)
                ->whereNull('returned_date')
                ->exists()
            ) {
                return $this->conflictResponse([
                    'loan' => 'Exemplar está emprestado e não pode ser excluído.'
                ]);
            }

            $copy->delete();

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Exemplar não encontrado.');
        } catch (Throwable $e) {
            $this->logError('Erro ao excluir exemplar.', $e, ['copy_id' => $id]);
            return $this->internalErrorResponse($e, 'Erro interno ao excluir exemplar.');
        }
    }
}
