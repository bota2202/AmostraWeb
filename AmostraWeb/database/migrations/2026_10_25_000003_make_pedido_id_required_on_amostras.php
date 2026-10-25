<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Toda amostra pertence obrigatoriamente a um pedido (RN005 / RN024).
     */
    public function up(): void
    {
        $semPedido = DB::table('amostras')->whereNull('pedido_id')->count();
        if ($semPedido > 0) {
            throw new RuntimeException(
                "Existem {$semPedido} amostra(s) sem pedido. Vincule-as a um pedido ou exclua-as antes de rodar esta migration."
            );
        }

        Schema::table('amostras', function (Blueprint $table) {
            $table->foreignId('pedido_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('amostras', function (Blueprint $table) {
            $table->foreignId('pedido_id')->nullable()->change();
        });
    }
};
