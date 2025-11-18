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
        Schema::create('jobs', function (Blueprint $table) {

            $table->bigIncrements('id')
                ->comment('Identificador único do job na fila');

            $table->string('queue')
                ->index()
                ->comment('Nome da fila onde o job foi enfileirado');

            $table->longText('payload')
                ->comment('Conteúdo serializado contendo os dados do job');

            $table->unsignedTinyInteger('attempts')
                ->comment('Quantidade de tentativas já executadas para este job');

            $table->unsignedInteger('reserved_at')
                ->nullable()
                ->comment('Timestamp de quando o job foi reservado por um worker');

            $table->unsignedInteger('available_at')
                ->comment('Timestamp indicando quando o job estará disponível para processamento');

            $table->unsignedInteger('created_at')
                ->comment('Timestamp de criação do job na fila');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
