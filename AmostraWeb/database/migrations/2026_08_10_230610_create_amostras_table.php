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
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->restrictOnDelete();
            $table->string('codigo_amostra', 6)->unique();
            $table->string('produto_id');
            $table->integer('quantidade');
            $table->enum('status',['em_analise','aprovada','reprovada','atrasada','cancelada'])->default('em_analise');
            $table->date('prazo');
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
