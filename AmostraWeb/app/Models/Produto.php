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
        'tipo',
        'ncm',
        'aplicacao_entrada',
        'aplicacao_saida',
        'origem_mercadoria',
        'controle_verba',
        'status',
        'marca',
        'data_revisao',
    ];

    protected $casts = [
        'status' => 'boolean',
        'data_revisao' => 'date',
    ];

    public function amostras()
    {
        return $this->hasMany(Amostra::class);
    }
}
