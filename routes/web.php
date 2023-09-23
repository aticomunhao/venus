<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
//Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'index'])->name('login');


Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



// Rota e dados gerenciados pelos controllers:
Route::get('/gerenciar-atendimentos', [App\Http\Controllers\GerenciarAtendimentoController::class, 'index'])->name('atedex');
Route::get('/criar-atendimento', [App\Http\Controllers\GerenciarAtendimentoController::class, 'create'])->name('atecre');
Route::post('/novo-atendimento', [App\Http\Controllers\GerenciarAtendimentoController::class, 'store'])->name('atetore');
Route::get('/cancelar-atendimento/{ida}', [App\Http\Controllers\GerenciarAtendimentoController::class, 'cancelar'])->name('atecan');
Route::get('/sobe-status/{ida}', [App\Http\Controllers\GerenciarAtendimentoController::class, 'sobeStatus'])->name('atess');
Route::get('/desce-status/{ida}', [App\Http\Controllers\GerenciarAtendimentoController::class, 'desceStatus'])->name('ateds');
Route::get('/editar-atendimento/{ida}', [App\Http\Controllers\GerenciarAtendimentoController::class, 'edit'])->name('ateedi');
Route::post('/grava-atualizacao/{ida}', [App\Http\Controllers\GerenciarAtendimentoController::class, 'altera'])->name('atealt');
Route::get('/visualizar-atendimentos/{idas}', [App\Http\Controllers\GerenciarAtendimentoController::class, 'visual'])->name('atevis');


// Atendentes
Route::get('/gerenciar-atendentes', [App\Http\Controllers\AtendenteController::class, 'index']);
Route::get('/novo-atendente', [App\Http\Controllers\AtendenteController::class, 'novo']);
Route::post('/tester', [App\Http\Controllers\AtendenteController::class, 'tester']);


Route::get('/visualizar-atendendes/{id}', [App\Http\Controllers\AtendenteController::class, 'show_detalhes_atendente'])->name('show_atendente');



// Pessoas
Route::get('/gerenciar-pessoas', [App\Http\Controllers\PessoaController::class, 'index']);
Route::get('/criar-pessoa', [App\Http\Controllers\PessoaController::class, 'create'])->name('pescre');
