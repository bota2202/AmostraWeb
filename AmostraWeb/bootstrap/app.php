<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permissao' => \App\Http\Middleware\VerificarPermissao::class,
        ]);

        // visitante tentando abrir uma página interna vai para o login
        $middleware->redirectGuestsTo(fn () => route('showLogin'));
        // usuário logado abrindo o login vai para o dashboard
        $middleware->redirectUsersTo(fn () => route('dashboard'));

        // marca amostras com prazo vencido como "Atrasada" (RN009), no máximo a cada 10 min
        $middleware->web(append: [
            \App\Http\Middleware\AtualizarAmostrasAtrasadas::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
