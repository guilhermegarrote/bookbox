<?php

namespace Database\Seeders;

use App\Helpers\Utils;
use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\Student;
use App\Models\Copy;
use Illuminate\Support\Carbon;

class LoansSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $copies   = Copy::all();

        if ($students->isEmpty() || $copies->isEmpty()) {
            $this->command->warn("❌ Não há alunos ou cópias disponíveis. Rode os seeders de Student e Copy primeiro.");
            return;
        }

        $created = 0;
        $total   = 50;

        $activeTarget = (int) ceil($total * 0.2);
        $activeCount  = 0;

        for ($i = 1; $i <= $total; $i++) {
            try {
                $student = $students->shuffle()->shift();
                $copy = $copies->shuffle()->shift();

                $start = Carbon::now()->subDays(rand(1, 60));
                $due   = (clone $start)->addDays(rand(7, 15));

                $returned = null;
                $active   = false;

                if ($activeCount < $activeTarget) {
                    $active   = true;
                    $returned = null;
                    $activeCount++;
                } else {
                    if (rand(0, 1)) {
                        $returned = (clone $start)->addDays(rand(1, max(1, $due->diffInDays($start))));
                        $active   = false;
                    } else {
                        $returned = null;
                        $active   = false;
                        $due = Carbon::now()->subDays(rand(1, 5));
                    }
                }

                Loan::create([
                    'student_id'    => Utils::convertUuidToBinary($student->id),
                    'copy_id'       => Utils::convertUuidToBinary($copy->id),
                    'start_date'    => $start,
                    'due_date'      => $due,
                    'returned_date' => $returned,
                    'active'        => $active,
                ]);

                $created++;
            } catch (\Throwable $e) {
                $this->command->error("❗ Erro ao criar empréstimo: {$e->getMessage()}");
            }
        }

        $this->command->info("✅ Seeder de empréstimos finalizado. {$created} empréstimos criados ({$activeCount} ativos).");
    }
}
