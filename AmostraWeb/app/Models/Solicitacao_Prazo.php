<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitacao_Prazo extends Model
{
    protected $table = 'solicitacoes_prazo';

    public const SITUACOES = [
        'pendente' => 'Pendente',
        'aprovada' => 'Aprovada',
        'reprovada' => 'Reprovada',
    ];

    protected $fillable = [
        'amostra_id',
        'solicitante_id',
        'aprovador_id',
        'justificativa',
        'prazo_atual',
        'novo_prazo',
        'situacao',
        'motivo_aprovacao_reprovacao',
        'data_solicitacao',
        'data_aprovacao',
    ];

    protected $casts = [
        'prazo_atual' => 'date',
        'novo_prazo' => 'date',
        'data_solicitacao' => 'datetime',
        'data_aprovacao' => 'datetime',
    ];

    public function amostra()
    {
        return $this->belongsTo(Amostra::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function aprovador()
    {
        return $this->belongsTo(User::class, 'aprovador_id');
    }
}
