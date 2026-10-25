<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario_Permissao extends Model
{
    protected $table = 'usuario_permissoes';

    protected $fillable = [
        'user_id',
        'permissao_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permissao()
    {
        return $this->belongsTo(Permissao::class);
    }
}
