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
    private array $generatedCpfs = [];
    private array $generatedEmails = [];

    public function run(): void
    {
        $firstNames = [
            'Ana','João','Maria','Carlos','Fernanda','Pedro','Juliana','Lucas','Patrícia','Rafael',
            'Camila','Rodrigo','Larissa','Gabriel','Aline','Thiago','Beatriz','Felipe','Mariana','André',
            'Clara','Diego','Bianca','Eduardo','Letícia','Marcelo','Natália','Vinícius','Sofia','Fábio',
            'Isabela','Leandro','Mônica','Otávio','Helena','Daniel','Vitória','Bruno','Manuela','Gustavo',
        ];

        $lastNames = [
            'Silva','Santos','Oliveira','Souza','Lima','Costa','Pereira','Ferreira','Almeida',
            'Nascimento','Araújo','Rocha','Martins','Barbosa','Ribeiro','Dias','Teixeira','Carvalho',
            'Gomes','Melo','Castro','Mendes','Correia','Cardoso','Monteiro','Moreira','Pinto','Batista',
            'Campos','Freitas','Vieira','Machado','Farias','Rezende','Ramos','Peixoto','Cavalcanti',
            'Fonseca','Tavares',
        ];

        $classes = SchoolClass::all();

        if ($classes->isEmpty()) {
            $this->command->warn('❌ Nenhuma turma encontrada na tabela schoolclass.');
            return;
        }

        $created = 0;

        for ($i = 1; $i <= 200; $i++) {

            $name = Arr::random($firstNames) . ' '
                . Arr::random($lastNames)
                . (rand(0, 1) ? ' ' . Arr::random($lastNames) : '');

            $cpf = $this->generateUniqueCpf();
            $email = $this->generateUniqueEmail($name, $i);
            $phone = $this->generatePhone();

            $class = $classes->random();

            try {
                DB::beginTransaction();

                $student = Student::create([
                    'name'  => $name,
                    'cpf'   => $cpf,
                    'email' => $email,
                    'phone' => $phone,
                ]);

                StudentSchoolClass::create([
                    'student_id'      => Utils::convertUuidToBinary($student->id),
                    'school_class_id' => Utils::convertUuidToBinary($class->id),
                ]);

                DB::commit();
                $created++;

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->command->error("❗ Erro ao cadastrar {$name}: {$e->getMessage()}");
            }
        }

        $this->command->info("🎓 Seeder finalizado. {$created} alunos criados com sucesso!");
    }

    private function generateUniqueCpf(): string
    {
        do {
            $cpf = $this->generateValidCpf();
        } while (isset($this->generatedCpfs[$cpf]));

        $this->generatedCpfs[$cpf] = true;
        return $cpf;
    }

    private function generateValidCpf(): string
    {
        $numbers = [];

        for ($i = 0; $i < 9; $i++) {
            $numbers[$i] = rand(0, 9);
        }

        $d1 = 0;
        for ($i = 0, $j = 10; $i < 9; $i++, $j--) {
            $d1 += $numbers[$i] * $j;
        }
        $d1 = ($d1 % 11 < 2) ? 0 : 11 - ($d1 % 11);

        $d2 = 0;
        for ($i = 0, $j = 11; $i < 9; $i++, $j--) {
            $d2 += $numbers[$i] * $j;
        }
        $d2 += $d1 * 2;
        $d2 = ($d2 % 11 < 2) ? 0 : 11 - ($d2 % 11);

        return implode('', $numbers) . $d1 . $d2;
    }

    private function generateUniqueEmail(string $name, int $index): string
    {
        $base = strtolower(str_replace(' ', '.', $name));

        do {
            $email = "{$base}.{$index}@example.com";
            $index++;
        } while (isset($this->generatedEmails[$email]));

        $this->generatedEmails[$email] = true;

        return $email;
    }

    private function generatePhone(): string
    {
        $dddValidos = [
            11,12,13,14,15,16,17,18,19,21,22,24,27,28,
            31,32,33,34,35,37,38,41,42,43,44,45,46,47,48,49,
            51,53,54,55,61,62,64,63,65,66,67,68,69,71,73,74,
            75,77,79,81,87,82,83,84,85,88,86,89,91,93,94,92,
            95,96,97,98,99
        ];

        $ddd = Arr::random($dddValidos);

        return sprintf('%d9%08d', $ddd, rand(0, 99999999));
    }
}
