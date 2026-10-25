<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos do cadastro de produtos do ERP que faltavam (08 - Modelo Conceitual).
     */
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->string('categoria')->nullable()->after('grupo');
            $table->string('nbs')->nullable()->after('categoria');
            $table->string('servico')->nullable()->after('ncm');
            $table->string('cest')->nullable()->after('servico');
            $table->string('anp')->nullable()->after('cest');
            $table->string('classe')->nullable()->after('data_revisao');
            $table->string('ca')->nullable()->after('classe');
            $table->string('id_categoria')->nullable()->after('ca');
            $table->string('id_preco')->nullable()->after('id_categoria');
            $table->string('tributacao_municipio')->nullable()->after('id_preco');
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn([
                'categoria', 'nbs', 'servico', 'cest', 'anp',
                'classe', 'ca', 'id_categoria', 'id_preco', 'tributacao_municipio',
            ]);
        });
    }
};
