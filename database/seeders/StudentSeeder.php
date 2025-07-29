<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\Student;
use App\Models\StudentSchoolClass;
use App\Models\View\SchoolClass;
use App\Helpers\Utils;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            'Administração', 'Engenharia Civil', 'Direito', 'Medicina', 'Arquitetura',
            'Psicologia', 'Ciência da Computação', 'Economia', 'Fisioterapia', 'Enfermagem',
        ];

        $terms = ['Annual', 'Semester'];
        $created = 0;

        for ($i = 1; $i <= 100; $i++) {
            $data = [
                'name'   => "Aluno Teste $i",
                'cpf'    => $this->randomDigits(11),
                'email'  => "aluno{$i}@example.com",
                'phone'  => $this->randomPhone(),
                'course' => $courses[array_rand($courses)],
                'term'   => $terms[array_rand($terms)],
                'period' => rand(1, 8),
            ];

            try {
                $class = SchoolClass::where(Arr::only($data, ['course', 'term', 'period']))->first();

                if (!$class) {
                    $this->command->warn("❌ Turma não encontrada para {$data['name']} ({$data['course']} - {$data['term']} - {$data['period']}º). Pulando...");
                    continue;
                }

                DB::beginTransaction();

                $student = Student::create(Arr::only($data, ['name', 'cpf', 'email', 'phone']));

                StudentSchoolClass::create([
                    'student_id'      => Utils::convertUuidToBinary($student->id),
                    'school_class_id' => Utils::convertUuidToBinary($class->id),
                ]);

                DB::commit();
                $created++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->command->error("❗ Erro ao cadastrar {$data['name']}: {$e->getMessage()}");
            }
        }

        $this->command->info("✅ Seeder finalizado. {$created} alunos criados.");
    }

    private function randomDigits(int $length): string
    {
        return substr(str_shuffle(str_repeat('0123456789', (int) ceil($length / 10))), 0, $length);
    }

    private function randomPhone(): string
    {
        $ddd = rand(11, 99);
        return $ddd . '9' . $this->randomDigits(8);
    }
}
