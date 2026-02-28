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
            'Desenvolvimento de Sistemas',
            'Eletrotécnica',
            'Manutenção e Suporte em Informática',
        ];

        $annualCourses = [
            'Administração',
            'Desenvolvimento de Sistemas',
        ];

        foreach ($courses as $course) {
            $startDate = Carbon::now()->subDays(rand(0, 120));

            if (\in_array($course, $annualCourses, true)) {
                $term = 'Annual';
                $endDate = (clone $startDate)->addYear();
            } else {
                $term = 'Semester';
                $endDate = (clone $startDate)->addMonths(6);
            }

            SchoolClass::create([
                'course' => $course,
                'term' => $term,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);
        }

        $this->command->info('✅ Classes cadastradas com sucesso!');
    }
}
