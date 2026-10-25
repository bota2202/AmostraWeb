<?php

namespace Tests;

use App\Models\Amostra;
use App\Models\Cliente;
use App\Models\Estabelecimento;
use App\Models\Grupo;
use App\Models\Pedido;
use App\Models\Permissao;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    /* ------------------------------------------------------------------
     | Ajudas para montar cenários nos testes
     * ------------------------------------------------------------------ */

    protected function criarEstabelecimento(array $dados = []): Estabelecimento
    {
        static $sequencia = 0;
        $sequencia++;

        return Estabelecimento::create([
            'razao_social' => 'Estabelecimento '.$sequencia,
            'cnpj' => sprintf('00.000.000/%04d-00', $sequencia),
            'uf' => 'sp',
            'cidade' => 'limeira',
            'status' => true,
            ...$dados,
        ]);
    }

    /**
     * @param array<string, array<string>|string> $permissoes módulo => ações ('*' = todas)
     */
    protected function criarUsuario(string $cargo = 'representante', ?Estabelecimento $estabelecimento = null, array $permissoes = [], array $dados = []): User
    {
        $estabelecimento ??= Estabelecimento::first() ?? $this->criarEstabelecimento();
        $codigo = Str::lower(Str::random(8));

        $user = User::create([
            'name' => 'Usuário '.$codigo,
            'email' => $codigo.'@teste.local',
            'codigo_usuario' => $codigo,
            'cargo' => $cargo,
            'estabelecimento_id' => $estabelecimento->id,
            'status' => true,
            'password' => 'senha123',
            ...$dados,
        ]);

        // permissões só existem por grupo: cria um grupo só deste usuário
        if ($permissoes) {
            $this->criarGrupo('Grupo de '.$codigo, $permissoes, [$user]);
        }

        return $user;
    }

    protected function criarGrupo(string $nome, array $permissoes, array $membros = []): Grupo
    {
        $grupo = Grupo::create(['nome' => $nome]);
        $grupo->permissoes()->sync($this->idsPermissoes($permissoes));
        $grupo->users()->sync(collect($membros)->pluck('id'));

        return $grupo;
    }

    /**
     * @param array<string, array<string>|string> $permissoes
     * @return array<int>
     */
    protected function idsPermissoes(array $permissoes): array
    {
        return Permissao::all()->filter(function (Permissao $p) use ($permissoes) {
            $acoes = $permissoes[$p->modulo] ?? [];
            return $acoes === '*' || in_array($p->acao, (array) $acoes, true);
        })->pluck('id')->all();
    }

    protected function criarCliente(array $dados = []): Cliente
    {
        static $sequencia = 0;
        $sequencia++;

        return Cliente::create([
            'codigo_empresa' => 'C'.$sequencia,
            'razao_social' => 'Cliente '.$sequencia.' LTDA',
            'tipo' => 'Revenda',
            'cnpj_cpf' => sprintf('11.111.111/%04d-11', $sequencia),
            'status' => true,
            ...$dados,
        ]);
    }

    protected function criarProduto(array $dados = []): Produto
    {
        static $sequencia = 0;
        $sequencia++;

        return Produto::create([
            'codigo_produto' => '110.'.str_pad((string) $sequencia, 5, '0', STR_PAD_LEFT),
            'descricao' => 'Produto '.$sequencia,
            'status' => true,
            ...$dados,
        ]);
    }

    /**
     * Pedido do vendedor com uma amostra por prazo informado.
     *
     * @param array<int, \DateTimeInterface|string> $prazos
     */
    protected function criarPedido(User $vendedor, array $prazos = ['+10 days'], string $status = 'em_analise'): Pedido
    {
        $pedido = Pedido::create([
            'cliente_id' => $this->criarCliente()->id,
            'user_id' => $vendedor->id,
            'estabelecimento_id' => $vendedor->estabelecimento_id,
            'data_emissao' => today(),
            'status' => 'aberto',
        ]);

        foreach ($prazos as $prazo) {
            Amostra::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $this->criarProduto()->id,
                'quantidade' => 1,
                'status' => $status,
                'prazo_avaliacao' => is_string($prazo) ? today()->modify($prazo) : $prazo,
            ]);
        }

        return $pedido->fresh();
    }

    /** Permissões típicas de um vendedor. */
    protected function permissoesVendedor(): array
    {
        return [
            'pedidos' => '*',
            'amostras' => ['visualizar', 'criar', 'editar', 'cancelar'],
            'solicitacoes_prazo' => ['visualizar', 'criar', 'excluir'],
            'clientes' => ['visualizar'],
            'produtos' => ['visualizar'],
        ];
    }

    /** Permissões típicas de um gestor. */
    protected function permissoesGestor(): array
    {
        return [
            'pedidos' => '*',
            'amostras' => '*',
            'solicitacoes_prazo' => '*',
            'clientes' => '*',
            'produtos' => '*',
            'logs' => '*',
        ];
    }
}
