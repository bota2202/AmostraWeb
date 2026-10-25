<?php

namespace Tests\Feature;

use App\Models\Amostra;
use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ciclo de vida da amostra (04 - Fluxo das Amostras / RN009 a RN016 / RN023 / RN024).
 */
class AmostraTest extends TestCase
{
    use RefreshDatabase;

    public function test_aprova_mesmo_atrasada_e_depois_fica_travada(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: $this->permissoesVendedor());
        $amostra = $this->criarPedido($vendedor, ['+5 days'], 'atrasada')->amostras->first();

        $this->actingAs($vendedor)
            ->post(route('amostras.aprovar', $amostra), ['observacao' => 'Cliente gostou'])
            ->assertRedirect(route('amostras.show', $amostra));

        $this->assertSame('aprovada', $amostra->fresh()->status);

        // finalizada: não pode mais ser alterada (RN013 / fluxo 9)
        $this->actingAs($vendedor)->post(route('amostras.cancelar', $amostra), ['motivo' => 'Desistiu do pedido']);
        $this->assertSame('aprovada', $amostra->fresh()->status);
        $this->actingAs($vendedor)->get(route('amostras.edit', $amostra))->assertRedirect(route('amostras.show', $amostra));
    }

    public function test_cancelamento_exige_motivo(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: $this->permissoesVendedor());
        $amostra = $this->criarPedido($vendedor)->amostras->first();

        $this->actingAs($vendedor)->post(route('amostras.cancelar', $amostra), ['motivo' => ''])
            ->assertSessionHasErrorsIn('cancelamento', 'motivo');
        $this->assertSame('em_analise', $amostra->fresh()->status);

        $this->actingAs($vendedor)->post(route('amostras.cancelar', $amostra), ['motivo' => 'Cliente desistiu da compra']);
        $this->assertSame('cancelada', $amostra->fresh()->status);
        $this->assertDatabaseHas('logs', [
            'tipo_registro' => 'Amostra',
            'registro_id' => $amostra->id,
            'campo_alterado' => 'status',
            'valor_novo' => 'cancelada',
        ]);
    }

    public function test_cancelar_exige_a_permissao_cancelar(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: ['amostras' => ['visualizar', 'editar']]);
        $amostra = $this->criarPedido($vendedor)->amostras->first();

        $this->actingAs($vendedor)->post(route('amostras.cancelar', $amostra), ['motivo' => 'Sem permissão'])
            ->assertForbidden();
    }

    public function test_so_exclui_amostra_cancelada_e_nunca_a_ultima_do_pedido(): void
    {
        $admin = $this->criarUsuario('admin');
        $pedido = $this->criarPedido($this->criarUsuario('representante'), ['+5 days', '+6 days']);
        [$primeira, $segunda] = $pedido->amostras;

        // em análise: não exclui (RN014)
        $this->actingAs($admin)->delete(route('amostras.destroy', $primeira));
        $this->assertModelExists($primeira);

        // cancelada: exclui
        $primeira->alterarStatus('cancelada', 'teste');
        $this->actingAs($admin)->delete(route('amostras.destroy', $primeira))->assertRedirect(route('pedidos.show', $pedido));
        $this->assertModelMissing($primeira);
        $this->assertDatabaseHas('logs', ['tipo_registro' => 'Amostra', 'registro_id' => $primeira->id, 'acao' => 'excluir']);

        // última amostra do pedido: não exclui (RN023)
        $segunda->alterarStatus('cancelada', 'teste');
        $this->actingAs($admin)->delete(route('amostras.destroy', $segunda));
        $this->assertModelExists($segunda);
    }

    public function test_edicao_nao_troca_o_pedido(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: $this->permissoesVendedor());
        $amostra = $this->criarPedido($vendedor)->amostras->first();
        $outroPedido = $this->criarPedido($vendedor);

        $this->actingAs($vendedor)->put(route('amostras.update', $amostra), [
            'produto_id' => $amostra->produto_id,
            'quantidade' => 7,
            'pedido_id' => $outroPedido->id,
        ])->assertRedirect();

        $amostra->refresh();
        $this->assertSame(7, $amostra->quantidade);
        $this->assertNotSame($outroPedido->id, $amostra->pedido_id);
    }

    public function test_prazo_vencido_vira_atrasada_automaticamente_pelo_sistema(): void
    {
        $vendedor = $this->criarUsuario('representante');
        $pedido = $this->criarPedido($vendedor, ['-2 days', '+3 days']);

        $this->artisan('amostras:atualizar-atrasadas')->assertSuccessful();

        $statuses = $pedido->amostras()->orderBy('prazo_avaliacao')->pluck('status')->all();
        $this->assertSame(['atrasada', 'em_analise'], $statuses);

        $log = Log::where('campo_alterado', 'status')->where('valor_novo', 'atrasada')->first();
        $this->assertNull($log->user_id, 'A mudança automática deve ser registrada como "Sistema".');
    }

    public function test_historico_da_amostra_fica_no_modulo_de_logs(): void
    {
        $admin = $this->criarUsuario('admin');
        $amostra = $this->criarPedido($admin)->amostras->first();

        $this->actingAs($admin)->put(route('amostras.update', $amostra), ['produto_id' => $amostra->produto_id, 'quantidade' => 9]);

        // a tela da amostra só tem o botão que abre o log filtrado
        $filtro = route('logs.index', ['registro' => 'Amostra-'.$amostra->id]);
        $this->actingAs($admin)->get(route('amostras.show', $amostra))
            ->assertOk()
            ->assertDontSee('Histórico da amostra')
            ->assertSee($filtro, false);

        $this->actingAs($admin)->get($filtro)
            ->assertOk()
            ->assertSee('Histórico de')
            ->assertSee('Amostra '.$amostra->codigo)
            ->assertSee('Quantidade');
    }

    public function test_amostra_sem_pedido_e_recusada(): void
    {
        $admin = $this->criarUsuario('admin');

        $this->actingAs($admin)->post('/amostras', [
            'produto_id' => $this->criarProduto()->id,
            'quantidade' => 1,
            'prazo_avaliacao' => today()->addDays(5)->format('Y-m-d'),
        ])->assertSessionHasErrors('pedido_id');

        $this->assertSame(0, Amostra::count());
    }
}
