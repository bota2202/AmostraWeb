<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Login por código de usuário e senha (RF001 / RN019) e saída segura (RF002).
 */
class LoginController extends Controller
{
    // tentativas permitidas por minuto para o mesmo código + IP
    private const MAX_TENTATIVAS = 5;

    public function showLogin()
    {
        return view('login.login');
    }

    public function login(LoginRequest $request)
    {
        $credenciais = $request->validated();
        $chave = Str::lower($credenciais['codigo_usuario']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($chave, self::MAX_TENTATIVAS)) {
            return back()->withInput($request->only('codigo_usuario'))
                ->with('error', __('auth.throttle', ['seconds' => RateLimiter::availableIn($chave)]));
        }

        // usuários inativos não podem entrar
        if (! Auth::attempt([...$credenciais, 'status' => true])) {
            RateLimiter::hit($chave, 60);

            $usuario = User::where('codigo_usuario', $credenciais['codigo_usuario'])->first();
            Log::registrar(
                $usuario,
                'login_falhou',
                observacao: $usuario
                    ? ($usuario->status ? 'Senha incorreta' : 'Usuário inativo')
                    : 'Código de usuário inexistente: '.Str::limit($credenciais['codigo_usuario'], 50),
                modulo: 'autenticacao',
            );

            return back()->withInput($request->only('codigo_usuario'))->with('error', __('auth.failed'));
        }

        RateLimiter::clear($chave);
        $request->session()->regenerate();
        Log::registrar($request->user(), 'login', observacao: 'IP '.$request->ip(), modulo: 'autenticacao');

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Log::registrar($request->user(), 'logout', modulo: 'autenticacao');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('showLogin');
    }
}
