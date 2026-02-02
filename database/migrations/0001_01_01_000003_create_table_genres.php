<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação do gênero (UUID em formato binário).')
            ;

            $table->string('name', 100)
                ->unique()
                ->comment('Nome do gênero.')
            ;

            $table->char('color_hex', 6)
                ->unique()
                ->comment('Cor do gênero em formato hexadecimal.')
            ;
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};
