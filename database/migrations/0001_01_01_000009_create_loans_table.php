<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação do empréstimo.');

            $table->binary('student_id', 16)
                ->comment('Código de identificação do aluno que realizou o empréstimo.');

            $table->binary('copy_id', 16)
                ->comment('Código de identificação do exemplar do livro emprestado.');

            $table->date('start_date')
                ->default(DB::raw('CURRENT_TIMESTAMP'))
                ->comment('Data em que o empréstimo foi realizado.');

            $table->date('due_date')
                ->comment('Data limite para devolução do exemplar.');

            $table->date('returned_date')
                ->nullable()
                ->comment('Data em que o exemplar foi devolvido.');

            $table->unique('copy_id');
            $table->index('student_id');

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('copy_id')->references('id')->on('copies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
