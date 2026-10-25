<?php

namespace Tests\Feature;

use App\Models\Solicitacao_Prazo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RN008 - alteração de prazo com justificativa, aprovada pelo gestor ou administrador.
 */
class SolicitacaoPrazoTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendedor_solicita_e_o_gestor_do_estabelecimento_aprova(): void
    {
        $estabelecimento = $this->criarEstabelecimento();
        $vendedor = $this->criarUsuario('representante', $estabelecimento, $this->permissoesVendedor());
        $gestor = $this->criarUsuario('gestor', $estabelecimento, $this->permissoesGestor());
        $amostra = $this->criarPedido($vendedor, ['-1 day'], 'atrasada')->amostras->first();
        $novoPrazo = today()->addDays(15)->format('Y-m-d');

        $this->actingAs($vendedor)->post(route('solicitacoes-prazo.store', $amostra), [
            'novo_prazo' => $novoPrazo,
            'justificativa' => 'O cliente ainda não testou o produto em campo.',
        ])->assertRedirect(route('amostras.show', $amostra));

        // o prazo só muda depois da aprovação
        $this->assertNotSame($novoPrazo, $amostra->fresh()->prazo_avaliacao->format('Y-m-d'));

        $solicitacao = Solicitacao_Prazo::first();
        $this->actingAs($gestor)->put(route('solicitacoes-prazo.update', $solicitacao), ['situacao' => 'aprovada'])
            ->assertRedirect();

        $amostra->refresh();
        $this->assertSame($novoPrazo, $amostra->prazo_avaliacao->format('Y-m-d'));
        $this->assertSame('em_analise', $amostra->status, 'Com o prazo novo, a amostra volta a ficar em análise.');
        $this->assertSame($gestor->id, $solicitacao->fresh()->aprovador_id);
    }

    public function test_justificativa_e_obrigatoria_e_o_prazo_precisa_ser_maior(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: $this->permissoesVendedor());
        $amostra = $this->criarPedido($vendedor, ['+10 days'])->amostras->first();

        $this->actingAs($vendedor)->post(route('solicitacoes-prazo.store', $amostra), [
            'novo_prazo' => today()->addDays(5)->format('Y-m-d'),
            'justificativa' => '',
        ])->assertSessionHasErrorsIn('solicitacao', ['novo_prazo', 'justificativa']);

        $this->assertSame(0, Solicitacao_Prazo::count());
    }

    public function test_vendedor_nao_aprova_mesmo_com_a_permissao(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: [...$this->permissoesVendedor(), 'solicitacoes_prazo' => '*']);
        $colega = $this->criarUsuario('representante', permissoes: [...$this->permissoesVendedor(), 'solicitacoes_prazo' => '*']);
        $amostra = $this->criarPedido($vendedor)->amostras->first();
        $solicitacao = $this->solicitar($amostra, $vendedor);

        $this->actingAs($colega)->put(route('solicitacoes-prazo.update', $solicitacao), ['situacao' => 'aprovada'])
            ->assertSessionHas('error');

        $this->assertSame('pendente', $solicitacao->fresh()->situacao);
    }

    public function test_gestor_nao_decide_solicitacao_de_outro_estabelecimento_nem_a_propria(): void
    {
        $meu = $this->criarEstabelecimento();
        $outro = $this->criarEstabelecimento();
        $gestor = $this->criarUsuario('gestor', $meu, $this->permissoesGestor());
        $vendedorDeFora = $this->criarUsuario('representante', $outro);
        $solicitacaoDeFora = $this->solicitar($this->criarPedido($vendedorDeFora)->amostras->first(), $vendedorDeFora);

        $this->actingAs($gestor)->put(route('solicitacoes-prazo.update', $solicitacaoDeFora), ['situacao' => 'aprovada']);
        $this->assertSame('pendente', $solicitacaoDeFora->fresh()->situacao);

        $propria = $this->solicitar($this->criarPedido($this->criarUsuario('representante', $meu))->amostras->first(), $gestor);
        $this->actingAs($gestor)->put(route('solicitacoes-prazo.update', $propria), ['situacao' => 'aprovada']);
        $this->assertSame('pendente', $propria->fresh()->situacao);
    }

    public function test_reprovar_exige_motivo(): void
    {
        $admin = $this->criarUsuario('admin');
        $vendedor = $this->criarUsuario('representante');
        $solicitacao = $this->solicitar($this->criarPedido($vendedor)->amostras->first(), $vendedor);

        $this->actingAs($admin)->put(route('solicitacoes-prazo.update', $solicitacao), ['situacao' => 'reprovada'])
            ->assertSessionHasErrorsIn('decisao', 'motivo_aprovacao_reprovacao');

        $this->assertSame('pendente', $solicitacao->fresh()->situacao);
    }

    public function test_lista_aguardando_decisao_do_gestor(): void
    {
        $estabelecimento = $this->criarEstabelecimento();
        $gestor = $this->criarUsuario('gestor', $estabelecimento, $this->permissoesGestor());
        $vendedor = $this->criarUsuario('representante', $estabelecimento);
        $amostra = $this->criarPedido($vendedor)->amostras->first();
        $this->solicitar($amostra, $vendedor);

        $this->actingAs($gestor)->get(route('solicitacoes-prazo.index'))
            ->assertOk()
            ->assertSee('Aguardando minha decisão')
            ->assertSee($amostra->codigo);
    }

    private function solicitar($amostra, $solicitante): Solicitacao_Prazo
    {
        return Solicitacao_Prazo::create([
            'amostra_id' => $amostra->id,
            'solicitante_id' => $solicitante->id,
            'justificativa' => 'Preciso de mais tempo para o cliente avaliar.',
            'prazo_atual' => $amostra->prazo_avaliacao,
            'novo_prazo' => $amostra->prazo_avaliacao->copy()->addDays(10),
            'situacao' => 'pendente',
            'data_solicitacao' => now(),
        ]);
    }
}
