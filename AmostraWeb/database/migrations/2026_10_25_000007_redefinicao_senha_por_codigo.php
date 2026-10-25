<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Redefinição de senha por código enviado ao e-mail:
     * - o e-mail passa a identificar a conta, então precisa ser único;
     * - o código guarda o número de tentativas erradas.
     */
    public function up(): void
    {
        $repetidos = DB::table('users')->select('email')->groupBy('email')->havingRaw('COUNT(*) > 1')->pluck('email');
        if ($repetidos->isNotEmpty()) {
            throw new RuntimeException('Há e-mails repetidos entre os usuários ('.$repetidos->implode(', ').'). Ajuste antes de rodar esta migration.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->unsignedTinyInteger('tentativas')->default(0)->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn('tentativas');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }
};
