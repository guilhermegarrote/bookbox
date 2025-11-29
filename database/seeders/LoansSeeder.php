<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Helpers\Utils;
use App\Models\Copy;
use App\Models\Loan;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LoansSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $copies   = Copy::all();

        if ($students->isEmpty() || $copies->isEmpty()) {
            $this->command->warn('❌ Não há alunos ou cópias disponíveis.');
            return;
        }

        $total = 40;
        $perGroup = (int) ($total / 4);

        $countFinished = $perGroup;
        $count14to7    = $perGroup;
        $count7to0     = $perGroup;
        $countLate     = $perGroup;

        $created = 0;

        foreach (range(1, $total) as $i) {

            $student = $students->random();
            $copy    = $copies->random();

            $start = Carbon::now()->subDays(rand(10, 40));
            $returned = null;

            if ($countFinished > 0) {
                $due = (clone $start)->addDays(rand(7, 15));
                $returned = (clone $due)->subDays(rand(0, 5));

                $countFinished--;
            } elseif ($count14to7 > 0) {
                $due = Carbon::now()->addDays(rand(7, 14));
                $returned = null;

                $count14to7--;
            } elseif ($count7to0 > 0) {
                $due = Carbon::now()->addDays(rand(0, 7));
                $returned = null;

                $count7to0--;
            } else {
                $due = Carbon::now()->subDays(rand(1, 15));
                $returned = null;

                $countLate--;
            }

            Loan::create([
                'student_id'    => Utils::convertUuidToBinary($student->id),
                'copy_id'       => Utils::convertUuidToBinary($copy->id),
                'start_date'    => $start,
                'due_date'      => $due,
                'returned_date' => $returned,
            ]);

            $created++;
        }

        $this->command->info("✅ Seeder finalizado. {$created} empréstimos gerados (4 estados equilibrados).");
    }
}
