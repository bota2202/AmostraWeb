<?php

namespace App\Http\Middleware;

use App\Models\Amostra;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante o status "Atrasada" automático (RN009) mesmo sem o agendador
 * configurado no servidor: roda no máximo uma vez a cada 10 minutos,
 * na primeira requisição de um usuário logado.
 *
 * O agendador (routes/console.php) também executa a mesma rotina de hora em hora.
 */
class AtualizarAmostrasAtrasadas
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && Cache::add('amostras:atualizacao-atrasadas', true, now()->addMinutes(10))) {
            try {
                Amostra::marcarAtrasadas();
            } catch (\Throwable $e) {
                // nunca impede o uso do sistema; o erro fica no log do Laravel
                report($e);
            }
        }

        return $next($request);
    }
}
