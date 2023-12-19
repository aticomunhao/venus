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
use App\Http\Controllers\SalaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\MediumController;
use LaravelLegends\PtBrValidator\Rules\FormatoCpf;
use App\Http\Controllers\ReuniaoMediunicaController;
use App\Http\Controllers\RecepcaoIntegradaController;



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

// Rotas da Recepção Atendimento Fraterno DAO:
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
Route::get('/gerenciar-atendente-dia', [GerenciarAtendimentoController::class, 'atendente_dia'])->name('atedia');
Route::get('/definir-sala-atendente', [GerenciarAtendimentoController::class, 'definir_sala'])->name('atesal');
Route::post('/gravar-escolha/{ida}', [GerenciarAtendimentoController::class, 'gravar_sala'])->name('ategrv');

//Rotas do Atendimento Fraterno:
Route::get('/atendendo', [AtendimentoFraternoController::class, 'index'])->name('afidex');
Route::get('/historico/{idat}/{idas}', [AtendimentoFraternoController::class, 'history'])->name('afihis');
Route::get('/fim-analise/{idat}', [AtendimentoFraternoController::class, 'fimanalise'])->name('afifna');
Route::get('/iniciar-atendimento/{idat}', [AtendimentoFraternoController::class, 'inicio'])->name('afiini');
Route::get('/gerar-enc_entre/{idat}', [AtendimentoFraternoController::class, 'enc_entre'])->name('afiene');
Route::get('/gerar-enc_trata/{idat}', [AtendimentoFraternoController::class, 'enc_trata'])->name('afient');
Route::get('/meus-atendimentos', [AtendimentoFraternoController::class, 'meus_atendimentos'])->name('afimeu');
Route::get('/tratar/{idat}', [AtendimentoFraternoController::class, 'tratar'])->name('afitra');
Route::post('/tratamentos/{idat}', [AtendimentoFraternoController::class, 'enc_trat'])->name('afitra');
Route::get('/entrevistar/{idat}', [AtendimentoFraternoController::class, 'entrevistar'])->name('afitent');
Route::post('/entrevistas/{idat}', [AtendimentoFraternoController::class, 'enc_entre'])->name('afiete');
Route::get('/temas/{idat}', [AtendimentoFraternoController::class, 'pre_tema'])->name('afi');
Route::post('/tematicas/{idat}', [AtendimentoFraternoController::class, 'tematica'])->name('afitem');
Route::get('/atender', [AtendimentoFraternoController::class, 'atende_agora'])->name('afiini');
Route::get('/final/{idat}', [AtendimentoFraternoController::class, 'final'])->name('afifin');
Route::post('/finalizar/{idat}', [AtendimentoFraternoController::class, 'finaliza'])->name('afifim');


// Atendentes
Route::get('/gerenciar-atendentes', [AtendenteController::class, 'index'])->name('ateger'); // Exibe todos atendentes
Route::get('/gerenciar-atendende/{id}', [AtendenteController::class, 'show'])->name('gerenciar-atendente_show'); // Exibir detalhes?
Route::get('/novo-atendente', [AtendenteController::class, 'create'])->name('novo-atendente'); // Select de pessoas -> atendentes.
Route::post('/inserir-atendente', [AtendenteController::class, 'RequestTest'])->name('inserir_atendente');

// Grupos
Route::get('/gerenciar-grupos', [GrupoController::class, 'index'])->name('nomes');
Route::get('criar-grupos', [GrupoController::class,'create'])->name('');
Route::post('incluir-grupos', [GrupoController::class,'store']);
Route::get('/editar-grupos/{id}', [GrupoController::class,'edit']);
Route::post('/atualizar-grupos/{id}', [GrupoController::class,'update'])->name('');
Route::any('/deletar-grupos/{id}', [GrupoController::class,'destroy']);
Route::get('/visualizar-grupos/{id}', [GrupoController::class, 'show'])->name('');

// Atendentes Misc/Testes
Route::get('/visualizar-atendendes/{id}', [AtendenteController::class, 'show_detalhes_atendente'])->name('show_atendente');

// Debugger
Route::get('/tester', [TesteController::class, 'index'])->name('tester');

// Pessoas
Route::get('/gerenciar-pessoas', [PessoaController::class, 'index'])->name('pesdex');
Route::get('/dados-pessoa', [PessoaController::class, 'store'])->name('pesdap');
Route::post('/criar-pessoa', [PessoaController::class, 'create'])->name('pescre');
Route::get('/excluir-pessoa', [PessoaController::class, 'destroy'])->name('pesdes');
Route::get('/editar-pessoa/{idp}', [PessoaController::class, 'edit'])->name('pesedt');
Route::post('/executa-edicao/{idp}', [PessoaController::class, 'update'])->name('pesexe');

/*
|--------------------------------------------------------------------------
| David Routes
|--------------------------------------------------------------------------
*/

// Fato
//Route::post('/Gerenciarm', [PessoaController::class, 'create'])->ID('pesdex');
Route::get('/gerenciar-fatos', [FatosController::class, 'index'])->name('descricao');
Route::get('/editar-fatos/{id}', [FatosController::class, 'edit'])->name('');
Route::post('/atualizar-fatos/{id}', [FatosController::class, 'update'])->name('');
Route::any('/incluir-fatos', [FatosController::class, 'incluir']);
Route::get('/criar-fatos', [FatosController::class, 'criar'])->name('');
Route::any('/deletar-fatos/{id}', [FatosController::class, 'destroy'])->name('');

//Salas
Route::get('/gerenciar-salas', [SalaController::class, 'index'])->name('salas');
Route::get('/editar-salas/{id}', [SalaController::class, 'edit'])->name('');
Route::post('/atualizar-salas/{id}', [SalaController::class, 'update'])->name('');
Route::get('/criar-salas', [SalaController::class, 'criar'])->name('');
Route::post('/incluir-salas', [SalaController::class, 'store']);
Route::any('/deletar-salas/{id}', [SalaController::class, 'destroy'])->name('');
Route::get('/visualizar-salas/{id}', [SalaController::class, 'show'])->name('');

//Méaiuns
Route::get('/gerenciar-mediuns', [MediumController::class, 'index'])->name('lista');
Route::get('/editar-mediuns/{id}', [MediumController::class, 'edit'])->name('');
Route::post('/atualizar-mediuns/{id}', [MediumController::class, 'update'])->name('');
Route::get('/criar-mediuns', [MediumController::class, 'create'])->name('');
Route::post('/incluir-mediuns', [MediumController::class, 'store']);
Route::any('/deletar-mediuns/{id}', [MediumController::class, 'destroy'])->name('');
Route::get('/visualizar-mediuns/{id}', [MediumController::class, 'show'])->name('');


/*
|--------------------------------------------------------------------------
| Moisés Routes
|--------------------------------------------------------------------------
*/

//RECEPÇÃO INTEGRADA DA DAO
Route::get('/gerenciar-recepcao', [RecepcaoIntegradaController::class, 'index'])->name('recdex');
Route::get('/agendar/{ide}/{idtt}', [RecepcaoIntegradaController::class, 'agenda'])->name('recage');
Route::post('/faltar/{ide}', [RecepcaoIntegradaController::class, 'faltas'])->name('recfal');
Route::get('/visualizar', [RecepcaoIntegradaController::class, 'visualizas'])->name('recvis');
Route::get('/inativar', [RecepcaoIntegradaController::class, 'inativas'])->name('recina');
Route::post('i', [RecepcaoIntegradaController::class, ''])->name('');
Route::get('/j', [RecepcaoIntegradaController::class, ''])->name('');
Route::put('/k', [RecepcaoIntegradaController::class, ''])->name('');

//REUNIÃO MEDIÚNICA
Route::get('/gerenciar-reunioes', [ReuniaoMediunicaController::class, 'index'])->name('remdex');
Route::get('/criar-reuniao', [ReuniaoMediunicaController::class, 'create'])->name('remcre');
Route::post('/nova-reuniao', [ReuniaoMediunicaController::class, 'store'])->name('remore');






