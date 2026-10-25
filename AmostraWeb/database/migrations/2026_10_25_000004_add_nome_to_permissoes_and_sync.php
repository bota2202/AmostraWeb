<?php

use App\Models\Permissao;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inclui o campo "nome" da permissão (Modelo Lógico) e sincroniza a lista
     * revisada de permissões padrão (config amostraweb.permissoes_padrao).
     */
    public function up(): void
    {
        Schema::table('permissoes', function (Blueprint $table) {
            $table->string('nome')->nullable()->after('id');
        });

        Permissao::sincronizarPadrao();
    }

    public function down(): void
    {
        Schema::table('permissoes', function (Blueprint $table) {
            $table->dropColumn('nome');
        });
    }
};
