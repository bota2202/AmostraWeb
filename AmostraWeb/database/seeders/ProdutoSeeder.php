<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

/**
 * Produtos de exemplo no formato do cadastro do ERP
 * (código "110.30949", campos como "080 - MATERIA PRIMA").
 */
class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $sinalizacao = ['familia' => '010 - SINALIZACAO', 'grupo' => '005 - ITENS PARA REPOSIÇÃO /ACESSÓRIOS DE SINALIZAÇÃO'];
        $epi = ['familia' => '020 - EPI', 'grupo' => '007 - EQUIPAMENTOS DE PROTEÇÃO INDIVIDUAL'];

        $produtos = [
            // código, descrição, classificação, subfamília, marca, NCM, C.A., ativo
            ['110.30101', 'PLACA SAÍDA DE EMERGÊNCIA FOTOLUMINESCENTE 20X30', $sinalizacao, '011 - EMERGENCIA', '360', '39269090', null, true],
            ['110.30102', 'PLACA USO OBRIGATÓRIO DE EPI 30X40', $sinalizacao, '012 - OBRIGACAO', '360', '39269090', null, true],
            ['110.30103', 'PLACA EXTINTOR DE INCÊNDIO 15X20', $sinalizacao, '013 - INCENDIO', '360', '39269090', null, true],
            ['110.30104', 'PLACA CUIDADO PISO MOLHADO 20X30', $sinalizacao, '014 - ALERTA', '360', '39269090', null, true],
            ['110.30105', 'FITA ZEBRADA AMARELA E PRETA 200M', $sinalizacao, '015 - DEMARCACAO', 'PLASTCOR', '39191000', null, true],
            ['110.30106', 'FITA ANTIDERRAPANTE PRETA 50MM X 5M', $sinalizacao, '015 - DEMARCACAO', '3M', '68053010', null, true],
            ['110.31141', '360-CORPO DO CONE PE DE 75CM', $sinalizacao, '016 - VIARIA', '360', '39269090', null, true],
            ['110.31142', 'CORRENTE PLÁSTICA ZEBRADA 6MM 25M', $sinalizacao, '016 - VIARIA', 'PLASTCOR', '39269090', null, true],
            ['200.00011', 'CAPACETE DE SEGURANÇA ABA FRONTAL BRANCO', $epi, '021 - CABECA', 'MSA', '65061000', '498', true],
            ['110.30949', 'OCULOS PARA COLHEITA AGRICOLA - CANE VISION', $epi, '022 - OLHOS', 'KALIPSO', '90049020', '25715', true],
            ['200.00012', 'PROTETOR AURICULAR TIPO PLUG SILICONE', $epi, '023 - AUDICAO', '3M', '39269090', '5745', true],
            ['200.00014', 'LUVA DE VAQUETA CANO CURTO', $epi, '024 - MAOS', 'VOLK', '42032900', '7456', true],
            ['200.00013', '360 - FILTRO QUIMICO VO+GA - MONTADO', $epi, '025 - RESPIRATORIO', '360', '63079010', '19634', true],
            ['200.00015', 'COLETE REFLETIVO LARANJA TIPO X', $epi, '026 - VISIBILIDADE', '360', '62114300', null, true],
            ['200.00016', 'BOTINA DE SEGURANÇA BICO DE PVC Nº 42', $epi, '027 - PES', 'MARLUVAS', '64034000', '42319', true],
            ['200.00017', 'CINTO PARAQUEDISTA 3 PONTOS', $epi, '028 - ALTURA', 'CARBOGRAFITE', '42050000', '35523', false],
        ];

        foreach ($produtos as [$codigo, $descricao, $classificacao, $subfamilia, $marca, $ncm, $ca, $ativo]) {
            Produto::create([
                'codigo_produto' => $codigo,
                'descricao' => $descricao,
                'familia' => $classificacao['familia'],
                'subfamilia' => $subfamilia,
                'grupo' => $classificacao['grupo'],
                'tipo' => '600 - REVENDA',
                'ncm' => $ncm,
                'origem_mercadoria' => '0 - NACIONAL, EXCETO AS INDICADAS NOS CÓDIGOS 3 A 5',
                'aplicacao_entrada' => '2 - COMERCIALIZACAO',
                'aplicacao_saida' => '2 - COMERCIALIZACAO',
                'controle_verba' => '102 - CONSUMO DE MERCADORIA',
                'marca' => $marca,
                'ca' => $ca,
                'status' => $ativo,
                'data_revisao' => today()->subMonths(random_int(1, 12)),
            ]);
        }
    }
}
