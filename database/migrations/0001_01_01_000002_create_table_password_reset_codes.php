<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('password_reset_codes', function (Blueprint $table) {
            $table->binary('id', 16)
                ->primary()
                ->comment('Código de identificação do código de redefinição de senha (UUID em formato binário).')
            ;

            $table->binary('user_id', 16)
                ->comment('Código de identificação do usuário que solicitou a redefinição de senha (UUID em formato binário).')
            ;

            $table->string('value', 60)
                ->comment('Hash do código enviado por e-mail para redefinição de senha.')
            ;

            $table->dateTime('expiration')
                ->comment('Data e hora em que o código expira.')
            ;

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_codes');
    }
};
