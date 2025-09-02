<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Copy\CopyStoreRequest;
use App\Models\Book;
use App\Models\Copy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class CopyController extends Controller
{
    /**
     * Store new copies linked to a book via ISBN.
     */
    public function store(CopyStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Find book by ISBN
            $book = Book::where('isbn', $request->isbn)->first();

            if (!$book) {
                return response()->json([
                    'message' => 'Livro não encontrado para o ISBN informado.'
                ], 404);
            }

            $copies = [];

            // Create the requested number of copies
            for ($i = 0; $i < $request->numberOfCopies; $i++) {
                $copies[] = Copy::create([
                    'book_id'   =>  Utils::convertUuidToBinary($book->id),
                    'number'    => $i+1,
                    'available' => $request->available ?? true,
                ]);
            }

            DB::commit();

             return $this->createdResponse();

        } catch (Throwable $e) {
            DB::rollBack();

            $this->logError('Erro ao cadastrar exemplar.', $e, ['book' => $book]);
            return $this->internalErrorResponse($e, 'Erro interno ao cadastrar exemplar.');

        }
    }
}
