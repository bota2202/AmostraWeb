<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Permissão = módulo + ação (ex.: amostras / criar).
 * A lista é fixa (config amostraweb.permissoes_padrao); não é cadastrada pela tela.
 */
class Permissao extends Model
{
    protected $table = 'permissoes';

    protected $fillable = [
        'nome',
        'modulo',
        'acao',
    ];

    // Ícones dos módulos (mesmos da sidebar)
    public const ICONES_MODULOS = [
        'pedidos' => 'fa-cart-shopping',
        'amostras' => 'fa-flask',
        'solicitacoes_prazo' => 'fa-calendar-plus',
        'clientes' => 'fa-people-group',
        'produtos' => 'fa-tags',
        'estabelecimentos' => 'fa-shop',
        'usuarios' => 'fa-user',
        'permissoes' => 'fa-unlock',
        'logs' => 'fa-clock-rotate-left',
    ];

    /**
     * Cria as permissões da lista padrão que ainda não existem e apaga as que
     * não estão mais na lista (retirando-as de usuários e grupos antes).
     */
    public static function sincronizarPadrao(): void
    {
        $temNome = Schema::hasColumn('permissoes', 'nome');

        DB::transaction(function () use ($temNome) {
            $validas = [];

            foreach (config('amostraweb.permissoes_padrao') as $modulo => $acoes) {
                foreach ($acoes as $acao) {
                    $permissao = self::firstOrCreate(['modulo' => $modulo, 'acao' => $acao]);

                    if ($temNome) {
                        $permissao->update(['nome' => self::nomePadrao($modulo, $acao)]);
                    }

                    $validas[] = $permissao->id;
                }
            }

            $antigas = self::whereNotIn('id', $validas)->pluck('id');
            if ($antigas->isNotEmpty()) {
                if (Schema::hasTable('usuario_permissoes')) {
                    DB::table('usuario_permissoes')->whereIn('permissao_id', $antigas)->delete();
                }
                if (Schema::hasTable('grupo_permissoes')) {
                    DB::table('grupo_permissoes')->whereIn('permissao_id', $antigas)->delete();
                }
                self::whereIn('id', $antigas)->delete();
            }
        });
    }

    /**
     * Ex.: "Cancelar amostras", "Aprovar solicitações de prazo".
     */
    public static function nomePadrao(string $modulo, string $acao): string
    {
        $acaoNome = config('amostraweb.acoes')[$acao] ?? $acao;
        $moduloNome = config('amostraweb.modulos')[$modulo] ?? $modulo;

        return $acaoNome.' '.Str::lower($moduloNome);
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
