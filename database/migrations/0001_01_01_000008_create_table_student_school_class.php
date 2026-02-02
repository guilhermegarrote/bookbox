<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_school_class', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação da relação específica entre aluno e turma (UUID em formato binário). ')
            ;

            $table->binary('student_id', 16)
                ->comment('Código de identificação do aluno (UUID em formato binário).')
            ;

            $table->binary('school_class_id', 16)
                ->comment('Código de identificação da turma (UUID em formato binário).')
            ;

            $table->unique(['student_id', 'school_class_id']);
            $table->index('school_class_id');

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_school_class');
    }
};
