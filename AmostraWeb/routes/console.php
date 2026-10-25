<?php

use App\Models\Amostra;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Rotinas automáticas do sistema
|--------------------------------------------------------------------------
| Para o agendador rodar em produção, configure no servidor (cron):
|   * * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
| Em desenvolvimento: php artisan schedule:work
*/

// RN009 - amostras com prazo vencido passam para "Atrasada"
Artisan::command('amostras:atualizar-atrasadas', function () {
    $total = Amostra::marcarAtrasadas();
    $this->info($total === 0
        ? 'Nenhuma amostra com prazo vencido.'
        : "{$total} amostra(s) marcada(s) como atrasada(s).");
})->purpose('Marca como "Atrasada" as amostras em análise com o prazo de avaliação vencido');

Schedule::command('amostras:atualizar-atrasadas')->hourly()->withoutOverlapping();
