<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Permissao;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Grupos de permissões de exemplo, com membros conforme o cargo.
 * As permissões padrão são criadas pela migration (precisa rodar antes).
 */
class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $todas = Permissao::all();

        // módulo => ações ('*' = todas as ações do módulo)
        $grupos = [
            'Administradores' => [
                'descricao' => 'Acesso total ao sistema.',
                'cargo' => 'admin',
                'permissoes' => array_fill_keys(array_keys(config('amostraweb.permissoes_padrao')), '*'),
            ],
            'Gestores' => [
                'descricao' => 'Gerenciam os estabelecimentos, aprovam prazos e acompanham a equipe.',
                'cargo' => 'gestor',
                'permissoes' => [
                    'dashboard' => '*',
                    'amostras' => '*',
                    'solicitacoes_prazo' => '*',
                    'clientes' => '*',
                    'pedidos' => '*',
                    'produtos' => ['visualizar', 'criar', 'editar'],
                    'estabelecimentos' => ['visualizar', 'editar'],
                    'usuarios' => ['visualizar', 'criar', 'editar'],
                    'logs' => '*',
                ],
            ],
            'Vendedores' => [
                'descricao' => 'Representantes: cadastram pedidos e amostras e pedem prazo.',
                'cargo' => 'representante',
                'permissoes' => [
                    'dashboard' => '*',
                    'amostras' => ['visualizar', 'criar', 'editar'],
                    'solicitacoes_prazo' => ['visualizar', 'criar'],
                    'clientes' => ['visualizar', 'criar', 'editar'],
                    'pedidos' => ['visualizar', 'criar', 'editar'],
                    'produtos' => ['visualizar'],
                ],
            ],
        ];

        foreach ($grupos as $nome => $dados) {
            $grupo = Grupo::create(['nome' => $nome, 'descricao' => $dados['descricao']]);

            $ids = $todas->filter(function (Permissao $p) use ($dados) {
                $acoes = $dados['permissoes'][$p->modulo] ?? [];
                return $acoes === '*' || in_array($p->acao, $acoes);
            })->pluck('id');

            $grupo->permissoes()->sync($ids);
            $grupo->users()->sync(User::where('cargo', $dados['cargo'])->pluck('id'));
        }
    }
}
