<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitacoes_prazo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amostra_id')
                ->constrained('amostras')
                ->restrictOnDelete();
            $table->foreignId('solicitante_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('aprovador_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();
            $table->text('justificativa');
            $table->date('prazo_atual');
            $table->date('novo_prazo');
            $table->enum('situacao', [
                'pendente',
                'aprovada',
                'reprovada'
            ])->default('pendente');
            $table->text('motivo_aprovacao_reprovacao')->nullable();
            $table->timestamp('data_solicitacao');
            $table->timestamp('data_aprovacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_prazo');
    }
};