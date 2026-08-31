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
            $table->string('categoria_produto')->nullable();
            $table->string('nbs')->nullable();
            $table->string('tipo')->nullable();
            $table->string('ncm')->nullable();
            $table->string('servico')->nullable();
            $table->string('cest')->nullable();
            $table->string('anp')->nullable();
            $table->string('aplicacao_entrada')->nullable();
            $table->string('aplicacao_saida')->nullable();
            $table->string('origem_mercadoria')->nullable();
            $table->boolean('controle_verba')->default(false);
            $table->boolean('status')->default(true);
            $table->string('marca')->nullable();
            $table->date('data_revisao')->nullable();
            $table->string('classe')->nullable();
            $table->string('ca')->nullable();
            $table->string('id_categoria')->nullable();
            $table->string('id_preco')->nullable();
            $table->string('tributacao_municipio')->nullable();

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