<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use App\Models\Concerns\GeraCodigoSequencial;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Amostra: produto enviado ao cliente para avaliação, sempre dentro de um pedido.
 *
 * Ciclo de vida (04 - Fluxo das Amostras):
 *   Em análise -> (prazo vence) Atrasada -> Aprovada | Reprovada
 *   Em análise/Atrasada -> Cancelada
 *   Aprovada, Reprovada e Cancelada são finais: não podem mais ser alteradas.
 */
class Amostra extends Model
{
    use GeraCodigoSequencial; // código automático (RN002)
    use Auditavel;            // auditoria e histórico (RN015 / RN016)

    public const STATUS = [
        'em_analise' => 'Em análise',
        'atrasada' => 'Atrasada',
        'aprovada' => 'Aprovada',
        'reprovada' => 'Reprovada',
        'cancelada' => 'Cancelada',
    ];

    public const STATUS_EM_ABERTO = ['em_analise', 'atrasada'];
    public const STATUS_FINAIS = ['aprovada', 'reprovada', 'cancelada'];

    protected string $moduloAuditoria = 'amostras';

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

    protected static function booted(): void
    {
        // o status do pedido acompanha as amostras
        static::saved(fn (Amostra $amostra) => $amostra->pedido?->sincronizarStatus());
        static::deleted(fn (Amostra $amostra) => $amostra->pedido?->sincronizarStatus());
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

    /* ------------------------------------------------------------------
     | Situação
     * ------------------------------------------------------------------ */

    public function getStatusNomeAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    /**
     * Ainda em avaliação (pode ser aprovada, reprovada, cancelada ou ter o prazo alterado).
     */
    public function emAberto(): bool
    {
        return in_array($this->status, self::STATUS_EM_ABERTO, true);
    }

    public function finalizada(): bool
    {
        return in_array($this->status, self::STATUS_FINAIS, true);
    }

    public function prazoVencido(): bool
    {
        return $this->emAberto() && $this->prazo_avaliacao->lt(today());
    }

    /**
     * Exclusão permanente: só amostras canceladas (RN014), e o pedido não
     * pode ficar sem amostras (RN023).
     */
    public function motivoParaNaoExcluir(): ?string
    {
        if ($this->status !== 'cancelada') {
            return 'Só amostras canceladas podem ser excluídas. Cancele a amostra antes.';
        }

        if ($this->pedido && $this->pedido->amostras()->count() <= 1) {
            return 'É a única amostra do pedido '.$this->pedido->codigo.'. Um pedido precisa ter pelo menos uma amostra.';
        }

        return null;
    }

    /* ------------------------------------------------------------------
     | Mudanças de status
     * ------------------------------------------------------------------ */

    /**
     * Registra o resultado da avaliação ou o cancelamento.
     *
     * @throws DomainException quando a transição não é permitida
     */
    public function alterarStatus(string $novoStatus, ?string $observacao = null): void
    {
        if (! in_array($novoStatus, self::STATUS_FINAIS, true)) {
            throw new DomainException('Status inválido para esta ação.');
        }

        if (! $this->emAberto()) {
            throw new DomainException('A amostra '.$this->codigo.' já está '.mb_strtolower($this->status_nome).' e não pode mais ser alterada.');
        }

        $this->observacaoAuditoria = $observacao;
        $this->status = $novoStatus;
        $this->save();
    }

    /**
     * Marca como "Atrasada" as amostras em análise com o prazo vencido (RN009).
     * Executado pelo sistema: a auditoria registra "Sistema" como responsável.
     *
     * @return int quantidade de amostras atualizadas
     */
    public static function marcarAtrasadas(): int
    {
        $total = 0;

        Log::comoSistema(function () use (&$total) {
            self::where('status', 'em_analise')
                ->whereDate('prazo_avaliacao', '<', today())
                ->with('pedido')
                ->each(function (Amostra $amostra) use (&$total) {
                    $amostra->observacaoAuditoria = 'Prazo de avaliação vencido em '.$amostra->prazo_avaliacao->format('d/m/Y').'. Status alterado automaticamente.';
                    $amostra->status = 'atrasada';
                    $amostra->save();
                    $total++;
                });
        });

        return $total;
    }

    /* ------------------------------------------------------------------
     | Consultas
     * ------------------------------------------------------------------ */

    /**
     * Atrasadas (inclui as em análise que vencem antes da próxima atualização automática).
     */
    public function scopeAtrasadas(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', 'atrasada')
                ->orWhere(fn (Builder $q) => $q->where('status', 'em_analise')
                    ->whereDate('prazo_avaliacao', '<', today()));
        });
    }

    public function scopeEmAberto(Builder $query): Builder
    {
        return $query->whereIn('status', self::STATUS_EM_ABERTO);
    }

    /**
     * Amostras que o usuário pode ver (RN017): as dos pedidos visíveis para ele.
     */
    public function scopeVisivelPara(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->whereIn('pedido_id', Pedido::query()->visivelPara($user)->select('id'));
    }

    public function visivelPara(User $user): bool
    {
        return $this->pedido?->visivelPara($user) ?? $user->isAdmin();
    }
}
