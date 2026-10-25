<?php

namespace App\Http\Controllers;

use App\Models\Amostra;
use DomainException;
use Illuminate\Http\Request;

/**
 * Registra o resultado da avaliação (Aprovada / Reprovada) e o cancelamento
 * da amostra (RF015 / RF017). Cada amostra tem ciclo independente (RN012).
 */
class AmostraStatusController extends Controller
{
    // RN010 - pode ser aprovada mesmo atrasada
    public function aprovar(Request $request, Amostra $amostra)
    {
        $dados = $request->validateWithBag('status', [
            'observacao' => ['nullable', 'string', 'max:1000'],
        ]);

        return $this->alterar($amostra, 'aprovada', $dados['observacao'] ?? 'Amostra aprovada pelo cliente.', 'aprovada');
    }

    // RN011 - pode ser reprovada mesmo atrasada
    public function reprovar(Request $request, Amostra $amostra)
    {
        $dados = $request->validateWithBag('status', [
            'observacao' => ['nullable', 'string', 'max:1000'],
        ]);

        return $this->alterar($amostra, 'reprovada', $dados['observacao'] ?? 'Amostra reprovada pelo cliente.', 'reprovada');
    }

    // RN013 - cancelamento exige motivo e trava a amostra
    public function cancelar(Request $request, Amostra $amostra)
    {
        $dados = $request->validateWithBag('cancelamento', [
            'motivo' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'motivo.required' => 'Informe o motivo do cancelamento.',
            'motivo.min' => 'Descreva o motivo com pelo menos 5 caracteres.',
        ]);

        return $this->alterar($amostra, 'cancelada', 'Cancelamento: '.$dados['motivo'], 'cancelada');
    }

    private function alterar(Amostra $amostra, string $status, string $observacao, string $texto)
    {
        $this->autorizarEscopo($amostra);

        try {
            $amostra->alterarStatus($status, $observacao);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('amostras.show', $amostra)->with('success', 'Amostra '.$amostra->codigo.' '.$texto.'.');
    }
}
