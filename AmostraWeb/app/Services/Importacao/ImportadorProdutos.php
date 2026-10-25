<?php

namespace App\Services\Importacao;

use App\Models\Produto;
use Illuminate\Support\Str;

/**
 * Importa produtos a partir do CSV exportado pelo ERP (RF036).
 * As colunas seguem a planilha Docs/Planilhas/Produto.xlsx.
 */
class ImportadorProdutos extends Importador
{
    public function modulo(): string
    {
        return 'produtos';
    }

    protected function colunasObrigatorias(): array
    {
        return ['codigo do produto', 'descricao do produto'];
    }

    public function modelo(): array
    {
        return [
            [
                'Código do produto', 'Descrição do produto', 'Status', 'Tipo', 'Descrição do tipo',
                'Família', 'Descrição da família', 'Cód. Sub-Família', 'Sub-Família', 'Cód. do Grupo', 'Descrição do grupo',
                'Categoria', 'Desc. Categoria', 'Classe', 'Desc. Classe', 'NCM', 'NBS',
                'Aplicação Entrada', 'Descrição aplicação entrada', 'Aplicação Saida', 'Descrição aplicação saida',
                'Código da origem da mercadoria', 'Descrição da origem da mercadoria',
                'Controle de verba', 'Descrição do controle de verba',
                'Código do serviço', 'Descrição do serviço', 'Código CEST', 'Descrição do CEST',
                'Código ANP', 'Descrição da ANP', 'Código marca', 'Marca', 'Data revisão',
                'Código C.A', 'Id. Categoria', 'Id. Preço', 'Código da Tributação do Município',
            ],
            [
                '110.30949', 'OCULOS PARA COLHEITA AGRICOLA - CANE VISION', 'ATIVO', '110', 'MATERIA PRIMA',
                '080', 'MATERIA PRIMA', '', '', '005', 'ITENS PARA REPOSIÇÃO /ACESSÓRIOS DE SINALIZAÇÃO',
                '', '', '', '', '90049020', '',
                '1', 'INDUSTRIALIZACAO', '23', 'REVENDA MATERIA PRIMA',
                '0', 'NACIONAL, EXCETO AS INDICADAS NOS CÓDIGOS 3 A 5',
                '101', 'CONSUMO DE MATERIA PRIMA',
                '', '', '', '', '', '', '3', '360', '01/08/2026',
                '', '', '', '',
            ],
        ];
    }

    protected function importarLinha(array $linha): string
    {
        $codigo = $this->valor($linha, 'codigo do produto');
        $descricao = $this->valor($linha, 'descricao do produto');

        if ($codigo === null) {
            throw new ErroDeLinha('Código do produto em branco.');
        }
        if (! preg_match('/^[A-Za-z0-9.\-\/]{1,30}$/', $codigo)) {
            throw new ErroDeLinha('Código do produto inválido: '.$codigo);
        }
        if ($descricao === null) {
            throw new ErroDeLinha('Produto '.$codigo.': descrição em branco.');
        }

        $produto = Produto::firstOrNew(['codigo_produto' => $codigo]);
        $produto->fill([
            'descricao' => Str::limit($descricao, 255, ''),
            'status' => $this->status($this->valor($linha, 'status')),
            'tipo' => $this->codigoEDescricao($linha, 'tipo', 'descricao do tipo'),
            'familia' => $this->codigoEDescricao($linha, 'familia', 'descricao da familia'),
            'subfamilia' => $this->codigoEDescricao($linha, 'cod sub familia', 'sub familia'),
            'grupo' => $this->codigoEDescricao($linha, 'cod do grupo', 'descricao do grupo'),
            'categoria' => $this->codigoEDescricao($linha, 'categoria', 'desc categoria'),
            'classe' => $this->codigoEDescricao($linha, 'classe', 'desc classe'),
            'ncm' => $this->valor($linha, 'ncm'),
            'nbs' => $this->valor($linha, 'nbs'),
            'aplicacao_entrada' => $this->codigoEDescricao($linha, 'aplicacao entrada', 'descricao aplicacao entrada'),
            'aplicacao_saida' => $this->codigoEDescricao($linha, 'aplicacao saida', 'descricao aplicacao saida'),
            'origem_mercadoria' => $this->codigoEDescricao($linha, 'codigo da origem da mercadoria', 'descricao da origem da mercadoria'),
            'controle_verba' => $this->codigoEDescricao($linha, 'controle de verba', 'descricao do controle de verba'),
            'servico' => $this->codigoEDescricao($linha, 'codigo do servico', 'descricao do servico'),
            'cest' => $this->codigoEDescricao($linha, 'codigo cest', 'descricao do cest'),
            'anp' => $this->codigoEDescricao($linha, 'codigo anp', 'descricao da anp'),
            'marca' => $this->valor($linha, 'marca', 'codigo marca'),
            'data_revisao' => $this->data($this->valor($linha, 'data revisao')),
            'ca' => $this->valor($linha, 'codigo c a', 'c a'),
            'id_categoria' => $this->valor($linha, 'id categoria'),
            'id_preco' => $this->valor($linha, 'id preco'),
            'tributacao_municipio' => $this->valor($linha, 'codigo da tributacao do municipio', 'tributacao do municipio'),
        ]);

        return $this->salvar($produto);
    }
}
