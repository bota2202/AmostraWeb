<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    public const STATUS = [
        'aberto' => 'Aberto',
        'fechado' => 'Fechado',
    ];

    protected $fillable = [
        'codigo',
        'cliente_id',
        'estabelecimento_id',
        'user_id',
        'observacao',
        'status',
        'data_emissao',
    ];

    protected $casts = [
        'data_emissao' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class);
    }

    // Representante que registrou o pedido
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function amostras()
    {
        return $this->hasMany(Amostra::class);
    }
}
