<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\Auditavel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['name', 'email', 'password','estabelecimento_id','codigo_usuario','cargo','status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Auditavel;

    // valor salvo no banco => nome exibido (o perfil "Vendedor" da documentação)
    public const CARGOS = [
        'admin' => 'Administrador',
        'gestor' => 'Gestor',
        'representante' => 'Vendedor',
    ];

    protected string $moduloAuditoria = 'usuarios';

    // permissões efetivas calculadas uma vez por requisição
    private ?Collection $permissoesEmCache = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status'=>'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Iniciais do nome para o avatar: primeiro e último nome ("Otávio Silva" => "OS").
     */
    public function getIniciaisAttribute(): string
    {
        $partes = preg_split('/\s+/', trim($this->name));
        $iniciais = mb_substr($partes[0], 0, 1);
        if (count($partes) > 1) {
            $iniciais .= mb_substr(end($partes), 0, 1);
        }
        return mb_strtoupper($iniciais);
    }

    public function getCargoNomeAttribute(): string
    {
        return self::CARGOS[$this->cargo] ?? $this->cargo;
    }

    public function estabelecimento(){
        return $this->belongsTo(Estabelecimento::class);
    }

    public function estabelecimentosGeridos()
    {
        return $this->hasMany(Estabelecimento::class, 'gestor_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_usuarios', 'user_id', 'grupo_id')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->cargo === 'admin';
    }

    public function isGestor(): bool
    {
        return $this->cargo === 'gestor';
    }

    public function isVendedor(): bool
    {
        return $this->cargo === 'representante';
    }

    /**
     * Estabelecimentos cujos pedidos e amostras o usuário pode ver (RN017).
     * null = todos (administrador).
     *
     * @return array<int>|null
     */
    public function estabelecimentosVisiveis(): ?array
    {
        if ($this->isAdmin()) {
            return null;
        }

        if ($this->isGestor()) {
            return $this->estabelecimentosGeridos()->pluck('id')
                ->push($this->estabelecimento_id)
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        // vendedor: o escopo é por responsável, não por estabelecimento
        return [];
    }

    /**
     * Permissões que o usuário realmente tem: a soma das permissões dos seus grupos
     * (não existe permissão dada direto ao usuário). Administradores têm sempre todas.
     *
     * @return Collection<int, Permissao>
     */
    public function permissoesEfetivas(): Collection
    {
        if ($this->permissoesEmCache !== null) {
            return $this->permissoesEmCache;
        }

        if ($this->isAdmin()) {
            return $this->permissoesEmCache = Permissao::all();
        }

        $this->loadMissing('grupos.permissoes');

        return $this->permissoesEmCache = $this->grupos->flatMap->permissoes
            ->unique('id')
            ->values();
    }

    /**
     * Limpa o cache depois de alterar grupos ou permissões do usuário.
     */
    public function esquecerPermissoes(): void
    {
        $this->permissoesEmCache = null;
        $this->unsetRelation('grupos');
    }

    /**
     * Ex.: $user->temPermissao('amostras', 'criar')
     */
    public function temPermissao(string $modulo, string $acao = 'visualizar'): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->permissoesEfetivas()->contains(
            fn (Permissao $p) => $p->modulo === $modulo && $p->acao === $acao
        );
    }

    public function solicitacoesPrazo()
    {
        return $this->hasMany(Solicitacao_Prazo::class, 'solicitante_id');
    }

    public function aprovacoesPrazo()
    {
        return $this->hasMany(Solicitacao_Prazo::class, 'aprovador_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
