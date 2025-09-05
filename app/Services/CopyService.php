<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CopyService
{
    /**
     * Cria múltiplas cópias para um livro e retorna os dados inseridos.
     *
     * @param int $bookId
     * @param int $quantity
     * @return array
     */
    public function storeCopies(int $bookId, int $quantity): array
    {
        if ($quantity <= 0) {
            return [];
        }

        $now = now();
        $copies = [];

        for ($i = 1; $i <= $quantity; $i++) {
            $copies[] = [
                'book_id' => $bookId,
                'number' => $i,
            ];
        }

        DB::table('copies')->insert($copies);

        return $copies;
    }
}
