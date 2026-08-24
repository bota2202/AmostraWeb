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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_empresa')->unique();
            $table->string('razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->boolean('status')->default(true);
            $table->string('tipo');
            $table->string('grupo_fiscal')->nullable();
            $table->string('tipo_empresa')->nullable();
            $table->string('grupo_economico')->nullable();
            $table->string('cnpj_cpf')->unique();
            $table->text('endereco_completo')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};