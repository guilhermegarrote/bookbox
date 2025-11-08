<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação do aluno (UUID em formato binário).')
            ;

            $table->string('name', 100)
                ->comment('Nome do aluno.')
            ;

            $table->binary('cpf', 64)
                ->unique()
                ->comment('Cadastro de Pessoa Física do aluno (armazenado em formato binário).')
            ;

            $table->binary('cpf_hash', 32)
                ->unique()
                ->comment('Hash utilizado para agilizar pesquisas pelo CPF do aluno.')
            ;

            $table->binary('email', 512)
                ->unique()
                ->comment('Endereço de e-mail do aluno (armazenado em formato binário).')
            ;

            $table->binary('email_hash', 32)
                ->unique()
                ->comment('Hash utilizado para agilizar pesquisas pelo e-mail do aluno.')
            ;

            $table->binary('phone', 64)
                ->nullable()
                ->unique()
                ->comment('Número de telefone do aluno (armazenado em formato binário).')
            ;

            $table->binary('phone_hash', 32)
                ->nullable()
                ->unique()
                ->comment('Hash utilizado para agilizar pesquisas pelo telefone do aluno.')
            ;
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
}
