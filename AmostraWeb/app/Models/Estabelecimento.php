<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estabelecimento extends Model
{
    protected $fillable = [
        'razao_social',
        'cnpj',
        'uf',
        'cidade',
        'bairro',
        'rua',
        'numero',
        'cep',
        'status',
        'gestor_id'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }
}
