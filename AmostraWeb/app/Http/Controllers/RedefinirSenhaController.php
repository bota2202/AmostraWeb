<?php

namespace App\Http\Controllers;

use App\Mail\CodigoRedefinicaoSenha;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * "Esqueci minha senha": o usuário informa o e-mail da conta, recebe um código
 * de 6 dígitos e, com ele, define uma nova senha.
 *
 * - O código vale por 15 minutos e aceita no máximo 5 tentativas erradas.
 * - O código é guardado com hash (nunca em texto puro).
 * - A tela nunca diz se o e-mail existe ou não (evita descobrir contas).
 */
class RedefinirSenhaController extends Controller
{
    public const VALIDADE_MINUTOS = 15;

    public const MAX_TENTATIVAS = 5;

    // envios de código permitidos por e-mail + IP a cada 10 minutos
    private const MAX_ENVIOS = 3;

    public function pedirCodigo()
    {
        return view('login.esqueci-senha');
    }

    public function enviarCodigo(Request $request)
    {
        $dados = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $email = Str::lower(trim($dados['email']));

        $chave = 'redefinir-senha|'.$email.'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($chave, self::MAX_ENVIOS)) {
            return back()->withInput()->with('error', 'Muitas solicitações. Tente novamente em '.ceil(RateLimiter::availableIn($chave) / 60).' minuto(s).');
        }
        RateLimiter::hit($chave, 600);

        $usuario = User::whereRaw('LOWER(email) = ?', [$email])->first();

        // usuários inativos não recebem código (não podem entrar no sistema)
        if ($usuario && $usuario->status) {
            $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => Hash::make($codigo), 'tentativas' => 0, 'created_at' => now()],
            );

            Mail::to($usuario->email)->send(new CodigoRedefinicaoSenha($usuario, $codigo, self::VALIDADE_MINUTOS));

            Log::registrar($usuario, 'senha_codigo', observacao: 'Código de redefinição de senha enviado por e-mail', modulo: 'autenticacao', userId: $usuario->id);
        }

        $request->session()->put('redefinir_senha_email', $email);

        return redirect()->route('senha.redefinir')
            ->with('success', 'Se o e-mail estiver cadastrado, você vai receber um código em instantes. Confira também a caixa de spam.');
    }

    public function informarCodigo(Request $request)
    {
        return view('login.redefinir-senha', [
            'email' => old('email', $request->session()->get('redefinir_senha_email')),
        ]);
    }

    public function redefinir(Request $request)
    {
        $dados = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'codigo' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [], [
            'codigo' => 'código',
            'password' => 'nova senha',
        ]);

        $email = Str::lower(trim($dados['email']));
        $registro = DB::table('password_reset_tokens')->where('email', $email)->first();
        $invalido = fn (string $mensagem) => back()->withInput($request->only('email'))->withErrors(['codigo' => $mensagem]);

        if (! $registro || now()->subMinutes(self::VALIDADE_MINUTOS)->greaterThan($registro->created_at)) {
            return $invalido('Código inválido ou expirado. Solicite um novo código.');
        }

        if ($registro->tentativas >= self::MAX_TENTATIVAS) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return $invalido('Muitas tentativas erradas. Solicite um novo código.');
        }

        if (! Hash::check($dados['codigo'], $registro->token)) {
            DB::table('password_reset_tokens')->where('email', $email)->increment('tentativas');
            $restantes = self::MAX_TENTATIVAS - $registro->tentativas - 1;

            return $invalido($restantes > 0
                ? "Código incorreto. Você ainda tem {$restantes} tentativa(s)."
                : 'Código incorreto. Solicite um novo código.');
        }

        $usuario = User::whereRaw('LOWER(email) = ?', [$email])->where('status', true)->first();
        if (! $usuario) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return $invalido('Código inválido ou expirado. Solicite um novo código.');
        }

        // grava a troca no log em nome do próprio usuário (ele não está logado)
        Log::semAuditoriaAutomatica(fn () => $usuario->update(['password' => $dados['password']]));
        Log::registrar($usuario, 'senha_redefinida', campo: 'password', valorAnterior: '••••••', valorNovo: '••••••',
            observacao: 'Senha redefinida com código enviado por e-mail', modulo: 'autenticacao', userId: $usuario->id);

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        $request->session()->forget('redefinir_senha_email');

        return redirect()->route('showLogin')->with('success', 'Senha redefinida. Entre com a nova senha.');
    }
}
