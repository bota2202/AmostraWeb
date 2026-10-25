<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Grupo de permissões (ex.: "Vendedores").
 * Todos os membros recebem as permissões do grupo, somadas às próprias.
 */
class Grupo extends Model
{
    protected $table = 'grupos';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'grupo_usuarios', 'grupo_id', 'user_id')
            ->withTimestamps();
    }

    public function permissoes()
    {
        return $this->belongsToMany(Permissao::class, 'grupo_permissoes', 'grupo_id', 'permissao_id')
            ->withTimestamps();
    }
}
