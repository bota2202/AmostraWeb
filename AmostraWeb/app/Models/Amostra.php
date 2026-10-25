<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Amostra extends Model
{
    public const STATUS = [
        'em_analise' => 'Em análise',
        'atrasada' => 'Atrasada',
        'aprovada' => 'Aprovada',
        'reprovada' => 'Reprovada',
        'cancelada' => 'Cancelada',
    ];

    protected $fillable = [
        'codigo',
        'pedido_id',
        'produto_id',
        'quantidade',
        'status',
        'prazo_avaliacao',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'prazo_avaliacao' => 'date',
    ];

    /**
     * O código é gerado automaticamente na criação: 000001, 000002, ...
     */
    protected static function booted(): void
    {
        static::creating(function (Amostra $amostra) {
            $amostra->codigo ??= self::proximoCodigo();
        });
    }

    public static function proximoCodigo(): string
    {
        $ultimo = (int) self::lockForUpdate()->max('codigo');
        return str_pad((string) ($ultimo + 1), 6, '0', STR_PAD_LEFT);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function solicitacoesPrazo()
    {
        return $this->hasMany(Solicitacao_Prazo::class);
    }

    /**
     * Amostras marcadas como atrasadas ou ainda em análise com o prazo vencido.
     */
    public function scopeAtrasadas(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', 'atrasada')
                ->orWhere(fn (Builder $q) => $q->where('status', 'em_analise')
                    ->whereDate('prazo_avaliacao', '<', today()));
        });
    }

    /**
     * Ainda aguardando avaliação (permite pedir mais prazo).
     */
    public function emAberto(): bool
    {
        return in_array($this->status, ['em_analise', 'atrasada']);
    }

    public function prazoVencido(): bool
    {
        return $this->emAberto() && $this->prazo_avaliacao->lt(today());
    }
}
