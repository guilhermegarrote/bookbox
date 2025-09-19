<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CopyService
{

    /**
     * Cria múltiplas cópias para um livro.
     *
     * @param string $bookId
     * @param int $quantity
     * @return bool
     */
    public function storeCopies(string $bookId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $copies = [];

        /* O cadastro só começa apartir do 1 se ainda não existir nenhum exemplar desse livro cadastro. Caso exista comece a cadastrar apartir do numero posterior ao cadastrado. */
        for ($i = 1; $i <= $quantity; $i++) {
            $copies[] = [
                'book_id' => $bookId,
                'number' => $i,
            ];

            /* Ao invés de guardar em um array a cada execução do for fazer o cadastro de um exemplar ao invés de usar DB use o Model de copy */
        }

        DB::table('copies')->insert($copies);

        // Ao invés de retornar os exemplares cadastrados, não retorne nada caso de certo porém se der errado retorne um trowblae ou exception o que for melhor
        // return $copies;
        return true; /* colocado só para remover o erro depois pode tirar */
    }
}
