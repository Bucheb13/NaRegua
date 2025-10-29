<?php

use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\BarbeariaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (usuário logado)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Buscar usuários via AJAX
    Route::get('/usuarios/buscar', [UsuarioController::class, 'buscar'])->name('usuarios.buscar');

    // CRUD completo de usuários (clientes, barbeiros, admin etc.)
    Route::resource('usuarios', UsuarioController::class);

    // CRUD de serviços
    Route::resource('servicos', ServicoController::class);

    // CRUD de produtos
    Route::resource('produtos', ProdutoController::class);

    // CRUD de agendamentos
    Route::resource('agendamentos', AgendamentoController::class);

    Route::post('/agendamentos/{id}/confirmar', [AgendamentoController::class, 'confirmar'])->name('agendamentos.confirmar');
    Route::post('/agendamentos/{id}/cancelar', [AgendamentoController::class, 'cancelar'])->name('agendamentos.cancelar');
    
    Route::patch('/agendamentos/{agendamento}/status', [AgendamentoController::class, 'atualizarStatus'])
    ->name('agendamentos.status'); 

    Route::get('/agendamentos/relatorio/load', [AgendamentoController::class, 'relatorioLoad'])
    ->name('agendamentos.relatorio.load');

    Route::get('/agendamentos/clientes/search', [AgendamentoController::class, 'buscarClientes'])
    ->name('agendamentos.clientes.search');

    // Cadastro de Barbearias (somente admin)
    Route::post('/barbearias', [BarbeariaController::class, 'store'])
    ->name('barbearias.store');
    Route::put('/barbearias/{barbearia}', [BarbeariaController::class, 'update'])
        ->name('barbearias.update');
        Route::delete('/barbearias/{barbearia}', [BarbeariaController::class, 'destroy'])
    ->name('barbearias.destroy');


    // CRUD de vendas
    Route::resource('vendas', VendaController::class);
    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas de autenticação padrão Laravel
require __DIR__ . '/auth.php';
