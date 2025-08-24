<?php

namespace Database\Seeders;

use App\Helpers\Utils;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Arr;

class BooksSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            'O Segredo das Estrelas', 'A Casa dos Ventos', 'Sombras do Passado',
            'O Último Guardião', 'Entre Dois Mundos', 'Luzes da Cidade',
            'A Jornada Infinita', 'O Enigma do Tempo', 'Mar de Lembranças',
            'Além da Escuridão', 'A Chave Perdida', 'Caminhos da Vida',
            'Noite Sem Fim', 'O Despertar dos Sonhos', 'As Crônicas da Lua'
        ];

        $authors = [
            'José Silva', 'Maria Oliveira', 'Carlos Souza', 'Ana Costa',
            'Fernanda Ribeiro', 'João Pereira', 'Juliana Santos',
            'Rafael Martins', 'Patrícia Almeida', 'Rodrigo Rocha',
            'Camila Araújo', 'Gabriel Nascimento', 'Larissa Monteiro',
            'Felipe Mendes', 'Beatriz Carvalho'
        ];

        $publishers = [
            'Editora Aurora', 'Livros & Cia', 'Mundo Literário',
            'Palavra Viva', 'Edições Horizonte', 'Estrela do Sul',
            'Editora Atlas', 'Novo Saber', 'Cultura Livre',
            'Editora Solaris'
        ];

        $genres = Genre::all();

        if ($genres->isEmpty()) {
            $this->command->warn("❌ Nenhum gênero encontrado. Rode o GenreSeeder primeiro.");
            return;
        }

        $created = 0;

        for ($i = 1; $i <= 50; $i++) {
            try {
                $book = Book::create([
                    'isbn'      => $this->generateIsbn(),
                    'title'     => Arr::random($titles),
                    'author'    => Arr::random($authors),
                    'publisher' => Arr::random($publishers),
                    'genre_id'  => Utils::convertUuidToBinary($genres->random()->id),
                ]);

                $created++;
            } catch (\Throwable $e) {
                $this->command->error("❗ Erro ao cadastrar livro: {$e->getMessage()}");
            }
        }

        $this->command->info("✅ Seeder de livros finalizado. {$created} livros criados.");
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
