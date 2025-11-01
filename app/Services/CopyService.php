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

        $lastNumber = Copy::where('book_id', $binaryBookId)->max('number') ?? 0;

        if ($lastNumber == 32767) {
            throw new InvalidArgumentException("Esse livro já atingiu o número máximo de cópias");
        }

        if (($lastNumber + $quantity) > 32767) {
            throw new InvalidArgumentException(
                "A quantidade de cópias deve ser menor que " . (32768 - $lastNumber) . "."
            );
        }

        DB::transaction(function () use ($binaryBookId, $quantity, $lastNumber) {
            $chunk = [];
            $chunkSize = 500;

            for ($i = 1; $i <= $quantity; $i++) {
                $chunk[] = [
                    'id' => Uuid::uuid4()->getBytes(),
                    'book_id' => $binaryBookId,
                    'number' => $lastNumber + $i,
                ];

                if (count($chunk) === $chunkSize) {
                    Copy::insert($chunk);
                    $chunk = [];
                }
            }

            if (!empty($chunk)) {
                Copy::insert($chunk);
            }
        });
    }
}
