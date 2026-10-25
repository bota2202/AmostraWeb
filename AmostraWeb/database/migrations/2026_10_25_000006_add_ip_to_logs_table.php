<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * De onde a ação foi feita: IP e navegador.
     */
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('ip', 45)->nullable()->after('observacao');
            $table->string('navegador')->nullable()->after('ip');
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn(['ip', 'navegador']);
        });
    }
};
