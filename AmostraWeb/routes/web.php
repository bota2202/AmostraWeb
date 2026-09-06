<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstabelecimentoController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('produtos', ProdutoController::class);
Route::resource('estabelecimentos',EstabelecimentoController::class);
Route::resource('usuarios',UserController::class);
