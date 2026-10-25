<?php

namespace Database\Seeders;

use App\Models\Amostra;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Database\Seeder;

/**
 * Amostras em todos os status, com prazos vencidos, próximos e futuros.
 */
class AmostraSeeder extends Seeder
{
    private int $sequencia = 0;

    public function run(): void
    {
        $produtos = Produto::where('status', true)->pluck('id');

        // 1 a 3 amostras por pedido
        foreach (Pedido::all() as $pedido) {
            for ($i = 0, $total = random_int(1, 3); $i < $total; $i++) {
                $this->criar($produtos->random(), $pedido);
            }
        }

        // algumas amostras sem pedido
        for ($i = 0; $i < 5; $i++) {
            $this->criar($produtos->random());
        }
    }

    private function criar(int $produtoId, ?Pedido $pedido = null): void
    {
        // [status, prazo em dias a partir de hoje]
        $cenario = collect([
            ['em_analise', random_int(8, 30)],   // prazo tranquilo
            ['em_analise', random_int(8, 30)],
            ['em_analise', random_int(1, 7)],    // vence esta semana
            ['em_analise', -random_int(1, 5)],   // vencida, ainda em análise
            ['atrasada', -random_int(3, 15)],
            ['aprovada', -random_int(5, 40)],
            ['aprovada', -random_int(5, 40)],
            ['reprovada', -random_int(5, 40)],
            ['cancelada', -random_int(5, 40)],
        ])->random();

        // pedido fechado só tem amostras já finalizadas
        if ($pedido?->status === 'fechado' && in_array($cenario[0], ['em_analise', 'atrasada'])) {
            $cenario = ['aprovada', -random_int(20, 40)];
        }

        // O seeder roda sem eventos de model, então o código é gerado aqui
        $this->sequencia++;

        Amostra::create([
            'codigo' => str_pad((string) $this->sequencia, 6, '0', STR_PAD_LEFT),
            'pedido_id' => $pedido?->id,
            'produto_id' => $produtoId,
            'quantidade' => random_int(1, 10),
            'status' => $cenario[0],
            'prazo_avaliacao' => today()->addDays($cenario[1]),
        ]);
    }
}
