<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Amostra;
use App\Models\Pedido;
use App\Models\Solicitacao_Prazo;

class DashboardController extends Controller
{
    public function showDashboard(){
        $resumo = [
            'em_analise' => Amostra::where('status', 'em_analise')->count(),
            'atrasadas' => Amostra::atrasadas()->count(),
            'pedidos_abertos' => Pedido::where('status', 'aberto')->count(),
            'solicitacoes_pendentes' => Solicitacao_Prazo::where('situacao', 'pendente')->count(),
        ];

        // Amostras em análise com prazo vencendo nos próximos 7 dias (ou já vencido)
        $prazosProximos = Amostra::with(['produto', 'pedido.cliente'])
            ->whereIn('status', ['em_analise', 'atrasada'])
            ->whereDate('prazo_avaliacao', '<=', today()->addDays(7))
            ->orderBy('prazo_avaliacao')
            ->limit(10)
            ->get();

        $solicitacoesPendentes = Solicitacao_Prazo::with(['amostra', 'solicitante'])
            ->where('situacao', 'pendente')
            ->oldest('data_solicitacao')
            ->limit(10)
            ->get();

        return view('dashboard.dashboard', [
            'resumo' => $resumo,
            'prazosProximos' => $prazosProximos,
            'solicitacoesPendentes' => $solicitacoesPendentes,
        ]);
    }
}
