<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Permissão = módulo + ação (ex.: amostras / criar).
 * A lista é fixa (config amostraweb.permissoes_padrao); não é cadastrada pela tela.
 */
class Permissao extends Model
{
    protected $table = 'permissoes';

    protected $fillable = [
        'modulo',
        'acao',
    ];

    // Ícones dos módulos (mesmos da sidebar)
    public const ICONES_MODULOS = [
        'amostras' => 'fa-flask',
        'clientes' => 'fa-people-group',
        'dashboard' => 'fa-chart-line',
        'estabelecimentos' => 'fa-shop',
        'pedidos' => 'fa-cart-shopping',
        'permissoes' => 'fa-unlock',
        'produtos' => 'fa-tags',
        'usuarios' => 'fa-user',
        'solicitacoes_prazo' => 'fa-calendar-plus',
        'logs' => 'fa-clock-rotate-left',
    ];

    /**
     * Cria as permissões da lista padrão que ainda não existem e apaga as que
     * não estão mais na lista (retirando-as de usuários e grupos antes).
     */
    public static function sincronizarPadrao(): void
    {
        DB::transaction(function () {
            $validas = [];

            foreach (config('amostraweb.permissoes_padrao') as $modulo => $acoes) {
                foreach ($acoes as $acao) {
                    $validas[] = self::firstOrCreate(['modulo' => $modulo, 'acao' => $acao])->id;
                }
            }

            $antigas = self::whereNotIn('id', $validas)->pluck('id');
            if ($antigas->isNotEmpty()) {
                DB::table('usuario_permissoes')->whereIn('permissao_id', $antigas)->delete();
                DB::table('grupo_permissoes')->whereIn('permissao_id', $antigas)->delete();
                self::whereIn('id', $antigas)->delete();
            }
        });
    }

    /**
     * Permissões agrupadas por módulo, na ordem da config (módulos e ações).
     *
     * @return Collection<string, Collection<int, Permissao>>
     */
    public static function agrupadasPorModulo(): Collection
    {
        $ordemModulos = array_keys(config('amostraweb.modulos'));
        $ordemAcoes = array_keys(config('amostraweb.acoes'));

        return self::all()
            ->sortBy([
                fn (Permissao $a, Permissao $b) => array_search($a->modulo, $ordemModulos) <=> array_search($b->modulo, $ordemModulos),
                fn (Permissao $a, Permissao $b) => array_search($a->acao, $ordemAcoes) <=> array_search($b->acao, $ordemAcoes),
            ])
            ->groupBy('modulo');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'usuario_permissoes', 'permissao_id', 'user_id')
            ->withTimestamps();
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_permissoes', 'permissao_id', 'grupo_id')
            ->withTimestamps();
    }

    public function getModuloNomeAttribute(): string
    {
        return config('amostraweb.modulos')[$this->modulo] ?? $this->modulo;
    }

    public function getAcaoNomeAttribute(): string
    {
        return config('amostraweb.acoes')[$this->acao] ?? $this->acao;
    }
}
