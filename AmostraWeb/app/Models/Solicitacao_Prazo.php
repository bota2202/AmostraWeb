<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Solicitacao_Prazo extends Model
{
    use Auditavel;

    protected string $moduloAuditoria = 'solicitacoes_prazo';

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

    public function getSituacaoNomeAttribute(): string
    {
        return self::SITUACOES[$this->situacao] ?? $this->situacao;
    }

    public function pendente(): bool
    {
        return $this->situacao === 'pendente';
    }

    /**
     * Solicitações das amostras que o usuário pode ver (RN017).
     */
    public function scopeVisivelPara(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->whereIn('amostra_id', Amostra::query()->visivelPara($user)->select('id'));
    }

    /**
     * Pendentes que o usuário pode aprovar/reprovar (gestor do estabelecimento ou admin).
     */
    public function scopeAguardandoDecisaoDe(Builder $query, User $user): Builder
    {
        $query->where('situacao', 'pendente');

        if ($user->isAdmin()) {
            return $query;
        }

        if (! $user->isGestor() || ! $user->temPermissao('solicitacoes_prazo', 'aprovar')) {
            return $query->whereRaw('1 = 0');
        }

        return $query->visivelPara($user)->where('solicitante_id', '!=', $user->id);
    }

    /**
     * Por que o usuário não pode aprovar/reprovar esta solicitação (null = pode).
     * Regra RN008: somente o gestor responsável pelo estabelecimento ou um administrador.
     */
    public function motivoParaNaoDecidir(User $user): ?string
    {
        if (! $this->pendente()) {
            return 'Esta solicitação já foi respondida.';
        }

        if (! $user->temPermissao('solicitacoes_prazo', 'aprovar')) {
            return 'Você não tem permissão para aprovar solicitações de prazo.';
        }

        if ($user->isAdmin()) {
            return null;
        }

        if (! $user->isGestor()) {
            return 'Somente o gestor do estabelecimento ou um administrador pode decidir solicitações de prazo.';
        }

        if (! $this->amostra->visivelPara($user)) {
            return 'Esta amostra não pertence a um estabelecimento sob sua gestão.';
        }

        if ((int) $this->solicitante_id === (int) $user->id) {
            return 'Você não pode decidir a sua própria solicitação.';
        }

        return null;
    }

    /**
     * Quem pediu pode desistir enquanto estiver pendente; admin também.
     */
    public function podeSerCanceladaPor(User $user): bool
    {
        return $this->pendente()
            && $user->temPermissao('solicitacoes_prazo', 'excluir')
            && ($user->isAdmin() || (int) $this->solicitante_id === (int) $user->id);
    }
}
