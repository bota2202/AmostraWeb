<?php

use App\Http\Controllers\AmostraController;
use App\Http\Controllers\AmostraStatusController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstabelecimentoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\ImportacaoController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RedefinirSenhaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\SolicitacaoPrazoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas do AmostraWeb
|--------------------------------------------------------------------------
| Cada módulo exige a permissão correspondente (middleware "permissao").
| Nas rotas "resource" a ação é deduzida do método:
|   index/show = visualizar, create/store = criar, edit/update = editar, destroy = excluir
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('showLogin');
    Route::post('/login', [LoginController::class, 'login'])->name('login');

    // esqueci minha senha: código enviado ao e-mail
    Route::get('/esqueci-senha', [RedefinirSenhaController::class, 'pedirCodigo'])->name('senha.esqueci');
    Route::post('/esqueci-senha', [RedefinirSenhaController::class, 'enviarCodigo'])->name('senha.enviar');
    Route::get('/redefinir-senha', [RedefinirSenhaController::class, 'informarCodigo'])->name('senha.redefinir');
    Route::post('/redefinir-senha', [RedefinirSenhaController::class, 'redefinir'])->name('senha.atualizar')->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Meu perfil: disponível para todos os usuários logados
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.editar');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::patch('/perfil/senha', [PerfilController::class, 'alterarSenha'])->name('perfil.senha');

    /* ---------------- Pedidos (não são excluídos: RN023) ---------------- */
    Route::resource('pedidos', PedidoController::class)->except('destroy')->middleware('permissao:pedidos');

    /* ---------------- Amostras ---------------- */
    Route::resource('amostras', AmostraController::class)->middleware('permissao:amostras');
    Route::post('/amostras/{amostra}/aprovar', [AmostraStatusController::class, 'aprovar'])->name('amostras.aprovar')->middleware('permissao:amostras,editar');
    Route::post('/amostras/{amostra}/reprovar', [AmostraStatusController::class, 'reprovar'])->name('amostras.reprovar')->middleware('permissao:amostras,editar');
    Route::post('/amostras/{amostra}/cancelar', [AmostraStatusController::class, 'cancelar'])->name('amostras.cancelar')->middleware('permissao:amostras,cancelar');

    /* ---------------- Solicitações de prazo ---------------- */
    Route::get('/solicitacoes-prazo', [SolicitacaoPrazoController::class, 'index'])->name('solicitacoes-prazo.index')->middleware('permissao:solicitacoes_prazo,visualizar');
    Route::post('/amostras/{amostra}/solicitacoes-prazo', [SolicitacaoPrazoController::class, 'store'])->name('solicitacoes-prazo.store')->middleware('permissao:solicitacoes_prazo,criar');
    Route::put('/solicitacoes-prazo/{solicitacao}', [SolicitacaoPrazoController::class, 'update'])->name('solicitacoes-prazo.update')->middleware('permissao:solicitacoes_prazo,aprovar');
    Route::delete('/solicitacoes-prazo/{solicitacao}', [SolicitacaoPrazoController::class, 'destroy'])->name('solicitacoes-prazo.destroy')->middleware('permissao:solicitacoes_prazo,excluir');

    /* ---------------- Clientes e produtos (importação antes do resource) ---------------- */
    foreach (['clientes', 'produtos'] as $modulo) {
        Route::get("/{$modulo}/importar", [ImportacaoController::class, 'create'])->name("{$modulo}.importar")->defaults('tipo', $modulo)->middleware("permissao:{$modulo},importar");
        Route::post("/{$modulo}/importar", [ImportacaoController::class, 'store'])->name("{$modulo}.importar.store")->defaults('tipo', $modulo)->middleware("permissao:{$modulo},importar");
        Route::get("/{$modulo}/importar/modelo", [ImportacaoController::class, 'modelo'])->name("{$modulo}.importar.modelo")->defaults('tipo', $modulo)->middleware("permissao:{$modulo},importar");
    }

    Route::resource('clientes', ClienteController::class)->middleware('permissao:clientes');
    Route::patch('/clientes/{cliente}/status', [ClienteController::class, 'alterarStatus'])->name('clientes.status')->middleware('permissao:clientes,editar');

    Route::resource('produtos', ProdutoController::class)->middleware('permissao:produtos');
    Route::patch('/produtos/{produto}/status', [ProdutoController::class, 'alterarStatus'])->name('produtos.status')->middleware('permissao:produtos,editar');

    /* ---------------- Estabelecimentos ---------------- */
    Route::resource('estabelecimentos', EstabelecimentoController::class)->middleware('permissao:estabelecimentos');
    Route::patch('/estabelecimentos/{estabelecimento}/status', [EstabelecimentoController::class, 'alterarStatus'])->name('estabelecimentos.status')->middleware('permissao:estabelecimentos,editar');

    /* ---------------- Usuários ---------------- */
    Route::resource('usuarios', UserController::class)->middleware('permissao:usuarios');
    Route::patch('/usuarios/{usuario}/status', [UserController::class, 'alterarStatus'])->name('usuarios.status')->middleware('permissao:usuarios,editar');
    Route::patch('/usuarios/{usuario}/senha', [UserController::class, 'alterarSenha'])->name('usuarios.senha')->middleware('permissao:usuarios,editar');

    /* ---------------- Permissões (grupos) e auditoria ---------------- */
    Route::resource('grupos', GrupoController::class)->middleware('permissao:permissoes');
    Route::resource('logs', LogController::class)->only(['index', 'show'])->middleware('permissao:logs');
});
