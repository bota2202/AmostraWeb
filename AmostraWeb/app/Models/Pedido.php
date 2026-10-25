<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use App\Models\Concerns\GeraCodigoSequencial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Pedido: envio de um ou mais produtos (amostras) para um cliente.
 * O status é calculado pelas amostras: "Em aberto" enquanto houver amostra
 * em avaliação e "Concluído" quando todas estiverem finalizadas.
 */
class Pedido extends Model
{
    use GeraCodigoSequencial; // código automático (RN001)
    use Auditavel;            // auditoria (RN020)

    public const STATUS = [
        'aberto' => 'Em aberto',
        'fechado' => 'Concluído',
    ];

    protected string $moduloAuditoria = 'pedidos';

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

    // Vendedor responsável pelo pedido (RN004)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function amostras()
    {
        return $this->hasMany(Amostra::class);
    }

    public function getStatusNomeAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    /**
     * Recalcula o status a partir das amostras.
     */
    public function sincronizarStatus(): void
    {
        $novo = $this->amostras()->emAberto()->exists() ? 'aberto' : 'fechado';

        if ($this->status !== $novo) {
            $this->observacaoAuditoria = $novo === 'fechado'
                ? 'Todas as amostras foram finalizadas.'
                : 'Pedido voltou a ter amostra em avaliação.';
            $this->update(['status' => $novo]);
        }
    }

    /**
     * Pedidos que o usuário pode ver (RN017):
     * vendedor - os próprios; gestor - os dos seus estabelecimentos; admin - todos.
     */
    public function scopeVisivelPara(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isGestor()) {
            return $query->whereIn('estabelecimento_id', $user->estabelecimentosVisiveis());
        }

        return $query->where('user_id', $user->id);
    }

    public function visivelPara(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGestor()) {
            return in_array((int) $this->estabelecimento_id, $user->estabelecimentosVisiveis(), true);
        }

        return (int) $this->user_id === (int) $user->id;
    }
}
