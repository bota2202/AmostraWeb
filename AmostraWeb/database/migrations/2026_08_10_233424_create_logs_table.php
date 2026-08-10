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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('modulo');
            $table->string('tipo_registro');
            $table->unsignedBigInteger('registro_id');
            $table->string('acao');
            $table->string('campo_alterado')->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_novo')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamp('data_hora');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};