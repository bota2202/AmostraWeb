<?php

namespace Tests\Feature;

use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fluxo do pedido (04 - Fluxo das Amostras / RN001 / RN003 / RN004 / RN017 / RN018).
 */
class PedidoTest extends TestCase
{
    use RefreshDatabase;

    private function dadosPedido(array $extra = []): array
    {
        return [
            'cliente_id' => $this->criarCliente()->id,
            'data_emissao' => today()->format('Y-m-d'),
            'amostras' => [
                ['produto_id' => $this->criarProduto()->id, 'quantidade' => 2, 'prazo_avaliacao' => today()->addDays(10)->format('Y-m-d')],
                ['produto_id' => $this->criarProduto()->id, 'quantidade' => 1, 'prazo_avaliacao' => today()->addDays(20)->format('Y-m-d')],
            ],
            ...$extra,
        ];
    }

    public function test_pedido_cria_uma_amostra_por_produto_com_codigos_automaticos(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: $this->permissoesVendedor());

        $this->actingAs($vendedor)->post('/pedidos', $this->dadosPedido())->assertRedirect();

        $pedido = Pedido::with('amostras')->first();
        $this->assertSame('000001', $pedido->codigo);
        $this->assertSame(['000001', '000002'], $pedido->amostras->pluck('codigo')->sort()->values()->all());
        $this->assertTrue($pedido->amostras->every(fn ($a) => $a->status === 'em_analise'));
    }

    public function test_pedido_sem_produtos_e_recusado(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: $this->permissoesVendedor());

        $this->actingAs($vendedor)
            ->post('/pedidos', $this->dadosPedido(['amostras' => []]))
            ->assertSessionHasErrors('amostras');

        $this->assertSame(0, Pedido::count());
    }

    public function test_vendedor_e_sempre_o_responsavel_e_o_estabelecimento_vem_dele(): void
    {
        $outroEstabelecimento = $this->criarEstabelecimento();
        $vendedor = $this->criarUsuario('representante', $outroEstabelecimento, $this->permissoesVendedor());
        $colega = $this->criarUsuario('representante');

        $this->actingAs($vendedor)->post('/pedidos', $this->dadosPedido(['user_id' => $colega->id]));

        $pedido = Pedido::first();
        $this->assertSame($vendedor->id, $pedido->user_id);
        $this->assertSame($outroEstabelecimento->id, $pedido->estabelecimento_id);
    }

    public function test_gestor_escolhe_o_vendedor_e_pode_trocar_o_responsavel(): void
    {
        $estabelecimento = $this->criarEstabelecimento();
        $gestor = $this->criarUsuario('gestor', $estabelecimento, $this->permissoesGestor());
        $vendedorA = $this->criarUsuario('representante', $estabelecimento);
        $vendedorB = $this->criarUsuario('representante', $estabelecimento);

        $this->actingAs($gestor)->post('/pedidos', $this->dadosPedido(['user_id' => $vendedorA->id]));
        $pedido = Pedido::first();
        $this->assertSame($vendedorA->id, $pedido->user_id);

        $this->actingAs($gestor)->put(route('pedidos.update', $pedido), [
            'cliente_id' => $pedido->cliente_id,
            'data_emissao' => today()->format('Y-m-d'),
            'user_id' => $vendedorB->id,
        ])->assertRedirect();

        $this->assertSame($vendedorB->id, $pedido->fresh()->user_id);
    }

    public function test_vendedor_ve_so_os_proprios_pedidos(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: $this->permissoesVendedor());
        $colega = $this->criarUsuario('representante');
        $meu = $this->criarPedido($vendedor);
        $doColega = $this->criarPedido($colega);

        $this->actingAs($vendedor)->get('/pedidos')
            ->assertSee($meu->codigo)
            ->assertDontSee(route('pedidos.show', $doColega));

        $this->actingAs($vendedor)->get(route('pedidos.show', $doColega))->assertForbidden();
    }

    public function test_gestor_ve_os_pedidos_do_seu_estabelecimento(): void
    {
        $meuEstabelecimento = $this->criarEstabelecimento();
        $outro = $this->criarEstabelecimento();
        $gestor = $this->criarUsuario('gestor', $meuEstabelecimento, $this->permissoesGestor());

        $daMinhaEquipe = $this->criarPedido($this->criarUsuario('representante', $meuEstabelecimento));
        $deOutraEquipe = $this->criarPedido($this->criarUsuario('representante', $outro));

        $this->actingAs($gestor)->get(route('pedidos.show', $daMinhaEquipe))->assertOk();
        $this->actingAs($gestor)->get(route('pedidos.show', $deOutraEquipe))->assertForbidden();
    }

    public function test_filtro_de_pedidos_por_codigo(): void
    {
        $admin = $this->criarUsuario('admin');
        $vendedor = $this->criarUsuario('representante');
        $primeiro = $this->criarPedido($vendedor);
        $segundo = $this->criarPedido($vendedor);

        $this->actingAs($admin)->get('/pedidos?busca='.$segundo->codigo)
            ->assertSee(route('pedidos.show', $segundo))
            ->assertDontSee(route('pedidos.show', $primeiro));
    }

    public function test_status_do_pedido_acompanha_as_amostras(): void
    {
        $vendedor = $this->criarUsuario('representante');
        $pedido = $this->criarPedido($vendedor, ['+5 days', '+6 days']);

        [$a, $b] = $pedido->amostras;
        $a->alterarStatus('aprovada');
        $this->assertSame('aberto', $pedido->fresh()->status);

        $b->alterarStatus('reprovada');
        $this->assertSame('fechado', $pedido->fresh()->status);
    }
}
