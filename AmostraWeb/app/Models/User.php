<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password','estabelecimento_id','codigo_usuario','cargo','status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const CARGOS = [
        'admin' => 'Administrador',
        'gestor' => 'Gestor',
        'representante' => 'Representante',
    ];

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

    public function permissoes()
    {
        return $this->belongsToMany(Permissao::class, 'usuario_permissoes', 'user_id', 'permissao_id')
            ->withTimestamps();
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

    /**
     * Permissões que o usuário realmente tem: as dos seus grupos + as marcadas nele.
     * Administradores têm sempre todas.
     *
     * @return \Illuminate\Support\Collection<int, Permissao>
     */
    public function permissoesEfetivas()
    {
        if ($this->isAdmin()) {
            return Permissao::all();
        }

        $this->loadMissing(['permissoes', 'grupos.permissoes']);

        return $this->permissoes
            ->concat($this->grupos->flatMap->permissoes)
            ->unique('id')
            ->values();
    }

    /**
     * Ex.: $user->temPermissao('amostras', 'criar')
     */
    public function temPermissao(string $modulo, string $acao): bool
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
