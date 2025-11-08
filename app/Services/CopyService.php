<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Copy;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

/**
 * Service class to handle operations related to book copies.
 */
class CopyService
{
    /**
     * Registers multiple copies of a book in the database.
     *
     * Ensures that the number of copies does not exceed the maximum allowed (32767)
     * and inserts them in chunks to optimize database performance.
     *
     * @param string $binaryBookId The book ID in binary format
     * @param int $quantity Number of copies to create
     *
     * @throws \InvalidArgumentException If quantity is invalid or exceeds maximum allowed
     * @throws \Exception If the database transaction fails
     */
    public function storeCopies(string $binaryBookId, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('A quantidade de cópias deve ser maior que zero.');
        }

        $lastNumber = Copy::where('book_id', $binaryBookId)->max('number') ?? 0;

        if ($lastNumber >= 32767) {
            throw new \InvalidArgumentException('Esse livro já atingiu o número máximo de cópias.');
        }

        if (($lastNumber + $quantity) > 32767) {
            $maxAllowed = 32767 - $lastNumber;

            throw new \InvalidArgumentException(
                "A quantidade de cópias deve ser menor ou igual a {$maxAllowed}.",
            );
        }

        DB::transaction(function () use ($binaryBookId, $quantity, $lastNumber) {
            $chunk = [];
            $chunkSize = 500;

            for ($i = 1; $i <= $quantity; ++$i) {
                $chunk[] = [
                    'id' => Uuid::uuid4()->getBytes(),
                    'book_id' => $binaryBookId,
                    'number' => $lastNumber + $i,
                ];

                if (\count($chunk) === $chunkSize) {
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
