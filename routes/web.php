<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Rota Padrão:
Route::get('/', function () {
    return view('welcome');
});


// Rota e dados gerenciados pelos controllers:
Route::get('/gerenciar-atendimentos', [GerenciarAtendimentoController::class, 'index'])->name('atedex');
Route::get('/criar-atendimento', [GerenciarAtendimentoController::class, 'create'])->name('atecre');
Route::post('/novo-atendimento', [GerenciarAtendimentoController::class, 'store'])->name('atetore');
Route::get('/cancelar-atendimento/{ida}', [GerenciarAtendimentoController::class, 'cancelar'])->name('atecan');
Route::get('/sobe-status/{ida}', [GerenciarAtendimentoController::class, 'sobeStatus'])->name('atess');
Route::get('/desce-status/{ida}', [GerenciarAtendimentoController::class, 'desceStatus'])->name('ateds');
Route::get('/editar-atendimento/{ida}', [GerenciarAtendimentoController::class, 'edit'])->name('ateedi');
Route::post('/grava-atualizacao/{ida}', [GerenciarAtendimentoController::class, 'altera'])->name('atealt');
Route::get('/visualizar-atendimentos/{idas}', [GerenciarAtendimentoController::class, 'visual'])->name('atevis');


// Atendentes
Route::get('/gerenciar-atendentes', [AtendenteController::class, 'index']);
Route::get('/novo-atendente', [AtendenteController::class, 'novo']);
Route::post('/tester', [AtendenteController::class, 'tester']);


Route::get('/visualizar-atendendes/{id}', [AtendenteController::class, 'show_detalhes_atendente'])->name('show_atendente');
// Route::post('/incluir-atendente', 'AtendenteController@store')->name('incluir-atendente');
// Route::post('/incluir-atendente', [EventController::class, 'store'])->name('incluir-atendente');

// Route::post('/search', [SearchController::class, 'search'])->name('search');
// Route::get('/tester', [AtendenteController::class, 'tester']);





// Pessoas
Route::get('/gerenciar-pessoas', [PessoaController::class, 'index']);
Route::get('/criar-pessoa', [PessoaController::class, 'create'])->name('pescre');
