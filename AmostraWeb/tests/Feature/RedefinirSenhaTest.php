<?php

namespace Tests\Feature;

use App\Mail\CodigoRedefinicaoSenha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * "Esqueci minha senha": código de 6 dígitos enviado ao e-mail da conta.
 */
class RedefinirSenhaTest extends TestCase
{
    use RefreshDatabase;

    private function pedirCodigo(string $email): ?string
    {
        $codigo = null;
        Mail::fake();

        $this->post(route('senha.enviar'), ['email' => $email])->assertRedirect(route('senha.redefinir'));

        Mail::assertSent(CodigoRedefinicaoSenha::class, function ($mail) use (&$codigo) {
            $codigo = $mail->codigo;
            return true;
        });

        return $codigo;
    }

    public function test_telas_abrem_e_o_login_tem_o_link(): void
    {
        $this->get(route('showLogin'))->assertOk()->assertSee(route('senha.esqueci'));
        $this->get(route('senha.esqueci'))->assertOk()->assertSee('Enviar código');
        $this->get(route('senha.redefinir'))->assertOk()->assertSee('Código recebido');
    }

    public function test_envia_codigo_e_redefine_a_senha(): void
    {
        $user = $this->criarUsuario();
        $codigo = $this->pedirCodigo($user->email);

        $this->assertMatchesRegularExpression('/^\d{6}$/', $codigo);
        // o código não fica em texto puro no banco
        $this->assertNotSame($codigo, DB::table('password_reset_tokens')->value('token'));

        $this->post(route('senha.atualizar'), [
            'email' => $user->email,
            'codigo' => $codigo,
            'password' => 'novaSenha1',
            'password_confirmation' => 'novaSenha1',
        ])->assertRedirect(route('showLogin'));

        $this->assertTrue(Hash::check('novaSenha1', $user->fresh()->password));
        $this->assertDatabaseCount('password_reset_tokens', 0);
        $this->assertDatabaseHas('logs', ['acao' => 'senha_redefinida', 'user_id' => $user->id, 'valor_novo' => '••••••']);

        // o código não pode ser usado de novo
        $this->post(route('senha.atualizar'), [
            'email' => $user->email, 'codigo' => $codigo,
            'password' => 'outra123', 'password_confirmation' => 'outra123',
        ])->assertSessionHasErrors('codigo');
    }

    public function test_email_inexistente_ou_inativo_nao_recebe_mas_a_tela_e_igual(): void
    {
        Mail::fake();
        $inativo = $this->criarUsuario(dados: ['status' => false]);

        $this->post(route('senha.enviar'), ['email' => 'ninguem@teste.local'])
            ->assertRedirect(route('senha.redefinir'))->assertSessionHas('success');
        $this->post(route('senha.enviar'), ['email' => $inativo->email])
            ->assertRedirect(route('senha.redefinir'))->assertSessionHas('success');

        Mail::assertNothingSent();
    }

    public function test_codigo_errado_conta_tentativas_e_bloqueia_na_quinta(): void
    {
        $user = $this->criarUsuario();
        $codigo = $this->pedirCodigo($user->email);
        $errado = $codigo === '000000' ? '111111' : '000000';
        $dados = ['email' => $user->email, 'codigo' => $errado, 'password' => 'novaSenha1', 'password_confirmation' => 'novaSenha1'];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('senha.atualizar'), $dados)->assertSessionHasErrors('codigo');
        }

        // mesmo com o código certo, depois de 5 erros precisa pedir outro
        $this->post(route('senha.atualizar'), [...$dados, 'codigo' => $codigo])->assertSessionHasErrors('codigo');
        $this->assertTrue(Hash::check('senha123', $user->fresh()->password));
    }

    public function test_codigo_expira_em_15_minutos(): void
    {
        $user = $this->criarUsuario();
        $codigo = $this->pedirCodigo($user->email);

        $this->travel(16)->minutes();

        $this->post(route('senha.atualizar'), [
            'email' => $user->email, 'codigo' => $codigo,
            'password' => 'novaSenha1', 'password_confirmation' => 'novaSenha1',
        ])->assertSessionHasErrors('codigo');
    }

    public function test_email_do_usuario_e_unico(): void
    {
        $existente = $this->criarUsuario();
        $admin = $this->criarUsuario('admin');

        $this->actingAs($admin)->post(route('usuarios.store'), [
            'estabelecimento_id' => $admin->estabelecimento_id,
            'name' => 'Outro',
            'email' => $existente->email,
            'codigo_usuario' => 'outro.usuario',
            'cargo' => 'representante',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ])->assertSessionHasErrors('email');
    }
}
