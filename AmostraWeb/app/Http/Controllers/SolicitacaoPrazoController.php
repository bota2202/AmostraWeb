<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolicitacaoPrazoRequest;
use App\Http\Requests\UpdateSolicitacaoPrazoRequest;
use App\Models\Amostra;
use App\Models\Solicitacao_Prazo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Solicitações de alteração do prazo de avaliação (RN008 / RF018 / RF019).
 */
class SolicitacaoPrazoController extends Controller
{
    /**
     * Lista: "Aguardando minha decisão" (gestores/admin) e todas as visíveis (RF031).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $podeDecidir = $user->isAdmin() || ($user->isGestor() && $user->temPermissao('solicitacoes_prazo', 'aprovar'));
        $aba = $request->query('aba', $podeDecidir ? 'pendentes' : 'todas');
        $situacao = $request->query('situacao');

        $query = $aba === 'pendentes'
            ? Solicitacao_Prazo::query()->aguardandoDecisaoDe($user)
            : Solicitacao_Prazo::query()->visivelPara($user)
                ->when($situacao, fn (Builder $q, $s) => $q->where('situacao', $s));

        $solicitacoes = $query
            ->with(['amostra.produto', 'amostra.pedido.cliente', 'solicitante', 'aprovador'])
            ->orderByRaw("CASE WHEN situacao = 'pendente' THEN 0 ELSE 1 END")
            ->latest('data_solicitacao')
            ->paginate($this->porPagina())
            ->withQueryString();

        return view('solicitacoes.index', [
            'solicitacoes' => $solicitacoes,
            'aba' => $aba,
            'situacao' => $situacao,
            'podeDecidir' => $podeDecidir,
            'totalPendentes' => $podeDecidir ? Solicitacao_Prazo::query()->aguardandoDecisaoDe($user)->count() : 0,
        ]);
    }

    /**
     * Abre uma solicitação de prazo para a amostra (justificativa obrigatória).
     */
    public function store(StoreSolicitacaoPrazoRequest $request, Amostra $amostra)
    {
        $this->autorizarEscopo($amostra);

        if (! $amostra->emAberto()) {
            return back()->with('error', 'Só é possível pedir prazo para amostras em análise ou atrasadas.');
        }

        if ($amostra->solicitacoesPrazo()->where('situacao', 'pendente')->exists()) {
            return back()->with('error', 'Já existe uma solicitação de prazo pendente para esta amostra.');
        }

        $amostra->solicitacoesPrazo()->create([
            ...$request->validated(),
            'solicitante_id' => $request->user()->id,
            'prazo_atual' => $amostra->prazo_avaliacao,
            'situacao' => 'pendente',
            'data_solicitacao' => now(),
        ]);

        return redirect()->route('amostras.show', $amostra)
            ->with('success', 'Solicitação enviada. O prazo só muda depois da aprovação do gestor ou de um administrador.');
    }

    /**
     * Aprova ou reprova. Se aprovada, o prazo da amostra é atualizado.
     */
    public function update(UpdateSolicitacaoPrazoRequest $request, Solicitacao_Prazo $solicitacao)
    {
        $solicitacao->load('amostra.pedido');

        if ($motivo = $solicitacao->motivoParaNaoDecidir($request->user())) {
            return back()->with('error', $motivo);
        }

        DB::transaction(function () use ($request, $solicitacao) {
            $solicitacao->update([
                ...$request->validated(),
                'aprovador_id' => $request->user()->id,
                'data_aprovacao' => now(),
            ]);

            if ($solicitacao->situacao === 'aprovada') {
                $amostra = $solicitacao->amostra;
                $amostra->observacaoAuditoria = 'Prazo alterado por solicitação aprovada por '.$request->user()->name.'.';
                $amostra->prazo_avaliacao = $solicitacao->novo_prazo;
                if ($amostra->status === 'atrasada' && $solicitacao->novo_prazo->gte(today())) {
                    $amostra->status = 'em_analise';
                }
                $amostra->save();
            }
        });

        $texto = $solicitacao->situacao === 'aprovada' ? 'aprovada. O novo prazo já está valendo' : 'reprovada';

        return redirect()->back(fallback: route('amostras.show', $solicitacao->amostra_id))
            ->with('success', 'Solicitação de prazo da amostra '.$solicitacao->amostra->codigo.' '.$texto.'.');
    }

    /**
     * Quem pediu (ou um admin) pode desistir enquanto estiver pendente.
     */
    public function destroy(Request $request, Solicitacao_Prazo $solicitacao)
    {
        if (! $solicitacao->podeSerCanceladaPor($request->user())) {
            return back()->with('error', 'Só quem fez a solicitação pode cancelá-la, e apenas enquanto estiver pendente.');
        }

        $amostraId = $solicitacao->amostra_id;
        $solicitacao->observacaoAuditoria = 'Solicitação cancelada pelo solicitante';
        $solicitacao->delete();

        return redirect()->route('amostras.show', $amostraId)->with('success', 'Solicitação de prazo cancelada.');
    }
}
