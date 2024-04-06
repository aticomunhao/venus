<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;






// Controllers
use App\Http\Controllers\AtendenteController;
use App\Http\Controllers\AtendimentoApoioController;
use App\Http\Controllers\AtendentePlantonistaController;
use App\Http\Controllers\TesteController;
use App\Http\Controllers\GerenciarAtendimentoController;
use App\Http\Controllers\AtendimentoFraternoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\FatosController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\MembroController;
use LaravelLegends\PtBrValidator\Rules\FormatoCpf;
use App\Http\Controllers\ReuniaoMediunicaController;
use App\Http\Controllers\GerenciarEncaminhamentoController;
use App\Http\Controllers\GerenciarTratamentosController;
use App\Http\Controllers\GerenciarTratamentoPTIController;
use App\Http\Controllers\GerenciarEntrevistaController;
use App\Http\Controllers\GerenciarEntrevistaevangelhoController;
use App\Http\Controllers\GerenciarDirigentesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/



Auth::routes();
Route::get('/logout', 'LexaAdmin@logout');

Route::fallback(function() {
    return view('tratamento-erro.erro-404');});

Route::get('/email/remessa-email', 'RecuperaSenhaController@index');
Route::post('/email/remessa-email', 'RecuperaSenhaController@validar');


Route::get('/', [LoginController::class, 'index']);
Route::any('/login/home', [LoginController::class, 'valida']);


//Route::name('usuario')->middleware('validaUsuario')->group(function () {
    Route::any('/login/valida', [LoginController::class, 'validaUserLogado'])->name('home.post');
    Route::get('/gerenciar-usuario', [UsuarioController::class, 'index']);

    Route::get('/usuario-incluir', [UsuarioController::class, 'create']);
    Route::get('/cadastrar-usuarios/configurar/{id}', [UsuarioController::class, 'configurarUsuario']);
    Route::post('/cad-usuario/inserir', [UsuarioController::class, 'store']);
    Route::get('/usuario/excluir/{id}', [UsuarioController::class, 'destroy']);
    Route::get('/usuario/alterar/{id}', [UsuarioController::class, 'edit']);
    Route::put('usuario-atualizar/{id}', [UsuarioController::class, 'update']);
    Route::any('/usuario/gerar-Senha/{id}', [UsuarioController::class, 'gerarSenha']);

//});

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
Route::get('/gerenciar-atendente-dia', [GerenciarAtendimentoController::class, 'atendente_dia'])->name('afidia');
Route::get('/definir-sala-atendente', [GerenciarAtendimentoController::class, 'definir_sala'])->name('afisal');
Route::get('/editar-atendente-dia/{idatd}', [GerenciarAtendimentoController::class, 'editar_afi'])->name('afiedt');
Route::post('/altera-atendente-dia/{idatd}', [GerenciarAtendimentoController::class, 'update_afi'])->name('afiupt');
Route::post('/incluir-afi-sala/{idat}/{idg}', [GerenciarAtendimentoController::class, 'salva_afi'])->name('afidef');
Route::post('/gravar-escolha/{idatd}', [GerenciarAtendimentoController::class, 'gravar_sala'])->name('ategrv');
Route::any('/excluir-atendente/{idatd}/{idad}', [GerenciarAtendimentoController::class, 'delete'])->name('atedel');

//Rotas do Atendimento Fraterno:
Route::get('/atendendo', [AtendimentoFraternoController::class, 'index'])->name('afidex');
Route::get('/historico/{idat}/{idas}', [AtendimentoFraternoController::class, 'history'])->name('afihis');
Route::get('/fim-analise/{idat}', [AtendimentoFraternoController::class, 'fimanalise'])->name('afifna');
Route::get('/iniciar-atendimento/{idat}', [AtendimentoFraternoController::class, 'inicio'])->name('afiini');
Route::get('/gerar-enc_entre/{idat}', [AtendimentoFraternoController::class, 'enc_entre'])->name('afiene');
Route::get('/gerar-enc_trata/{idat}', [AtendimentoFraternoController::class, 'enc_trata'])->name('afient');
Route::get('/meus-atendimentos', [AtendimentoFraternoController::class, 'meus_atendimentos'])->name('afimeu');
Route::get('/tratar/{idat}/{idas}', [AtendimentoFraternoController::class, 'tratar'])->name('afitra');
Route::post('/tratamentos/{idat}/{idas}', [AtendimentoFraternoController::class, 'enc_trat'])->name('afitra');
Route::get('/entrevistar/{idat}/{idas}', [AtendimentoFraternoController::class, 'entrevistar'])->name('afitent');
Route::post('/entrevistas/{idat}', [AtendimentoFraternoController::class, 'enc_entre'])->name('afiete');
Route::get('/temas/{idat}', [AtendimentoFraternoController::class, 'pre_tema'])->name('afi');
Route::post('/tematicas/{idat}', [AtendimentoFraternoController::class, 'tematica'])->name('afitem');
Route::get('/atender', [AtendimentoFraternoController::class, 'atende_agora'])->name('afiini');
Route::get('/final/{idat}', [AtendimentoFraternoController::class, 'final'])->name('afifin');
Route::post('/finalizar/{idat}', [AtendimentoFraternoController::class, 'finaliza'])->name('afifim');




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

//Entrevista-evangelho
Route::get('/gerenciar-evangelho', [GerenciarEntrevistaevangelhoController::class, 'index'])->name('start');
Route::get('/criar-evangelho/{ide}', [GerenciarEntrevistaevangelhoController::class, 'create'])->name('criar');
Route::post('/incluir-evangelho/{id}', [GerenciarEntrevistaevangelhoController::class, 'store'])->name('agendar');
Route::get('/editar-evangelho/{id}', [GerenciarEntrevistaevangelhoController::class, 'edit'])->name('');
Route::post('/atualizar-evangelho/{id}', [GerenciarEntrevistaevangelhoController::class, 'update'])->name('');
Route::get('/visualizar-evangelho/{id}', [GerenciarEntrevistaevangelhoController::class, 'show'])->name('');
Route::any('/excluir-evangelho/{id}', [GerenciarEntrevistaevangelhoController::class, 'destroy'])->name('');
Route::any('/finalizar-evangelho/{id}', [GerenciarEntrevistaevangelhoController::class, 'finalizar'])->name('');
Route::any('/inativar-evangelho/{id}', [GerenciarEntrevistaevangelhoController::class, 'inativar'])->name('');



//Entrevista

Route::get('/gerenciar-entrevistas', [GerenciarEntrevistaController::class, 'index'])->name('gerenciamento');
Route::get('/criar-entrevista/{id}', [GerenciarEntrevistaController::class, 'create'])->name('criar-entrevista');
Route::post('/incluir-entrevista/{id}', [GerenciarEntrevistaController::class, 'store'])->name('incluir.entrevista');
Route::get('/agendar-entrevistador/{id}', [GerenciarEntrevistaController::class, 'criar'])->name('agendar-entrevistador');
Route::post('/incluir-entrevistador/{id}', [GerenciarEntrevistaController::class, 'incluir'])->name('');
Route::get('/editar-entrevista/{id}', [GerenciarEntrevistaController::class, 'edit'])->name('');
Route::post('/atualizar-entrevista/{id}', [GerenciarEntrevistaController::class, 'update'])->name('');
Route::get('/visualizar-entrevista/{id}', [GerenciarEntrevistaController::class, 'show'])->name('');
Route::any('/finalizar-entrevista/{id}', [GerenciarEntrevistaController::class, 'finalizar'])->name('finalizar.entrevista');
Route::any('/inativar-entrevista/{id}', [GerenciarEntrevistaController::class, 'inativar'])->name('');





  

// Grupos
Route::get('/gerenciar-grupos', [GrupoController::class, 'index'])->name('nomes');
Route::get('criar-grupos', [GrupoController::class,'create'])->name('');
Route::post('incluir-grupos', [GrupoController::class,'store']);
Route::get('/editar-grupos/{id}', [GrupoController::class,'edit']);
Route::post('/atualizar-grupos/{id}', [GrupoController::class,'update'])->name('');
Route::any('/deletar-grupos/{id}', [GrupoController::class,'destroy']);
Route::get('/visualizar-grupos/{id}', [GrupoController::class, 'show'])->name('');


// Fato

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

//Médiuns
Route::get('/gerenciar-membro', [MembroController::class, 'index'])->name('lista');
Route::get('/editar-membro/{id}', [MembroController::class, 'edit'])->name('');
Route::post('/atualizar-membro/{id}', [MembroController::class, 'update'])->name('');
Route::get('/criar-membro', [MembroController::class, 'create'])->name('');
Route::post('/incluir-membro', [MembroController::class, 'store'])->name('membro.store');
Route::any('/deletar-membro/{id}', [MembroController::class, 'destroy'])->name('');
Route::get('/visualizar-membro/{id}', [MembroController::class, 'show'])->name('');


/*
|--------------------------------------------------------------------------
| Moisés Routes
|--------------------------------------------------------------------------
*/

//RECEPÇÃO INTEGRADA DA DAO
Route::get('/gerenciar-encaminhamentos', [GerenciarEncaminhamentoController::class, 'index'])->name('gecdex');
Route::get('/agendar/{ide}/{idtt}', [GerenciarEncaminhamentoController::class, 'agenda'])->name('gecage');
Route::get('/agendar-tratamento/{ide}', [GerenciarEncaminhamentoController::class, 'tratamento'])->name('gtctra');
Route::post('incluir-tratamento/{idtr}', [GerenciarEncaminhamentoController::class, 'tratar'])->name('gtctrt');
Route::get('/visualizar-enc/{ide}', [GerenciarEncaminhamentoController::class, 'visualizar'])->name('gecvis');
Route::post('/inativar/{ide}', [GerenciarEncaminhamentoController::class, 'inative'])->name('gecina');


Route::get('/gerenciar-tratamentos', [GerenciarTratamentosController::class, 'index'])->name('gtcdex');
Route::get('/visualizar-trat/{idtr}', [GerenciarTratamentosController::class, 'visualizar'])->name('gecvis');
Route::post('/presenca/{idtr}', [GerenciarTratamentosController::class, 'presenca'])->name('gtcpre');
Route::get('/registrar-falta', [GerenciarTratamentosController::class, 'falta'])->name('gtcfal');



Route::get('/gerenciar-tratamentos-pti', [GerenciarTratamentoPTIController::class, 'index'])->name('gtcpdx');


Route::put('/k', [GerenciarTratamentosController::class, ''])->name('');

//REUNIÃO MEDIÚNICA
Route::get('/gerenciar-reunioes', [ReuniaoMediunicaController::class, 'index'])->name('remdex');
Route::get('/criar-reuniao', [ReuniaoMediunicaController::class, 'create'])->name('remcre');
Route::post('/nova-reuniao', [ReuniaoMediunicaController::class, 'store'])->name('remore');
Route::get('/editar-reuniao/{id}', [ReuniaoMediunicaController::class, 'edit']);
Route::any('/atualizar-reuniao/{id}', [ReuniaoMediunicaController::class, 'update']);
Route::any('/excluir-reuniao/{id}', [ReuniaoMediunicaController::class, 'destroy']);
Route::any('/visualizar-reuniao/{id}', [ReuniaoMediunicaController::class, 'show']);



/*
|--------------------------------------------------------------------------
| Nathan Routes
|--------------------------------------------------------------------------
*/

//Atendentes de Apoio
Route::get('/gerenciar-atendentes-apoio', [AtendimentoApoioController::class, 'index'])->name('indexAtendenteApoio');
Route::get('/incluir-atendentes-apoio', [AtendimentoApoioController::class, 'create']);
Route::any('/armazenar-atendentes-apoio', [AtendimentoApoioController::class, 'store']);
Route::any('/visualizar-atendentes-apoio/{id}', [AtendimentoApoioController::class, 'show']);
Route::any('/editar-atendentes-apoio/{id}', [AtendimentoApoioController::class, 'edit']);
Route::any('/atualizar-atendentes-apoio/{id}', [AtendimentoApoioController::class, 'update']);

//Atendentes Plantonistas
Route::get('/gerenciar-atendentes-plantonistas', [AtendentePlantonistaController::class, 'index'])->name('indexAtendentePlantonista');
Route::get('/incluir-atendentes-plantonistas', [AtendentePlantonistaController::class, 'create']);
Route::any('/armazenar-atendentes-plantonistas', [AtendentePlantonistaController::class, 'store']);
Route::any('/visualizar-atendentes-plantonistas/{id}', [AtendentePlantonistaController::class, 'show']);
Route::any('/editar-atendentes-plantonistas/{id}', [AtendentePlantonistaController::class, 'edit']);
Route::any('/atualizar-atendentes-plantonistas/{id}', [AtendentePlantonistaController::class, 'update']);


//Dirigentes

Route::get('/gerenciar-dirigentes', [GerenciarDirigentesController::class, 'index'])->name('indexDirigente');
Route::get('/incluir-dirigentes', [GerenciarDirigentesController::class, 'create']);
Route::any('/armazenar-dirigentes', [GerenciarDirigentesController::class, 'store']);
Route::any('/visualizar-dirigentes/{id}', [GerenciarDirigentesController::class, 'show']);
Route::any('/editar-dirigentes/{id}', [GerenciarDirigentesController::class, 'edit']);
Route::any('/atualizar-dirigentes/{id}', [GerenciarDirigentesController::class, 'update']);
Route::any('/excluir-dirigentes/{id}', [GerenciarDirigentesController::class, 'destroy']);















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
    Route::get('/gerenciar-atendente-dia', [GerenciarAtendimentoController::class, 'atendente_dia'])->name('afidia');
    Route::get('/definir-sala-atendente', [GerenciarAtendimentoController::class, 'definir_sala'])->name('afisal');
    Route::get('/editar-atendente-dia/{idatd}', [GerenciarAtendimentoController::class, 'editar_afi'])->name('afiedt');
    Route::post('/altera-atendente-dia/{idatd}', [GerenciarAtendimentoController::class, 'update_afi'])->name('afiupt');
    Route::post('/incluir-afi-sala/{idat}/{idg}', [GerenciarAtendimentoController::class, 'salva_afi'])->name('afidef');
    Route::post('/gravar-escolha/{idatd}', [GerenciarAtendimentoController::class, 'gravar_sala'])->name('ategrv');
    Route::any('/excluir-atendente/{idatd}/{idad}', [GerenciarAtendimentoController::class, 'delete'])->name('atedel');

    //Rotas do Atendimento Fraterno:
    Route::get('/atendendo', [AtendimentoFraternoController::class, 'index'])->name('afidex');
    Route::get('/historico/{idat}/{idas}', [AtendimentoFraternoController::class, 'history'])->name('afihis');
    Route::get('/fim-analise/{idat}', [AtendimentoFraternoController::class, 'fimanalise'])->name('afifna');
    Route::get('/iniciar-atendimento/{idat}', [AtendimentoFraternoController::class, 'inicio'])->name('afiini');
    Route::get('/gerar-enc_entre/{idat}', [AtendimentoFraternoController::class, 'enc_entre'])->name('afiene');
    Route::get('/gerar-enc_trata/{idat}', [AtendimentoFraternoController::class, 'enc_trata'])->name('afient');
    Route::get('/meus-atendimentos', [AtendimentoFraternoController::class, 'meus_atendimentos'])->name('afimeu');
    Route::get('/tratar/{idat}/{idas}', [AtendimentoFraternoController::class, 'tratar'])->name('afitra');
    Route::post('/tratamentos/{idat}/{idas}', [AtendimentoFraternoController::class, 'enc_trat'])->name('afitra');
    Route::get('/entrevistar/{idat}/{idas}', [AtendimentoFraternoController::class, 'entrevistar'])->name('afitent');
    Route::post('/entrevistas/{idat}', [AtendimentoFraternoController::class, 'enc_entre'])->name('afiete');
    Route::get('/temas/{idat}', [AtendimentoFraternoController::class, 'pre_tema'])->name('afi');
    Route::post('/tematicas/{idat}', [AtendimentoFraternoController::class, 'tematica'])->name('afitem');
    Route::get('/atender', [AtendimentoFraternoController::class, 'atende_agora'])->name('afiini');
    Route::get('/final/{idat}', [AtendimentoFraternoController::class, 'final'])->name('afifin');
    Route::post('/finalizar/{idat}', [AtendimentoFraternoController::class, 'finaliza'])->name('afifim');




