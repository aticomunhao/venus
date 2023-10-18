<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\AtendenteController;
use App\Http\Controllers\TesteController;
use App\Http\Controllers\GerenciarAtendimentoController;
use App\Http\Controllers\AtendimentoFraternoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\FatosController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
// Validadores
use LaravelLegends\PtBrValidator\Rules\FormatoCpf;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Auth::routes();

Route::get('/logout', 'LexaAdmin@logout');

Route::get('/', [LoginController::class, 'index']);
Route::any('/login/valida', [LoginController::class, 'validaUserLogado'])->name('home.post');
Route::any('/login/home', [LoginController::class, 'valida']);

Route::name('usuario')->middleware('validaUsuario')->group(function () {
    Route::get('gerenciar-usuario', [UsuarioController::class, 'index']);
    Route::get('usuario-incluir', [UsuarioController::class, 'create']);
    Route::get('cadastrar-usuarios/configurar/{id}', [UsuarioController::class, 'configurarUsuario']);
    Route::post('/cad-usuario/inserir', [UsuarioController::class, 'store']);
    Route::get('/usuario/excluir/{id}', [UsuarioController::class, 'destroy']);
    Route::get('/usuario/alterar/{id}', [UsuarioController::class, 'edit']);
    Route::put('usuario-atualizar/{id}', [UsuarioController::class, 'update']);
    Route::get('/usuario/gerar-Senha/{id}', [UsuarioController::class, 'gerarSenha']);

  });

Route::post('/usuario/gravaSenha', [UsuarioController::class, 'gravaSenha']);
Route::get('/usuario/alterar-senha', [UsuarioController::class, 'alteraSenha']);

// Pessoas
Route::get('/gerenciar-pessoas', [PessoaController::class, 'index'])->name('pesdex');
Route::post('/criar-pessoa', [PessoaController::class, 'create'])->name('pescre');
Route::get('/excluir-pessoa/{idp}', [PessoaController::class, 'destroy'])->name('pesdes');




Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rotas da Recepão DAO:
Route::get('/gerenciar-atendimentos', [GerenciarAtendimentoController::class, 'index'])->name('atedex');
Route::get('/criar-atendimento', [GerenciarAtendimentoController::class, 'create'])->name('atecre');
Route::post('/novo-atendimento', [GerenciarAtendimentoController::class, 'store'])->name('atetore');
Route::get('/cancelar-atendimento/{ida}', [GerenciarAtendimentoController::class, 'cancelar'])->name('atecan');
Route::get('/sobe-status/{ida}', [GerenciarAtendimentoController::class, 'sobeStatus'])->name('atess');
Route::get('/desce-status/{ida}', [GerenciarAtendimentoController::class, 'desceStatus'])->name('ateds');
Route::get('/editar-atendimento/{ida}', [GerenciarAtendimentoController::class, 'edit'])->name('ateedi');
Route::post('/grava-atualizacao/{ida}', [GerenciarAtendimentoController::class, 'altera'])->name('atealt');
Route::get('/visualizar-atendimentos/{idas}', [GerenciarAtendimentoController::class, 'visual'])->name('atevis');
Route::put('/atendente-atualizar/{ida}', [GerenciarAtendimentoController::class, 'salvaatend'])->name('salate');

//Rotas do Atendimento Fraterno:
Route::get('/atendendo', [AtendimentoFraternoController::class, 'index'])->name('afidex');



/*
|--------------------------------------------------------------------------
| Alexandre Routes
|--------------------------------------------------------------------------
*/

// Atendentes
Route::get('/gerenciar-atendentes', [AtendenteController::class, 'index'])->name('gerenciar-atendentes'); // Exibe todos atendentes
Route::get('/gerenciar-atendende/{id}', [AtendenteController::class, 'show'])->name('gerenciar-atendente_show'); // Exibir detalhes?
Route::get('/novo-atendente', [AtendenteController::class, 'create'])->name('novo-atendente'); // Select de pessoas -> atendentes.
Route::post('/inserir-atendente', [AtendenteController::class, 'RequestTest'])->name('inserir_atendente');

// Atendentes Misc/Testes
Route::get('/visualizar-atendendes/{id}', [AtendenteController::class, 'show_detalhes_atendente'])->name('show_atendente');

// Debugger
Route::get('/tester', [TesteController::class, 'index'])->name('tester');



// Fato
//Route::post('/Gerenciarm', [PessoaController::class, 'create'])->ID('pesdex');
Route::get('/gerenciar-fatos', [FatosController::class, 'index'])->name('');
Route::get('/editar-fatos/{id}', [FatosController::class, 'edit'])->name('');
Route::get('/incluir-atendimento{id}', [FatosController::class, 'incluir'])->name('');




