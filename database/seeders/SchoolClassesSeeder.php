<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SchoolClassesSeeder extends Seeder
{
    public function run()
    {
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
            'Engenharia de Produção',
            'Engenharia Elétrica',
            'Engenharia Mecânica',
            'Engenharia de Software',
            'Sistemas de Informação',
            'Análise e Desenvolvimento de Sistemas',
            'Contabilidade',
            'Gestão de Recursos Humanos',
            'Marketing',
            'Publicidade e Propaganda',
            'Jornalismo',
            'Pedagogia',
            'Educação Física',
            'Farmácia',
            'Biomedicina',
            'Odontologia',
            'Veterinária',
            'Nutrição',
            'Design Gráfico',
            'Design de Interiores',
            'Moda',
            'Relações Internacionais',
            'Serviço Social',
            'Turismo',
            'Hotelaria',
            'Letras',
            'História',
            'Geografia',
            'Matemática',
            'Física',
            'Química',
            'Biologia',
        ];

        $terms = ['Annual', 'Semester'];

        for ($i = 1; $i <= 50; ++$i) {
            $course = $courses[array_rand($courses)];
            $term = $terms[array_rand($terms)];

            $startDate = Carbon::now()->subDays(rand(0, 365));

            if ($term === 'Annual') {
                $endDate = (clone $startDate)->addYear();
            } else {
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
