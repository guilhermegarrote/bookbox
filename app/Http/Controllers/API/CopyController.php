<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Copy\CopyStoreRequest;
use App\Models\Book;
use App\Models\Copy;
use App\Services\CopyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

class CopyController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $copies = Copy::with(['book'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return $this->successResponse($copies->toArray());
        } catch (Throwable $e) {
            $this->logError('Erro ao listar exemplares.', $e);
            return $this->internalErrorResponse($e, 'Erro interno ao listar os exemplares.');
        }
    }

    public function store(CopyStoreRequest $request, CopyService $copyService): JsonResponse
    {
        DB::beginTransaction();

        try {
            $book = Book::where('isbn', $request->isbn)->first();

            if (!$book) {
                return $this->notFoundResponse('Livro não encontrado para o ISBN informado.');
            }

            $binaryBookId = Utils::convertUuidToBinary($book->id);

            // Aqui precisa garantir que $numberOfCopies exista (talvez do request)
            $numberOfCopies = $request->input('number_of_copies', 1);

            $copyService->storeCopies($binaryBookId, $numberOfCopies);

            DB::commit();

            return $this->createdResponse();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->logError('Erro ao cadastrar exemplar.', $e, ['book' => $book ?? null]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar exemplar.');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $binaryId = Utils::convertUuidToBinary($id);
            $copy = Copy::with(['book'])->findOrFail($binaryId);

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

            // Caso precise validar se o exemplar está emprestado antes de excluir:
            // if ($copy->loans()->whereNull('returned_date')->exists()) {
            //     return $this->conflictResponse(['loan' => 'Exemplar está emprestado e não pode ser excluído.']);
            // }

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
