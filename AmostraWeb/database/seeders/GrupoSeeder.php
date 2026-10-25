<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Permissao;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Grupos de permissões de exemplo, com membros conforme o cargo.
 * As permissões padrão são criadas pelas migrations (precisam rodar antes).
 * Administradores não precisam de grupo: têm todas as permissões.
 */
class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $todas = Permissao::all();

        // módulo => ações ('*' = todas as ações do módulo)
        $grupos = [
            'Gestores' => [
                'descricao' => 'Acompanham o estabelecimento, decidem solicitações de prazo e mantêm os cadastros.',
                'cargo' => 'gestor',
                'permissoes' => [
                    'pedidos' => '*',
                    'amostras' => '*',
                    'solicitacoes_prazo' => '*',
                    'clientes' => ['visualizar', 'criar', 'editar', 'importar'],
                    'produtos' => ['visualizar', 'criar', 'editar', 'importar'],
                    'estabelecimentos' => ['visualizar'],
                    'usuarios' => ['visualizar'],
                    'logs' => '*',
                ],
            ],
            'Vendedores' => [
                'descricao' => 'Cadastram pedidos e amostras, registram o retorno do cliente e pedem prazo.',
                'cargo' => 'representante',
                'permissoes' => [
                    'pedidos' => '*',
                    'amostras' => ['visualizar', 'criar', 'editar', 'cancelar'],
                    'solicitacoes_prazo' => ['visualizar', 'criar', 'excluir'],
                    'clientes' => ['visualizar', 'criar', 'editar'],
                    'produtos' => ['visualizar'],
                ],
            ],
        ];

        foreach ($grupos as $nome => $dados) {
            $grupo = Grupo::create(['nome' => $nome, 'descricao' => $dados['descricao']]);

            $ids = $todas->filter(function (Permissao $p) use ($dados) {
                $acoes = $dados['permissoes'][$p->modulo] ?? [];
                return $acoes === '*' || in_array($p->acao, $acoes, true);
            })->pluck('id');

            $grupo->permissoes()->sync($ids);
            $grupo->users()->sync(User::where('cargo', $dados['cargo'])->pluck('id'));
        }
    }
}
