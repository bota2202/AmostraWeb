<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloqueia o acesso quando o usuário não tem a permissão do módulo (RNF004).
 *
 * Uso nas rotas:
 *   ->middleware('permissao:amostras')            ação deduzida do método do controller
 *   ->middleware('permissao:amostras,cancelar')   ação informada
 */
class VerificarPermissao
{
    // método do controller => ação da permissão
    private const ACOES_POR_METODO = [
        'index' => 'visualizar',
        'show' => 'visualizar',
        'create' => 'criar',
        'store' => 'criar',
        'edit' => 'editar',
        'update' => 'editar',
        'destroy' => 'excluir',
    ];

    public function handle(Request $request, Closure $next, string $modulo, ?string $acao = null): Response
    {
        $acao ??= self::ACOES_POR_METODO[$request->route()->getActionMethod()] ?? 'editar';

        if (! $request->user()?->temPermissao($modulo, $acao)) {
            $acaoNome = mb_strtolower(config('amostraweb.acoes')[$acao] ?? $acao);
            $moduloNome = mb_strtolower(config('amostraweb.modulos')[$modulo] ?? $modulo);

            abort(403, "Você não tem permissão para {$acaoNome} em {$moduloNome}.");
        }

        return $next($request);
    }
}
