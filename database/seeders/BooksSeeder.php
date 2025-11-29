<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Helpers\Utils;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class BooksSeeder extends Seeder
{
    public function run(): void
    {
        $titlePrefixes = [
            'O Segredo de', 'As Crônicas de', 'A Lenda de', 'O Mistério de',
            'O Enigma de', 'A Jornada para', 'Sombras de', 'Luzes de',
            'A Ascensão de', 'A Queda de', 'Memórias de', 'A Profecia de',
        ];

        $titleObjects = [
            'Estrelas', 'Ventania', 'Vales Perdidos', 'Orion', 'Khalmir',
            'Mar Profundo', 'Eternidade', 'Nexus', 'Horizonte', 'Aurora',
            'Tempestade', 'Outono', 'Neblina', 'Amanhecer', 'Crepúsculo',
        ];

        $authors = [
            'José A. Silva', 'Maria de Oliveira', 'Carlos H. Souza',
            'Ana L. Costa', 'Fernanda Ribeiro', 'João M. Pereira',
            'Juliana Santos', 'Rafael D. Martins', 'Patrícia Almeida',
            'Rodrigo Rocha', 'Camila Araújo', 'Gabriel Nascimento',
            'Larissa Monteiro', 'Felipe Mendes', 'Beatriz Carvalho',
            'H. G. Mendonça', 'L. T. Vargas', 'A. P. Santana',
            'Marcos F. Duarte', 'Sofia R. Antunes',
        ];

        $publishers = [
            'Editora Aurora', 'Estrela do Sul', 'Solaris Books',
            'Horizonte Editorial', 'Ponto & Vírgula', 'Palavra Viva',
            'Luz & Sombra Press', 'Atlas Brasil', 'Nova Era',
            'Cultura Livre', 'Alvorecer Editora',
        ];

        /** @var Collection $genres */
        $genres = Genre::all();

        if ($genres->isEmpty()) {
            $this->command->warn('❌ Nenhum gênero encontrado. Rode o GenresSeeder primeiro.');
            return;
        }

        $created = 0;
        $uniqueChecks = [];

        for ($i = 1; $i <= 80; $i++) {
            try {
                $title = Arr::random($titlePrefixes) . ' ' . Arr::random($titleObjects);

                $author = Arr::random($authors);
                $publisher = Arr::random($publishers);
                $genre = $genres->random();

                $uniqueKey = $title . '|' . $author . '|' . $publisher;

                if (isset($uniqueChecks[$uniqueKey])) {
                    $i--;
                    continue;
                }
                $uniqueChecks[$uniqueKey] = true;

                Book::create([
                    'isbn'      => $this->generateUniqueIsbn(),
                    'title'     => $title,
                    'author'    => $author,
                    'publisher' => $publisher,
                    'genre_id'  => Utils::convertUuidToBinary($genre->id),
                ]);

                $created++;

            } catch (\Throwable $e) {
                $this->command->error("❗ Erro ao cadastrar livro: {$e->getMessage()}");
            }
        }

        $this->command->info("📚 Seeder de livros finalizado. {$created} livros criados com sucesso!");
    }

    private array $generatedIsbns = [];

    private function generateUniqueIsbn(): string
    {
        do {
            $isbn = $this->generateIsbn();
        } while (isset($this->generatedIsbns[$isbn]));

        $this->generatedIsbns[$isbn] = true;

        return $isbn;
    }

    private function generateIsbn(): string
    {
        $isbnBase = '978' . str_pad((string) mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);

        $sum = 0;
        foreach (str_split($isbnBase) as $i => $digit) {
            $sum += (int) $digit * ($i % 2 === 0 ? 1 : 3);
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $isbnBase . $checkDigit;
    }
}
