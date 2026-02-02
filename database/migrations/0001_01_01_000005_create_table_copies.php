<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('copies', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação do exemplar (UUID em formato binário).')
            ;

            $table->binary('book_id', 16)
                ->comment('Código de identificação do livro vinculado ao exemplar.')
            ;

            $table->unsignedSmallInteger('number')
                ->comment('Número do exemplar.')
            ;

            $table->unique(['book_id', 'number']);

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copies');
    }
};
