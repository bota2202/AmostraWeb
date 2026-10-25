<?php

namespace Database\Seeders;

use App\Models\Amostra;
use App\Models\Solicitacao_Prazo;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Solicitações de prazo: algumas pendentes (aparecem no dashboard),
 * algumas aprovadas e uma reprovada.
 */
class SolicitacaoPrazoSeeder extends Seeder
{
    public function run(): void
    {
        $representantes = User::where('cargo', 'representante')->where('status', true)->pluck('id');
        $gestores = User::where('cargo', 'gestor')->pluck('id');
        $amostras = Amostra::whereIn('status', ['em_analise', 'atrasada'])->with('pedido')->get()->shuffle();

        $justificativas = [
            'Cliente ainda não conseguiu testar o produto em campo.',
            'Responsável pela avaliação está de férias até a próxima semana.',
            'A obra atrasou e o produto só será instalado no fim do mês.',
            'Cliente pediu mais tempo para comparar com o fornecedor atual.',
        ];

        // 4 pendentes
        foreach ($amostras->splice(0, 4) as $amostra) {
            Solicitacao_Prazo::create([
                'amostra_id' => $amostra->id,
                'solicitante_id' => $amostra->pedido?->user_id ?? $representantes->random(),
                'justificativa' => $justificativas[array_rand($justificativas)],
                'prazo_atual' => $amostra->prazo_avaliacao,
                'novo_prazo' => $amostra->prazo_avaliacao->copy()->addDays(random_int(7, 20))->max(today()->addDays(5)),
                'situacao' => 'pendente',
                'data_solicitacao' => now()->subDays(random_int(0, 3)),
            ]);
        }

        // 3 já respondidas (2 aprovadas, 1 reprovada)
        foreach ($amostras->splice(0, 3)->values() as $i => $amostra) {
            $aprovada = $i < 2;
            $prazoAnterior = $amostra->prazo_avaliacao->copy()->subDays(random_int(5, 10));

            Solicitacao_Prazo::create([
                'amostra_id' => $amostra->id,
                'solicitante_id' => $amostra->pedido?->user_id ?? $representantes->random(),
                'aprovador_id' => $gestores->random(),
                'justificativa' => $justificativas[array_rand($justificativas)],
                // na aprovada, o prazo atual da amostra já é o novo prazo
                'prazo_atual' => $aprovada ? $prazoAnterior : $amostra->prazo_avaliacao,
                'novo_prazo' => $aprovada ? $amostra->prazo_avaliacao : $amostra->prazo_avaliacao->copy()->addDays(30),
                'situacao' => $aprovada ? 'aprovada' : 'reprovada',
                'motivo_aprovacao_reprovacao' => $aprovada ? 'Ok, prazo liberado.' : 'Prazo já foi estendido antes; precisamos da resposta do cliente.',
                'data_solicitacao' => now()->subDays(random_int(8, 15)),
                'data_aprovacao' => now()->subDays(random_int(1, 7)),
            ]);
        }
    }
}
