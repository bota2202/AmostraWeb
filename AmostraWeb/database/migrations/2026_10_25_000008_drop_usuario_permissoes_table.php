<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * As permissões passam a ser dadas só por grupo: a tabela de permissões
     * marcadas direto no usuário deixa de existir.
     */
    public function up(): void
    {
        Schema::dropIfExists('usuario_permissoes');
    }

    public function down(): void
    {
        Schema::create('usuario_permissoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('permissao_id')->constrained('permissoes')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'permissao_id']);
        });
    }
};
