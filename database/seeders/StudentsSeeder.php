<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Helpers\Utils;
use App\Models\Student;
use App\Models\StudentSchoolClass;
use App\Models\View\SchoolClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StudentsSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = [
            'Ana',
            'João',
            'Maria',
            'Carlos',
            'Fernanda',
            'Pedro',
            'Juliana',
            'Lucas',
            'Patrícia',
            'Rafael',
            'Camila',
            'Rodrigo',
            'Larissa',
            'Gabriel',
            'Aline',
            'Thiago',
            'Beatriz',
            'Felipe',
            'Mariana',
            'André',
            'Clara',
            'Diego',
            'Bianca',
            'Eduardo',
            'Letícia',
            'Marcelo',
            'Natália',
            'Vinícius',
            'Sofia',
            'Fábio',
            'Isabela',
            'Leandro',
            'Mônica',
            'Otávio',
            'Helena',
            'Daniel',
            'Vitória',
            'Bruno',
            'Manuela',
            'Gustavo',
        ];

        $lastNames = [
            'Silva',
            'Santos',
            'Oliveira',
            'Souza',
            'Lima',
            'Costa',
            'Pereira',
            'Ferreira',
            'Almeida',
            'Nascimento',
            'Araújo',
            'Rocha',
            'Martins',
            'Barbosa',
            'Ribeiro',
            'Dias',
            'Teixeira',
            'Carvalho',
            'Gomes',
            'Melo',
            'Castro',
            'Mendes',
            'Correia',
            'Cardoso',
            'Monteiro',
            'Moreira',
            'Pinto',
            'Araújo',
            'Batista',
            'Campos',
            'Freitas',
            'Vieira',
            'Machado',
            'Farias',
            'Rezende',
            'Ramos',
            'Peixoto',
            'Cavalcanti',
            'Fonseca',
            'Tavares',
        ];

        $created = 0;

        $classes = SchoolClass::all();

        if ($classes->isEmpty()) {
            $this->command->warn('❌ Nenhuma turma encontrada na tabela schoolclass.');

            return;
        }

        for ($i = 1; $i <= 550; ++$i) {
            $class = $classes->random();

            $name = Arr::random($firstNames) . ' ' . Arr::random($lastNames);

            $data = [
                'name' => $name,
                'cpf' => $this->randomDigits(11),
                'email' => "aluno{$i}@example.com",
                'phone' => $this->randomPhone(),
                'course' => $class->course,
                'term' => $class->term,
                'period' => $class->period,
            ];

            try {
                DB::beginTransaction();

                $student = Student::create(Arr::only($data, ['name', 'cpf', 'email', 'phone']));

                StudentSchoolClass::create([
                    'student_id' => Utils::convertUuidToBinary($student->id),
                    'school_class_id' => Utils::convertUuidToBinary($class->id),
                ]);

                DB::commit();
                ++$created;
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
