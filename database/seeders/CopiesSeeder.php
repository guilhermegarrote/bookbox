<?php

namespace Database\Seeders;

use App\Helpers\Utils;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Copy;

class CopiesSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::all();

        if ($books->isEmpty()) {
            $this->command->warn("❌ Nenhum livro encontrado. Rode o BookSeeder primeiro.");
            return;
        }

        $created = 0;

        foreach ($books as $book) {
            $copiesCount = rand(2, 5);

            for ($i = 1; $i <= $copiesCount; $i++) {
                try {
                    Copy::create([
                        'book_id' => Utils::convertUuidToBinary($book->id),
                        'number'  => $i,
                    ]);
                    $created++;
                } catch (\Throwable $e) {
                    $this->command->error("❗ Erro ao criar cópia do livro {$book->title}: {$e->getMessage()}");
                }
            }
        }

        $this->command->info("✅ Seeder de cópias finalizado. {$created} cópias criadas.");
    }
}
