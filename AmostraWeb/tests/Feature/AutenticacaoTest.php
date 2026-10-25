<?php

namespace Tests\Feature;

use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RF001 / RF002 / RN019 - login por código de usuário, saída segura.
 */
class AutenticacaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_entra_com_codigo_de_usuario_e_senha(): void
    {
        $user = $this->criarUsuario('admin');

        $this->post('/login', ['codigo_usuario' => $user->codigo_usuario, 'password' => 'senha123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('logs', ['acao' => 'login', 'user_id' => $user->id]);
    }

    public function test_senha_errada_nao_entra_e_fica_na_auditoria(): void
    {
        $user = $this->criarUsuario('admin');

        $this->from('/login')
            ->post('/login', ['codigo_usuario' => $user->codigo_usuario, 'password' => 'errada'])
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Código de usuário ou senha inválidos.');

        $this->assertGuest();
        $this->assertDatabaseHas('logs', ['acao' => 'login_falhou', 'registro_id' => $user->id]);
    }

    public function test_usuario_inativo_nao_entra(): void
    {
        $user = $this->criarUsuario('admin', dados: ['status' => false]);

        $this->post('/login', ['codigo_usuario' => $user->codigo_usuario, 'password' => 'senha123']);

        $this->assertGuest();
    }

    public function test_bloqueia_apos_cinco_tentativas(): void
    {
        $user = $this->criarUsuario('admin');

        foreach (range(1, 5) as $tentativa) {
            $this->post('/login', ['codigo_usuario' => $user->codigo_usuario, 'password' => 'errada']);
        }

        // mesmo com a senha certa, fica bloqueado por um tempo
        $this->post('/login', ['codigo_usuario' => $user->codigo_usuario, 'password' => 'senha123'])
            ->assertSessionHas('error', fn ($mensagem) => str_contains($mensagem, 'Muitas tentativas'));

        $this->assertGuest();
    }

    public function test_visitante_e_levado_ao_login(): void
    {
        $this->get('/pedidos')->assertRedirect(route('showLogin'));
    }

    public function test_sair_encerra_a_sessao(): void
    {
        $user = $this->criarUsuario('admin');

        $this->actingAs($user)->post('/logout')->assertRedirect(route('showLogin'));

        $this->assertGuest();
        $this->assertSame('logout', Log::latest('id')->value('acao'));
    }
}
