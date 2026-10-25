<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstabelecimentoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AmostraController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SolicitacaoPrazoController;
use App\Http\Controllers\UsuarioPermissaoController;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'showDashboard']);
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');

    Route::resource('produtos', ProdutoController::class);
    Route::resource('estabelecimentos', EstabelecimentoController::class);
    Route::resource('usuarios', UserController::class);
    Route::resource('amostras',AmostraController::class);
    Route::resource('clientes',ClienteController::class);
    Route::resource('grupos', GrupoController::class); // tela "Permissões" da sidebar
    Route::resource('pedidos',PedidoController::class);
    Route::resource('logs', LogController::class)->only(['index', 'show']);

    // Ativar / inativar
    Route::patch('/produtos/{produto}/status', [ProdutoController::class, 'alterarStatus'])->name('produtos.status');
    Route::patch('/estabelecimentos/{estabelecimento}/status', [EstabelecimentoController::class, 'alterarStatus'])->name('estabelecimentos.status');
    Route::patch('/usuarios/{usuario}/status', [UserController::class, 'alterarStatus'])->name('usuarios.status');
    Route::patch('/clientes/{cliente}/status', [ClienteController::class, 'alterarStatus'])->name('clientes.status');

    // Usuários: senha e permissões (a tela de permissões é uma aba do editar)
    Route::patch('/usuarios/{usuario}/senha', [UserController::class, 'alterarSenha'])->name('usuarios.senha');
    Route::put('/usuarios/{usuario}/permissoes', [UsuarioPermissaoController::class, 'update'])->name('usuarios.permissoes.update');

    // Solicitações de prazo das amostras
    Route::post('/amostras/{amostra}/solicitacoes-prazo', [SolicitacaoPrazoController::class, 'store'])->name('solicitacoes-prazo.store');
    Route::put('/solicitacoes-prazo/{solicitacao}', [SolicitacaoPrazoController::class, 'update'])->name('solicitacoes-prazo.update');
    Route::delete('/solicitacoes-prazo/{solicitacao}', [SolicitacaoPrazoController::class, 'destroy'])->name('solicitacoes-prazo.destroy');

    // Meu perfil
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.editar');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::patch('/perfil/senha', [PerfilController::class, 'alterarSenha'])->name('perfil.senha');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/login', [LoginController::class, 'showLogin'])->name('showLogin');
});
