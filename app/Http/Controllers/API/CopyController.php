<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Copy\CopyAddRequest;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Loan;
use App\Models\View\Copy as ViewCopy;
use App\Services\CopyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Controller responsible for managing book copies (CRUD operations).
 *
 * Provides endpoints to list, create, retrieve, and delete book copies.
 */
class CopyController extends Controller
{
    /**
     * Retrieve a paginated list of book copies.
     *
     * This endpoint fetches paginated copies ordered by their number.
     * It includes internal error handling and logging.
     *
     * @return JsonResponse returns a paginated list of book copies in JSON format
     */
    public function index(): JsonResponse
    {
        try {
            $copies = Cache::remember('copies:page:' . request('page', 1), 300, function () {
                return ViewCopy::orderBy('number', 'asc')->paginate(10);
            });

            return $this->successResponse($copies->toArray());
        } catch (\Throwable $e) {
            $this->logError('Erro ao listar exemplares.', $e);

            return $this->internalErrorResponse($e, 'Erro interno ao listar os exemplares.');
        }
    }

    /**
     * Add new copies to a specific book.
     *
     * Validates the request, ensures the book exists, and uses the service
     * layer to create the requested number of copies. Each book is identified
     * by a UUID converted to its binary representation for performance.
     *
     * @param CopyAddRequest $request validated request data
     * @param CopyService $copyService service responsible for creating copies
     * @param string $id UUID of the target book
     *
     * @return JsonResponse returns the number of added copies
     *
     * @see CopyService::storeCopies()
     */
    public function add(CopyAddRequest $request, CopyService $copyService, string $id): JsonResponse
    {
        try {
            $id = trim($id);

            $binaryBookId = Utils::convertUuidToBinary($id);

            Book::where('id', $binaryBookId)->firstOrFail();

            $amount = (int) $request->input('amount', 1);
            $copyService->storeCopies($binaryBookId, $amount);

            return $this->successResponse(['added_copies' => $amount]);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Livro não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao cadastrar exemplar.', $e, ['book_id' => $id ?? null]);

            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar exemplar.');
        }
    }

    /**
     * Retrieve detailed information about a specific copy.
     *
     * @param string $id UUID of the copy to retrieve
     *
     * @return JsonResponse returns the copy information in JSON format
     */
    public function show(string $id): JsonResponse
    {
        try {
            $id = trim($id);

            $binaryId = Utils::convertUuidToBinary($id);

            $copy = Cache::remember("copy:{$id}", 300, fn () => Copy::findOrFail($binaryId));

            return $this->successResponse($copy->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Exemplar não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao buscar exemplar.', $e, ['copy_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao buscar exemplar.');
        }
    }

    /**
     * Delete a copy if it is not currently on loan.
     *
     * Performs integrity validation to ensure that no active loans exist for
     * the copy before deletion. If the copy is on loan, a conflict response
     * is returned.
     *
     * @param string $id UUID of the copy to be deleted
     *
     * @return JsonResponse returns 204 No Content on success or a conflict message otherwise
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $id = trim($id);

            $binaryId = Utils::convertUuidToBinary($id);
            $copy = Copy::findOrFail($binaryId);

            if (Loan::where('copy_id', $binaryId)->whereNull('returned_date')->exists()) {
                return $this->conflictResponse([
                    'loan' => 'Exemplar está emprestado e não pode ser excluído.',
                ]);
            }

            $copy->delete();

            Cache::forget("copy:{$id}");

            return $this->noContentResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Exemplar não encontrado.');
        } catch (\Throwable $e) {
            $this->logError('Erro ao excluir exemplar.', $e, ['copy_id' => $id]);

            return $this->internalErrorResponse($e, 'Erro interno ao excluir exemplar.');
        }
    }
}
