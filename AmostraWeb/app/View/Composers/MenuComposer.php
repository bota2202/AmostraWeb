<?php

namespace App\View\Composers;

use App\Models\Amostra;
use App\Models\Solicitacao_Prazo;
use Illuminate\View\View;

/**
 * Monta o menu lateral conforme as permissões do usuário logado.
 * Itens sem permissão de visualizar não aparecem; seções vazias somem.
 */
class MenuComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user) {
            $view->with('menu', []);
            return;
        }

        $secoes = [
            null => [
                ['rota' => 'dashboard', 'ativo' => 'dashboard', 'icone' => 'fa-chart-line', 'texto' => 'Dashboard', 'modulo' => null],
            ],
            'Operação' => [
                ['rota' => 'pedidos.index', 'ativo' => 'pedidos.*', 'icone' => 'fa-cart-shopping', 'texto' => 'Pedidos', 'modulo' => 'pedidos'],
                ['rota' => 'amostras.index', 'ativo' => 'amostras.*', 'icone' => 'fa-flask', 'texto' => 'Amostras', 'modulo' => 'amostras',
                    'contador' => fn () => Amostra::query()->visivelPara($user)->atrasadas()->count(),
                    'contador_texto' => 'atrasadas'],
                ['rota' => 'solicitacoes-prazo.index', 'ativo' => 'solicitacoes-prazo.*', 'icone' => 'fa-calendar-plus', 'texto' => 'Solicitações de prazo', 'modulo' => 'solicitacoes_prazo',
                    'contador' => fn () => Solicitacao_Prazo::query()->aguardandoDecisaoDe($user)->count(),
                    'contador_texto' => 'aguardando sua decisão'],
            ],
            'Cadastros' => [
                ['rota' => 'clientes.index', 'ativo' => 'clientes.*', 'icone' => 'fa-people-group', 'texto' => 'Clientes', 'modulo' => 'clientes'],
                ['rota' => 'produtos.index', 'ativo' => 'produtos.*', 'icone' => 'fa-tags', 'texto' => 'Produtos', 'modulo' => 'produtos'],
                ['rota' => 'estabelecimentos.index', 'ativo' => 'estabelecimentos.*', 'icone' => 'fa-shop', 'texto' => 'Estabelecimentos', 'modulo' => 'estabelecimentos'],
            ],
            'Administração' => [
                ['rota' => 'usuarios.index', 'ativo' => 'usuarios.*', 'icone' => 'fa-user', 'texto' => 'Usuários', 'modulo' => 'usuarios'],
                ['rota' => 'grupos.index', 'ativo' => 'grupos.*', 'icone' => 'fa-unlock', 'texto' => 'Permissões', 'modulo' => 'permissoes'],
                ['rota' => 'logs.index', 'ativo' => 'logs.*', 'icone' => 'fa-clock-rotate-left', 'texto' => 'Logs', 'modulo' => 'logs'],
            ],
        ];

        $menu = [];
        foreach ($secoes as $titulo => $itens) {
            $visiveis = [];
            foreach ($itens as $item) {
                if ($item['modulo'] !== null && ! $user->temPermissao($item['modulo'])) {
                    continue;
                }

                $item['ativo'] = request()->routeIs($item['ativo']);
                $item['contador'] = isset($item['contador']) ? ($item['contador'])() : 0;
                $visiveis[] = $item;
            }

            if ($visiveis) {
                $menu[] = ['titulo' => $titulo ?: null, 'itens' => $visiveis];
            }
        }

        $view->with('menu', $menu);
    }
}
