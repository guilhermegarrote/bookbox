<?php

namespace App\Services;

use App\Models\Copy;
use Illuminate\Support\Facades\DB;
use Exception;

class CopyService
{
    /**
     * Cria múltiplas cópias para um livro.
     *
     * @param string $binaryBookId
     * @param int $quantity
     * @throws \Exception
     */
    public function storeCopies(string $binaryBookId, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new Exception("A quantidade de cópias deve ser maior que zero.");
        }

        DB::beginTransaction();

        try {
            $lastNumber = Copy::where('book_id', $binaryBookId)->max('number') ?? 0;

            for ($i = 1; $i <= $quantity; $i++) {
                Copy::create([
                    'book_id' => $binaryBookId,
                    'number'  => $lastNumber + $i,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new Exception("Erro ao criar cópias: " . $e->getMessage(), 0, $e);
        }
    }
}
