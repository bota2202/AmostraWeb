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
        Schema::create('amostras', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 6)->unique();

            $table->foreignId('pedido_id')
                ->nullable()
                ->constrained('pedidos')
                ->restrictOnDelete();

            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->restrictOnDelete();

            $table->integer('quantidade');

            $table->enum('status', [
                'em_analise',
                'atrasada',
                'aprovada',
                'reprovada',
                'cancelada'
            ])->default('em_analise');

            $table->date('prazo_avaliacao');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amostras');
    }
};