<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação do livro (UUID em formato binário).');

            $table->string('isbn', 13)
                ->unique()
                ->comment('Padrão Internacional de Numeração do Livro.');

            $table->string('title', 255)
                ->comment('Título do livro.');

            $table->string('author', 300)
                ->comment('Autor do livro.');

            $table->binary('genre_id', 16)
                ->comment('Código de identificação do gênero, relaciona o livro ao seu gênero (UUID em formato binário).');

            $table->string('publisher', 150)
                ->comment('Empresa responsável pela fabricação e lançamento do livro.');

            $table->unique(['title', 'author', 'publisher']);

            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};

