<?php

namespace Tests\Feature;

use App\Models\Permissao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RN022 / RNF004 - acesso controlado por permissões (próprias + grupos).
 */
class PermissaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_permissoes_padrao_sao_criadas_pela_migration(): void
    {
        $this->assertSame(
            collect(config('amostraweb.permissoes_padrao'))->flatten()->count(),
            Permissao::count()
        );
        $this->assertDatabaseHas('permissoes', ['modulo' => 'amostras', 'acao' => 'cancelar', 'nome' => 'Cancelar amostras']);
    }

    public function test_sem_permissao_recebe_403(): void
    {
        $vendedor = $this->criarUsuario('representante');

        $this->actingAs($vendedor)->get('/clientes')->assertForbidden();
        $this->actingAs($vendedor)->get('/usuarios')->assertForbidden();
    }

    public function test_dashboard_e_perfil_sao_liberados_para_todos(): void
    {
        $vendedor = $this->criarUsuario('representante');

        $this->actingAs($vendedor)->get('/dashboard')->assertOk();
        $this->actingAs($vendedor)->get('/perfil')->assertOk();
    }

    public function test_permissao_individual_libera_a_acao(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: ['clientes' => ['visualizar']]);

        $this->actingAs($vendedor)->get('/clientes')->assertOk();
        $this->actingAs($vendedor)->get('/clientes/create')->assertForbidden();
    }

    public function test_permissao_vem_do_grupo(): void
    {
        $vendedor = $this->criarUsuario('representante');
        $this->criarGrupo('Vendedores', ['produtos' => ['visualizar']], [$vendedor]);

        $this->assertTrue($vendedor->fresh()->temPermissao('produtos', 'visualizar'));
        $this->actingAs($vendedor)->get('/produtos')->assertOk();
    }

    public function test_administrador_tem_todas_as_permissoes(): void
    {
        $admin = $this->criarUsuario('admin');

        $this->assertSame(Permissao::count(), $admin->permissoesEfetivas()->count());
        $this->actingAs($admin)->get('/usuarios')->assertOk();
        $this->actingAs($admin)->get('/logs')->assertOk();
    }

    public function test_menu_mostra_so_o_que_o_usuario_pode_ver(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: ['pedidos' => ['visualizar']]);

        $this->actingAs($vendedor)->get('/dashboard')
            ->assertSee('Pedidos')
            ->assertDontSee(route('usuarios.index'))
            ->assertDontSee(route('logs.index'));
    }

    public function test_permissoes_vem_so_dos_grupos_e_a_alteracao_fica_no_log(): void
    {
        $admin = $this->criarUsuario('admin');
        $vendedor = $this->criarUsuario('representante');
        $grupo = $this->criarGrupo('Vendedores', [], [$vendedor]);

        $this->assertFalse($vendedor->fresh()->temPermissao('clientes', 'visualizar'));

        $this->actingAs($admin)->put(route('grupos.update', $grupo), [
            'nome' => 'Vendedores',
            'usuarios' => [$vendedor->id],
            'permissoes' => $this->idsPermissoes(['clientes' => ['visualizar']]),
        ])->assertRedirect();

        $this->assertTrue($vendedor->fresh()->temPermissao('clientes', 'visualizar'));
        $this->assertDatabaseHas('logs', ['acao' => 'permissoes', 'registro_id' => $grupo->id, 'user_id' => $admin->id]);

        // saiu do grupo: perde as permissões dele
        $this->actingAs($admin)->put(route('grupos.update', $grupo), ['nome' => 'Vendedores', 'usuarios' => []]);
        $this->assertFalse($vendedor->fresh()->temPermissao('clientes', 'visualizar'));
    }

    public function test_nao_existe_mais_tela_de_permissao_individual(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('usuarios.permissoes.update'));
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasTable('usuario_permissoes'));

        $admin = $this->criarUsuario('admin');
        $vendedor = $this->criarUsuario('representante');
        $this->actingAs($admin)->get(route('usuarios.edit', $vendedor))->assertOk()->assertDontSee('form-permissoes');
    }
}
