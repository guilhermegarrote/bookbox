<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

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
                ['color_hex' => $this->randomColor()],
            );
        }
    }

    private function randomColor(): string
    {
        return \sprintf('%06X', mt_rand(0, 0xFFFFFF));
    }
}
