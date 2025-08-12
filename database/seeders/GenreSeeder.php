<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            ['name' => 'Ficção Científica', 'color_hex' => '1E90FF'],
            ['name' => 'Fantasia', 'color_hex' => '8A2BE2'],
            ['name' => 'Romance', 'color_hex' => 'FF69B4'],
            ['name' => 'Terror', 'color_hex' => '8B0000'],
            ['name' => 'Mistério', 'color_hex' => '2F4F4F'],
            ['name' => 'Aventura', 'color_hex' => 'FFD700'],
            ['name' => 'História', 'color_hex' => 'A0522D'],
            ['name' => 'Biografia', 'color_hex' => '008B8B'],
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate(
                ['name' => $genre['name']],
                ['color_hex' => $genre['color_hex']]
            );
        }
    }
}
