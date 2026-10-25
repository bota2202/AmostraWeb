<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'codigo_empresa',
        'razao_social',
        'nome_fantasia',
        'status',
        'tipo',
        'grupo_fiscal',
        'tipo_empresa',
        'grupo_economico',
        'cnpj_cpf',
        'endereco_completo',
        'telefone',
        'email',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
