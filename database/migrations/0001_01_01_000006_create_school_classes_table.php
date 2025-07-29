<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolClassesTable extends Migration
{
    public function up()
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação da turma (UUID em formato binário).');

            $table->string('course', 150)
                ->comment('Nome do curso ao qual a turma está integrada.');

            $table->enum('term', ['Annual', 'Semester'])
                ->comment('Período de vigência da turma, definido como anual ou semestral.');

            $table->date('start_date')
                ->comment('Data em que a turma iniciou as aulas.');

            $table->date('end_date')
                ->comment('Data em que a turma concluirá as aulas.');
        });
    }

    public function down()
    {
        Schema::dropIfExists('school_classes');
    }
}
