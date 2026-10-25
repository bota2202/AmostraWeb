<?php

namespace App\Http\Controllers;

use App\Models\Amostra;
use App\Models\Pedido;
use App\Models\Solicitacao_Prazo;
use Illuminate\Http\Request;

/**
 * Dashboard inicial (RF003 / RF004) com alertas de prazo (RF030) e
 * solicitações pendentes (RF031), sempre no escopo do usuário (RN017).
 */
class DashboardController extends Controller
{
    public function showDashboard(Request $request)
    {
        $user = $request->user();
        $diasAlerta = (int) config('amostraweb.dias_alerta_prazo', 7);
        $amostras = fn () => Amostra::query()->visivelPara($user);

        // gestores (do estabelecimento) e administradores decidem solicitações de prazo
        $decideSolicitacoes = $user->isAdmin() || ($user->isGestor() && $user->temPermissao('solicitacoes_prazo', 'aprovar'));

        $porStatus = $amostras()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $finalizadas90 = $amostras()->whereIn('status', ['aprovada', 'reprovada'])
            ->where('updated_at', '>=', now()->subDays(90))
            ->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $avaliadas = (int) $finalizadas90->sum();

        $solicitacoes = $decideSolicitacoes
            ? Solicitacao_Prazo::query()->aguardandoDecisaoDe($user)
            : Solicitacao_Prazo::query()->where('solicitante_id', $user->id)->where('situacao', 'pendente');

        return view('dashboard.dashboard', [
            'resumo' => [
                'em_analise' => (int) ($porStatus['em_analise'] ?? 0),
                'atrasadas' => $amostras()->atrasadas()->count(),
                'vencendo' => $amostras()->where('status', 'em_analise')
                    ->whereDate('prazo_avaliacao', '>=', today())
                    ->whereDate('prazo_avaliacao', '<=', today()->addDays($diasAlerta))->count(),
                'pedidos_abertos' => Pedido::query()->visivelPara($user)->where('status', 'aberto')->count(),
                'solicitacoes' => (clone $solicitacoes)->count(),
            ],
            'porStatus' => collect(Amostra::STATUS)->map(fn ($nome, $status) => (int) ($porStatus[$status] ?? 0)),
            'totalAmostras' => (int) $porStatus->sum(),
            'taxaAprovacao' => $avaliadas > 0 ? round(($finalizadas90['aprovada'] ?? 0) / $avaliadas * 100) : null,
            'avaliadas90' => $avaliadas,
            'prazosProximos' => $amostras()->emAberto()
                ->whereDate('prazo_avaliacao', '<=', today()->addDays($diasAlerta))
                ->with(['produto', 'pedido.cliente'])
                ->orderBy('prazo_avaliacao')
                ->limit(8)
                ->get(),
            'solicitacoesLista' => $solicitacoes->with(['amostra', 'solicitante'])->oldest('data_solicitacao')->limit(6)->get(),
            'decideSolicitacoes' => $decideSolicitacoes,
            'diasAlerta' => $diasAlerta,
        ]);
    }
}
