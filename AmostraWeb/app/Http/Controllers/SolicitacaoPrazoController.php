<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolicitacaoPrazoRequest;
use App\Http\Requests\UpdateSolicitacaoPrazoRequest;
use App\Models\Amostra;
use App\Models\Solicitacao_Prazo;
use Illuminate\Support\Facades\DB;

/**
 * Solicitações de novo prazo de avaliação de uma amostra.
 * As telas ficam dentro da página da amostra (amostras.show).
 */
class SolicitacaoPrazoController extends Controller
{
    /**
     * Abre uma nova solicitação de prazo para a amostra.
     */
    public function store(StoreSolicitacaoPrazoRequest $request, Amostra $amostra)
    {
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

        return redirect()->route('amostras.show', $amostra)->with('success', 'Solicitação de prazo enviada com sucesso');
    }

    /**
     * Aprova ou reprova a solicitação. Se aprovada, o prazo da amostra é atualizado.
     */
    public function update(UpdateSolicitacaoPrazoRequest $request, Solicitacao_Prazo $solicitacao)
    {
        if ($solicitacao->situacao !== 'pendente') {
            return back()->with('error', 'Esta solicitação já foi respondida.');
        }

        DB::transaction(function () use ($request, $solicitacao) {
            $solicitacao->update([
                ...$request->validated(),
                'aprovador_id' => $request->user()->id,
                'data_aprovacao' => now(),
            ]);

            if ($solicitacao->situacao === 'aprovada') {
                $amostra = $solicitacao->amostra;
                $amostra->prazo_avaliacao = $solicitacao->novo_prazo;
                if ($amostra->status === 'atrasada') {
                    $amostra->status = 'em_analise';
                }
                $amostra->save();
            }
        });

        $texto = $solicitacao->situacao === 'aprovada' ? 'aprovada' : 'reprovada';
        return redirect()->route('amostras.show', $solicitacao->amostra_id)->with('success', 'Solicitação de prazo '.$texto.' com sucesso');
    }

    /**
     * Cancela (exclui) uma solicitação que ainda está pendente.
     */
    public function destroy(Solicitacao_Prazo $solicitacao)
    {
        if ($solicitacao->situacao !== 'pendente') {
            return back()->with('error', 'Só é possível cancelar solicitações pendentes.');
        }

        $amostraId = $solicitacao->amostra_id;
        $solicitacao->delete();
        return redirect()->route('amostras.show', $amostraId)->with('success', 'Solicitação de prazo cancelada');
    }
}
