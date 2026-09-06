<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstabelecimentoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'showDashboard']);
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::resource('produtos', ProdutoController::class);
    Route::resource('estabelecimentos', EstabelecimentoController::class);
    Route::resource('usuarios', UserController::class);


    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/login', [LoginController::class, 'showLogin'])->name('showLogin');
});
