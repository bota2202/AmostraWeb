<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstabelecimentoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AmostraController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissaoController;
use App\Http\Controllers\PedidoController;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'showDashboard']);
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::resource('produtos', ProdutoController::class);
    Route::resource('estabelecimentos', EstabelecimentoController::class);
    Route::resource('usuarios', UserController::class);
    Route::resource('amostras',AmostraController::class);
    Route::resource('clientes',ClienteController::class);
    Route::resource('permissoes',PermissaoController::class);
    Route::resource('pedidos',PedidoController::class);


    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/login', [LoginController::class, 'showLogin'])->name('showLogin');
});
