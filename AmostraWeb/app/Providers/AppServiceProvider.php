<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        // datas por extenso no idioma do sistema (ex.: "25 de outubro de 2026")
        \Illuminate\Support\Carbon::setLocale(config('app.locale'));

        // paginação própria (total de registros + rótulos acessíveis em pt-BR)
        Paginator::defaultView('vendor.pagination.amostraweb');

        /*
         * Mostra um trecho da tela só para quem tem a permissão:
         *   @permite('amostras', 'criar') ... @endpermite
         */
        Blade::if('permite', function (string $modulo, string $acao = 'visualizar') {
            return (bool) auth()->user()?->temPermissao($modulo, $acao);
        });

        // menu lateral conforme as permissões do usuário
        View::composer('layouts.app', \App\View\Composers\MenuComposer::class);
    }
}
