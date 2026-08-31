<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/produtos',[ProdutoController::class,'index']);
Route::get('/produtos/create',[ProdutoController::class, 'create']);
Route::get('/produtos/{produto}',[ProdutoController::class, 'show']);
Route::get('/produtos/{produto}/edit',[ProdutoController::class,'edit']);

Route::post('/produtos/store',[ProdutoController::class,'store']);
Route::post('produtos/{produto}/update',[ProdutoController::class,'update']);

Route::delete('/produtos/{produto}/destroy',[ProdutoController::class,'destroy']);
