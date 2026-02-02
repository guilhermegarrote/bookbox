<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id()
                ->comment('Chave primária auto-incrementável da tabela')
            ;

            $table->string('uuid')
                ->unique()
                ->comment('Identificador único do job que falhou')
            ;

            $table->text('connection')
                ->comment('Nome da conexão (ex: redis, database) usada pelo job')
            ;

            $table->text('queue')
                ->comment('Nome da fila em que o job estava')
            ;

            $table->longText('payload')
                ->comment('Dados completos do job, incluindo classe e parâmetros')
            ;

            $table->longText('exception')
                ->comment('Informação detalhada da exceção que causou a falha')
            ;

            $table->timestamp('failed_at')
                ->useCurrent()
                ->comment('Data e hora em que o job falhou')
            ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};
