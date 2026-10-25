<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * "lote" junta os registros de uma mesma ação: editar um cliente e mudar 3 campos
     * gera 3 registros (um por campo) com o mesmo lote, e a tela mostra uma ação só.
     */
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('lote', 36)->nullable()->after('id')->index();
        });

        // registros antigos: junta as alterações do mesmo registro, feitas pela mesma pessoa no mesmo instante
        $lotes = [];
        DB::table('logs')->orderBy('id')->chunkById(500, function ($logs) use (&$lotes) {
            foreach ($logs as $log) {
                $chave = in_array($log->acao, ['editar', 'alterar_status', 'permissoes', 'membros'], true)
                    ? implode('|', [$log->user_id, $log->tipo_registro, $log->registro_id, $log->data_hora])
                    : 'id'.$log->id;

                $lotes[$chave] ??= (string) Str::uuid();
                DB::table('logs')->where('id', $log->id)->update(['lote' => $lotes[$chave]]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex(['lote']);
            $table->dropColumn('lote');
        });
    }
};
