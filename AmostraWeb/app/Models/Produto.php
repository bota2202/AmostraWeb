<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $fillable = [
        'codigo_produto',
        'descricao',
        'familia',
        'subfamilia',
        'grupo',
        'categoria_produto',
        'nbs',
        'tipo',
        'ncm',
        'servico',
        'cest',
        'anp',
        'aplicacao_entrada',
        'aplicacao_saida',
        'origem_mercadoria',
        'controle_verba',
        'status',
        'marca',
        'data_revisao',
        'classe',
        'ca',
        'id_categoria',
        'id_preco',
        'tributacao_municipio'
    ];

    protected $casts = [
        'controle_verba'=>'boolean',
        'status' => 'boolean',
        'data_revisao' => 'date',
    ];
}
