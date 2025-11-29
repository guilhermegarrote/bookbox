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
            ['Ficção Científica', '#1E88E5'],
            ['Fantasia', '#8E24AA'],
            ['Romance', '#E91E63'],
            ['Terror', '#B71C1C'],
            ['Mistério', '#303F9F'],
            ['Suspense', '#1A237E'],
            ['Aventura', '#FB8C00'],
            ['Distopia', '#5D4037'],
            ['Clássicos', '#455A64'],

            ['História', '#6D4C41'],
            ['Biografia', '#3E2723'],
            ['Autoajuda', '#43A047'],
            ['Religião', '#8BC34A'],
            ['Filosofia', '#4E342E'],
            ['Ensaios', '#546E7A'],
            ['Educação', '#039BE5'],
            ['Crônicas', '#7E57C2'],
            ['Memórias', '#5E35B1'],
            ['Guerra', '#C62828'],

            ['Drama', '#8D6E63'],
            ['Comédia', '#FDD835'],
            ['Humor', '#FBC02D'],
            ['Poesia', '#AB47BC'],
            ['Conto', '#26A69A'],
            ['Infantil', '#F06292'],

            ['Policial', '#0D47A1'],
            ['Mitologia', '#6A1B9A'],
            ['Literatura Nacional', '#4CAF50'],
            ['Literatura Estrangeira', '#009688'],
            ['Gótico', '#512DA8'],
            ['Cyberpunk', '#00ACC1'],
            ['Steampunk', '#795548'],
            ['Realismo Mágico', '#7CB342'],
            ['Épico', '#D84315'],

            ['Ciência', '#0288D1'],
            ['Tecnologia', '#0277BD'],
            ['Matemática', '#1565C0'],
            ['Física', '#283593'],
            ['Química', '#00897B'],
            ['Biologia', '#2E7D32'],

            ['Sociologia', '#5C5C8A'],
            ['Antropologia', '#A97458'],
            ['Política', '#BA2B2B'],
            ['Economia', '#AD1457'],
            ['Direito', '#424242'],

            ['Artes', '#EC407A'],
            ['Música', '#9C4DCC'],
            ['Cinema', '#5C6BC0'],
            ['Fotografia', '#37474F'],
        ];

        foreach ($genres as [$name, $color]) {
            Genre::firstOrCreate(
                ['name' => $name],
                ['color_hex' => ltrim($color, '#')],
            );
        }
    }
}
