<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Copy\CopyStoreRequest;
use App\Models\Book;
use App\Models\Copy;
use App\Services\CopyService;
use Illuminate\Http\JsonResponse;
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

            $copyService->storeCopies($binaryBookId, $numberOfCopies);

            DB::commit();

            return $this->createdResponse();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->logError('Erro ao cadastrar exemplar.', $e, ['book' => $book ?? null]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar exemplar.');
        }
    }

    /*
        está faltando as funções show() e destroy()
    */
}
