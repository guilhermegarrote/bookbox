<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação do usuário (UUID em formato binário).');

            $table->string('name', 100)
                ->comment('Nome do usuário.');

            $table->binary('email', 512)
                ->unique()
                ->comment('Endereço de e-mail do usuário (armazenado como binário por segurança).');

            $table->binary('email_hash', 32)
                ->unique()
                ->comment('Hash do e-mail do usuário, utilizado para agilizar buscas e autenticação.');

            $table->string('password', 60)
                ->comment('Hash da senha do usuário, gerado para proteger o acesso à conta.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
