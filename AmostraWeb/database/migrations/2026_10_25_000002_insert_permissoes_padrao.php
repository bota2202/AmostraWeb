<?php

use App\Models\Permissao;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Cria as permissões padrão (CRUD de cada módulo, definidas em
     * config/amostraweb.php > permissoes_padrao) e remove as que não fazem
     * mais parte da lista. Permissões não são mais cadastradas pela tela.
     */
    public function up(): void
    {
        Permissao::sincronizarPadrao();
    }

    /**
     * As permissões ficam no banco; desfazer as tabelas de grupos já basta.
     */
    public function down(): void
    {
        //
    }
};
