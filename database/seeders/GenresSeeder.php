<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenresSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Ficção Científica',
            'Fantasia',
            'Romance',
            'Terror',
            'Mistério',
            'Aventura',
            'História',
            'Biografia',
            'Drama',
            'Comédia',
            'Poesia',
            'Conto',
            'Suspense',
            'Infantil',
            'Distopia',
            'Clássicos',
            'Autoajuda',
            'Religião',
            'Filosofia',
            'Ensaios',
            'Educação',
            'Crônicas',
            'Humor',
            'Policial',
            'Literatura Nacional',
            'Literatura Estrangeira',
            'Guerra',
            'Memórias',
            'Mitologia',
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate(
                ['name' => $genre],
                ['color_hex' => $this->randomColor()]
            );
        }
    }

    private function randomColor(): string
    {
        return sprintf('%06X', mt_rand(0, 0xFFFFFF));
    }
}
