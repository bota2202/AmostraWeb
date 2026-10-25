<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            // código, descrição, família, subfamília, marca, NCM, ativo
            ['100101', 'Placa Saída de Emergência fotoluminescente 20x30', 'SINALIZACAO', 'EMERGENCIA', '360', '3926.90.90', true],
            ['100102', 'Placa Uso Obrigatório de EPI 30x40', 'SINALIZACAO', 'OBRIGACAO', '360', '3926.90.90', true],
            ['100103', 'Placa Extintor de Incêndio 15x20', 'SINALIZACAO', 'INCENDIO', '360', '3926.90.90', true],
            ['100104', 'Placa Cuidado Piso Molhado 20x30', 'SINALIZACAO', 'ALERTA', '360', '3926.90.90', true],
            ['100105', 'Fita Zebrada amarela e preta 200m', 'SINALIZACAO', 'DEMARCACAO', 'Plastcor', '3919.10.00', true],
            ['100106', 'Fita Antiderrapante preta 50mm x 5m', 'SINALIZACAO', 'DEMARCACAO', '3M', '6805.30.10', true],
            ['100107', 'Cone de Sinalização 75cm laranja', 'SINALIZACAO', 'VIARIA', 'Plastcor', '3926.90.90', true],
            ['100108', 'Corrente Plástica zebrada 6mm 25m', 'SINALIZACAO', 'VIARIA', 'Plastcor', '3926.90.90', true],
            ['200201', 'Capacete de Segurança aba frontal branco', 'EPI', 'CABECA', 'MSA', '6506.10.00', true],
            ['200202', 'Óculos de Proteção incolor antiembaçante', 'EPI', 'OLHOS', 'Kalipso', '9004.90.20', true],
            ['200203', 'Protetor Auricular tipo plug silicone', 'EPI', 'AUDICAO', '3M', '3926.90.90', true],
            ['200204', 'Luva de Vaqueta cano curto', 'EPI', 'MAOS', 'Volk', '4203.29.00', true],
            ['200205', 'Respirador PFF2 sem válvula', 'EPI', 'RESPIRATORIO', '3M', '6307.90.10', true],
            ['200206', 'Colete Refletivo laranja tipo X', 'EPI', 'VISIBILIDADE', '360', '6211.43.00', true],
            ['200207', 'Botina de Segurança bico de PVC nº 42', 'EPI', 'PES', 'Marluvas', '6403.40.00', true],
            ['200208', 'Cinto Paraquedista 3 pontos', 'EPI', 'ALTURA', 'Carbografite', '4205.00.00', false],
        ];

        foreach ($produtos as [$codigo, $descricao, $familia, $subfamilia, $marca, $ncm, $ativo]) {
            Produto::create([
                'codigo_produto' => $codigo,
                'descricao' => $descricao,
                'familia' => $familia,
                'subfamilia' => $subfamilia,
                'grupo' => $familia === 'EPI' ? 'PROTECAO' : 'COMUNICACAO',
                'tipo' => 'REVENDA',
                'ncm' => $ncm,
                'origem_mercadoria' => '0 - NACIONAL',
                'marca' => $marca,
                'status' => $ativo,
                'data_revisao' => today()->subMonths(random_int(1, 12)),
            ]);
        }
    }
}
