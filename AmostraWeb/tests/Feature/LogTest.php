<?php

namespace Tests\Feature;

use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

/**
 * RN015 / RN020 / RNF005 - logs automáticos e protegidos.
 */
class LogTest extends TestCase
{
    use RefreshDatabase;

    public function test_alteracao_registra_usuario_campo_antes_e_depois(): void
    {
        $admin = $this->criarUsuario('admin');
        $cliente = $this->criarCliente(['razao_social' => 'Nome Antigo LTDA']);

        $this->actingAs($admin)->put(route('clientes.update', $cliente), [
            'razao_social' => 'Nome Novo LTDA',
            'tipo' => 'Revenda',
        ])->assertRedirect();

        $this->assertDatabaseHas('logs', [
            'user_id' => $admin->id,
            'modulo' => 'clientes',
            'tipo_registro' => 'Cliente',
            'registro_id' => $cliente->id,
            'acao' => 'editar',
            'campo_alterado' => 'razao_social',
            'valor_anterior' => 'Nome Antigo LTDA',
            'valor_novo' => 'Nome Novo LTDA',
        ]);
    }

    public function test_senha_alterada_nao_grava_o_valor(): void
    {
        $admin = $this->criarUsuario('admin');
        $usuario = $this->criarUsuario('representante');

        $this->actingAs($admin)->patch(route('usuarios.senha', $usuario), [
            'password' => 'novaSenha1',
            'password_confirmation' => 'novaSenha1',
        ]);

        $log = Log::where('campo_alterado', 'password')->first();
        $this->assertNotNull($log);
        $this->assertSame('••••••', $log->valor_novo);
    }

    public function test_registros_de_log_nao_podem_ser_alterados_nem_excluidos(): void
    {
        $log = Log::registrar(null, 'login', observacao: 'teste', modulo: 'autenticacao');

        $this->expectException(LogicException::class);
        $log->update(['observacao' => 'adulterado']);
    }

    public function test_lista_filtra_por_modulo(): void
    {
        $admin = $this->criarUsuario('admin');
        $this->criarCliente();
        $this->criarProduto();
        $logCliente = Log::where('modulo', 'clientes')->first();
        $logProduto = Log::where('modulo', 'produtos')->first();

        $this->actingAs($admin)->get('/logs?modulo=clientes')
            ->assertOk()
            ->assertSee(route('logs.show', $logCliente))
            ->assertDontSee(route('logs.show', $logProduto));
    }

    public function test_detalhe_mostra_registro_valores_e_ip(): void
    {
        $admin = $this->criarUsuario('admin');
        $cliente = $this->criarCliente(['razao_social' => 'Antigo LTDA']);

        $this->actingAs($admin)
            ->withServerVariables(['REMOTE_ADDR' => '10.0.0.25'])
            ->put(route('clientes.update', $cliente), ['razao_social' => 'Novo LTDA', 'tipo' => 'Revenda']);

        $log = Log::where('campo_alterado', 'razao_social')->first();
        $this->assertSame('10.0.0.25', $log->ip);

        $this->actingAs($admin)->get(route('logs.show', $log))
            ->assertOk()
            ->assertSee('Cliente Novo LTDA')
            ->assertSee('Antigo LTDA')
            ->assertSee('10.0.0.25');
    }

    public function test_campos_salvos_juntos_viram_uma_acao_so(): void
    {
        $admin = $this->criarUsuario('admin');
        $cliente = $this->criarCliente(['razao_social' => 'Antigo LTDA', 'tipo' => 'Revenda']);

        $this->actingAs($admin)->put(route('clientes.update', $cliente), [
            'razao_social' => 'Novo LTDA',
            'tipo' => 'Consumidor',
        ]);

        $lotes = Log::where('acao', 'editar')->where('registro_id', $cliente->id)->pluck('lote')->unique();
        $this->assertCount(1, $lotes, 'Os campos de um mesmo salvar devem ficar no mesmo lote.');

        $this->actingAs($admin)->get('/logs')
            ->assertOk()
            ->assertSeeInOrder([$admin->name, 'alterou o cliente', 'Novo LTDA'])
            ->assertSee('Antigo LTDA → Novo LTDA');
    }

    public function test_lista_agrupa_por_dia_e_filtra_por_tipo(): void
    {
        $admin = $this->criarUsuario('admin');
        $this->actingAs($admin);
        $this->criarCliente(['razao_social' => 'Cliente Hoje LTDA']);
        Log::registrar($admin, 'login', modulo: 'autenticacao');

        $this->get('/logs')->assertOk()->assertSee('Hoje')->assertSee('cadastrou o cliente')->assertSee('entrou no sistema');

        $this->get('/logs?tipo=acessos')->assertOk()
            ->assertSee('entrou no sistema')
            ->assertDontSee('cadastrou o cliente');
    }

    public function test_detalhe_de_exclusao_mostra_os_dados_do_registro_excluido(): void
    {
        $admin = $this->criarUsuario('admin');
        $cliente = $this->criarCliente(['razao_social' => 'Excluido LTDA']);

        $this->actingAs($admin)->delete(route('clientes.destroy', $cliente));
        $log = Log::where('acao', 'excluir')->where('tipo_registro', 'Cliente')->firstOrFail();

        $this->actingAs($admin)->get(route('logs.show', $log))
            ->assertOk()
            ->assertSee('excluiu o cliente')
            ->assertSee('Excluido LTDA')
            ->assertSee('Dados no momento da exclusão');
    }
}