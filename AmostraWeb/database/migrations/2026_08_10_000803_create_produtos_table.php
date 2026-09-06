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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_produto')->unique();
            $table->string('descricao');
            $table->string('familia')->nullable();
            $table->string('subfamilia')->nullable();
            $table->string('grupo')->nullable();
            $table->string('tipo')->nullable();
            $table->string('ncm')->nullable();
            $table->string('aplicacao_entrada')->nullable();
            $table->string('aplicacao_saida')->nullable();
            $table->string('origem_mercadoria')->nullable();
            $table->string('controle_verba')->nullable();
            $table->boolean('status')->default(true);
            $table->string('marca')->nullable();
            $table->date('data_revisao')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};