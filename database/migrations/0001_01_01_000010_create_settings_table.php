<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação da configuração (UUID em formato binário).');

            $table->string('key', 255)
                ->unique()
                ->comment('Nome da configuração.');

            $table->string('value', 255)
                ->comment('Valor associado à configuração.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
