<?php

namespace App\Services;

use App\Models\Copy;
use Illuminate\Support\Facades\DB;
use Exception;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class CopyService
{
    /**
     * Register multiple copies of a book.
     *
     * @param string $binaryBookId
     * @param int $quantity
     * @throws Exception
     */
    public function storeCopies(string $binaryBookId, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("A quantidade de cópias deve ser maior que zero.");
        }

        DB::transaction(function () use ($binaryBookId, $quantity) {
            $lastNumber = Copy::where('book_id', $binaryBookId)->max('number') ?? 0;

            $copies = [];

            for ($i = 1; $i <= $quantity; $i++) {
                $copies[] = [
                    'id' => Uuid::uuid4()->getBytes(),
                    'book_id' => $binaryBookId,
                    'number'  => $lastNumber + $i
                ];
            }

            Copy::insert($copies);
        });
    }
}
