<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'modulo',
        'tipo_registro',
        'registro_id',
        'acao',
        'campo_alterado',
        'valor_anterior',
        'valor_novo',
        'observacao',
        'data_hora',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
