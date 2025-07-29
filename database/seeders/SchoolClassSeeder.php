<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolClass;
use Carbon\Carbon;

class SchoolClassSeeder extends Seeder
{
    public function run()
    {
        // Lista exemplo de cursos válidos, substitua pelos seus reais
        $courses = [
            'Administração',
            'Engenharia Civil',
            'Direito',
            'Medicina',
            'Arquitetura',
            'Psicologia',
            'Ciência da Computação',
            'Economia',
            'Fisioterapia',
            'Enfermagem',
        ];

        $terms = ['Annual', 'Semester'];

        for ($i = 1; $i <= 20; $i++) {
            $course = $courses[array_rand($courses)];
            $term = $terms[array_rand($terms)];

            // Define a data inicial como uma data aleatória até hoje
            $startDate = Carbon::now()->subDays(rand(0, 365));

            // Define o endDate com base no termo:
            if ($term === 'Annual') {
                // final do período: até 1 ano depois
                $endDate = (clone $startDate)->addYear();
            } else {
                // semestral = 6 meses depois
                $endDate = (clone $startDate)->addMonths(6);
            }

            SchoolClass::create([
                'course' => $course,
                'term' => $term,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);
        }
    }
}
