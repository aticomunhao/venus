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
use App\Http\Controllers\AtendimentoFraternoEspecificoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\FatosController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\MembroController;
use App\Http\Controllers\MediunidadePessoaController;
use LaravelLegends\PtBrValidator\Rules\FormatoCpf;
use App\Http\Controllers\ReuniaoMediunicaController;
use App\Http\Controllers\GerenciarEncaminhamentoController;
use App\Http\Controllers\GerenciarEncaminhamentoPTIController;
use App\Http\Controllers\GerenciarEncaminhamentoIntegralController;
use App\Http\Controllers\GerenciarTratamentosController;
use App\Http\Controllers\GerenciarEntrevistaController;
use App\Http\Controllers\GerenciarPTIController;
use App\Http\Controllers\GerenciarIntegralController;
use App\Http\Controllers\GerenciarPerfil;
use App\Http\Controllers\GerenciarSetor;
use App\Http\Controllers\GerenciarVersoesController;
use App\Http\Controllers\PresencaController;
use App\Http\Controllers\PresencaDirigenteController;
use App\Http\Controllers\GerenciarVersoesControllerController;
use App\Http\Controllers\RelatoriosController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/





Auth::routes();

Route::fallback(function () {
    return view('tratamento-erro.erro-404');
});

Route::get('/', [LoginController::class, 'index']);
Route::any('/login/home', [LoginController::class, 'valida']);
Route::any('/login/valida', [LoginController::class, 'validaUserLogado'])->name('home.post');
Route::get('/usuario/sessao', [LoginController::class, 'checaSession']);
Route::get('/usuario/alterar-senha', [UsuarioController::class, 'alteraSenha']);
Route::post('/usuario/gravaSenha', [UsuarioController::class, 'gravaSenha']);

//Gerenciar usuários
Route::middleware('rotas:1')->group(function () {
    Route::post('/cad-usuario/inserir', [UsuarioController::class, 'store']);
    Route::get('/cadastrar-usuarios/configurar/{id}', [UsuarioController::class, 'configurarUsuario']);
    Route::get('/gerenciar-usuario', [UsuarioController::class, 'index']);
    Route::get('/usuario/alterar/{id}', [UsuarioController::class, 'edit']);

    Route::any('/usuario/gerar-Senha/{id}', [UsuarioController::class, 'gerarSenha']);
    Route::any('usuario-atualizar/{id}', [UsuarioController::class, 'update']);
    Route::get('/usuario/excluir/{id}', [UsuarioController::class, 'destroy']);
    Route::get('/usuario-incluir', [UsuarioController::class, 'create']);
});

// Gerenciar Pessoas
Route::middleware('rotas:2')->group(function () {
    Route::post('/criar-pessoa', [PessoaController::class, 'create'])->name('pescre');
    Route::get('/dados-pessoa', [PessoaController::class, 'store'])->name('pesdap');
    Route::get('/editar-pessoa/{idp}', [PessoaController::class, 'edit'])->name('pesedt');
    Route::get('/excluir-pessoa/{idp}', [PessoaController::class, 'destroy'])->name('pesdes');
    Route::post('/executa-edicao/{idp}', [PessoaController::class, 'update'])->name('pesexe');
});

// Visualizar Pessoas
Route::middleware('rotas:3')->group(function () {
    Route::get('/gerenciar-pessoas', [PessoaController::class, 'index'])->name('pesdex');
    Route::get('/visualizar-pessoa/{idp}', [PessoaController::class, 'show'])->name('pesedt');
});

// Gerenciar Atendente Dia
Route::middleware('rotas:4')->group(function () {
    Route::post('/altera-atendente-dia/{idatd}', [GerenciarAtendimentoController::class, 'update_afi'])->name('afiupt');
    Route::get('/definir-sala-atendente', [GerenciarAtendimentoController::class, 'definir_sala'])->name('afisal');
    Route::get('/editar-atendente-dia/{idatd}', [GerenciarAtendimentoController::class, 'editar_afi'])->name('afiedt');
    Route::any('/excluir-atendente/{idatd}/{idad}', [GerenciarAtendimentoController::class, 'delete'])->name('atedel');
    Route::get('/finalizar-atendente-dia/{idatd}', [GerenciarAtendimentoController::class, 'finaliza_afi'])->name('afiedt');
    Route::get('/gerenciar-atendente-dia', [GerenciarAtendimentoController::class, 'atendente_dia'])->name('afidia');
    Route::post('/gravar-escolha/{idatd}', [GerenciarAtendimentoController::class, 'gravar_sala'])->name('ategrv');
    Route::post('/incluir-afi-sala/{ida}', [GerenciarAtendimentoController::class, 'salva_afi'])->name('afidef');
});

// Recepção Atendimento Fraterno
Route::middleware('rotas:5')->group(function () {
    Route::get('/cancelar-atendimento/{ida}', [GerenciarAtendimentoController::class, 'cancelar'])->name('atecan');
    Route::get('/criar-atendimento', [GerenciarAtendimentoController::class, 'create'])->name('atecre');
    Route::get('/editar-atendimento/{ida}', [GerenciarAtendimentoController::class, 'edit'])->name('ateedi');
    Route::get('/gerenciar-atendimentos', [GerenciarAtendimentoController::class, 'index'])->name('atedex');
    Route::post('/grava-atualizacao/{ida}', [GerenciarAtendimentoController::class, 'altera'])->name('atealt');
    Route::post('/novo-atendimento', [GerenciarAtendimentoController::class, 'store'])->name('atetore');
    Route::get('/visualizar-atendimentos/{idas}', [GerenciarAtendimentoController::class, 'visual'])->name('atevis');
    Route::any('/tabela-atendimentos/{assist}/{cpf}/{status}/{dt_ini}', [GerenciarAtendimentoController::class, 'ajaxAtendimento']);
    Route::any('/ajaxCRUD', [GerenciarAtendimentoController::class, 'ajaxCRUD']);
});

// Atendente Fraterno Individual
Route::middleware('rotas:6')->group(function () {
    Route::get('/atendendo', [AtendimentoFraternoController::class, 'index'])->name('afidex');
    Route::get('/atender', [AtendimentoFraternoController::class, 'atende_agora'])->name('afiini');
    Route::get('/entrevistar/{idat}/{idas}', [AtendimentoFraternoController::class, 'entrevistar'])->name('afitent');
    Route::post('/entrevistas/{idat}', [AtendimentoFraternoController::class, 'enc_entre'])->name('afiete');
    Route::get('/fim-analise/{idat}', [AtendimentoFraternoController::class, 'fimanalise'])->name('afifna');
    Route::get('/final/{idat}', [AtendimentoFraternoController::class, 'final'])->name('afifin');
    Route::any('/finalizar/{idat}', [AtendimentoFraternoController::class, 'finaliza'])->name('afifim');
    Route::get('/gerar-enc_entre/{idat}', [AtendimentoFraternoController::class, 'enc_entre'])->name('afiene');
    Route::get('/gerar-enc_trata/{idat}', [AtendimentoFraternoController::class, 'enc_trata'])->name('afient');
    Route::get('/historico/{idat}/{idas}', [AtendimentoFraternoController::class, 'history'])->name('afihis');
    Route::get('/iniciar-atendimento/{idat}', [AtendimentoFraternoController::class, 'inicio'])->name('afiini');
    Route::get('/meus-atendimentos', [AtendimentoFraternoController::class, 'meus_atendimentos'])->name('afimeu');
    Route::get('/temas/{idat}', [AtendimentoFraternoController::class, 'pre_tema'])->name('afi');
    Route::post('/tematicas/{idat}', [AtendimentoFraternoController::class, 'tematica'])->name('afitem');
    Route::post('/tratamentos/{idat}/{idas}', [AtendimentoFraternoController::class, 'enc_trat'])->name('afitra');
    Route::get('/tratar/{idat}/{idas}', [AtendimentoFraternoController::class, 'tratar'])->name('afitra');
    Route::any('/reset/{idat}', [AtendimentoFraternoController::class, 'reset'])->name('');
});

// Atendente Fraterno Específico
Route::middleware('rotas:7')->group(function () {
    Route::get('/atendendo-afe', [AtendimentoFraternoEspecificoController::class, 'index'])->name('index.atendendoafe');
    Route::get('/historico-afe/{idat}/{idas}', [AtendimentoFraternoEspecificoController::class, 'history'])->name('');
    Route::get('/fim-analise-afe/{idat}', [AtendimentoFraternoEspecificoController::class, 'fimanalise'])->name('');
    Route::get('/iniciar-atendimento-afe/{idat}', [AtendimentoFraternoEspecificoController::class, 'inicio'])->name('');
    Route::get('/gerar-enc_entre-afe/{idat}', [AtendimentoFraternoEspecificoController::class, 'enc_entre'])->name('');
    Route::get('/gerar-enc_trata-afe/{idat}', [AtendimentoFraternoEspecificoController::class, 'enc_trata'])->name('');
    Route::get('/meus-atendimentos-afe', [AtendimentoFraternoEspecificoController::class, 'meus_atendimentos'])->name('');
    Route::get('/tratar-afe/{idat}/{idas}', [AtendimentoFraternoEspecificoController::class, 'tratar'])->name('');
    Route::post('/tratamentos-afe/{idat}/{idas}', [AtendimentoFraternoEspecificoController::class, 'enc_trat'])->name('');
    Route::get('/entrevistar-afe/{idat}/{idas}', [AtendimentoFraternoEspecificoController::class, 'entrevistar'])->name('');
    Route::post('/entrevistas-afe/{idat}', [AtendimentoFraternoEspecificoController::class, 'enc_entre'])->name('');
    Route::get('/temas-afe/{idat}', [AtendimentoFraternoEspecificoController::class, 'pre_tema'])->name('');
    Route::post('/tematicas-afe/{idat}', [AtendimentoFraternoEspecificoController::class, 'tematica'])->name('');
    Route::get('/atender-afe', [AtendimentoFraternoEspecificoController::class, 'atende_agora'])->name('Atender-afe');
    Route::any('/final-afe/{idat}', [AtendimentoFraternoEspecificoController::class, 'final'])->name('');
    Route::post('/finalizar-afe/{idat}', [AtendimentoFraternoEspecificoController::class, 'finaliza'])->name('');
    Route::any('/reset/{idat}', [AtendimentoFraternoEspecificoController::class, 'reset'])->name('');
});

// Gerenciar Presença Entrevista
Route::middleware('rotas:8')->group(function () {
    Route::get('/gerenciar-presenca', [PresencaController::class, 'index'])->name('listas');
    Route::get('/editar-presenca/{id}', [PresencaController::class, 'edit'])->name('');
    Route::post('/atualizar-presenca/{id}', [PresencaController::class, 'update'])->name('');
    Route::any('/incluir-presenca', [PresencaController::class, 'incluir']);
    Route::get('/criar-presenca/{id}', [PresencaController::class, 'criar'])->name('');
    Route::get('/visualizar-presenca/{id}', [PresencaController::class, 'show'])->name('');
    Route::any('/inativar-presenca/{id}', [PresencaController::class, 'destroy'])->name('');
});

// Gerenciar Entrevistas
Route::middleware('rotas:9')->group(function () {
    Route::get('/gerenciar-entrevistas', [GerenciarEntrevistaController::class, 'index'])->name('gerenciamento');
    Route::get('/criar-entrevista/{id}', [GerenciarEntrevistaController::class, 'create'])->name('criar-entrevista');
    Route::post('/incluir-entrevista/{id}', [GerenciarEntrevistaController::class, 'store'])->name('incluir.entrevista');
    Route::get('/agendar-entrevistador/{id}', [GerenciarEntrevistaController::class, 'criar'])->name('agendar-entrevistador');
    Route::post('/incluir-entrevistador/{id}', [GerenciarEntrevistaController::class, 'incluir'])->name('');
    Route::get('/editar-entrevista/{id}', [GerenciarEntrevistaController::class, 'edit'])->name('');
    Route::post('/atualizar-entrevista/{id}', [GerenciarEntrevistaController::class, 'update'])->name('');
    Route::get('/visualizar-entrevista/{id}', [GerenciarEntrevistaController::class, 'show'])->name('');
    Route::any('/finalizar-entrevista/{id}', [GerenciarEntrevistaController::class, 'finalizar'])->name('finalizar.entrevista');
    Route::any('/nao-aceito-entrevista/{id}', [GerenciarEntrevistaController::class, 'fim'])->name('');
    Route::any('/inativar-entrevista/{id}/{tp}', [GerenciarEntrevistaController::class, 'inativar'])->name('');
});

// Gerenciar Grupos
Route::middleware('rotas:10')->group(function () {
    Route::get('/gerenciar-grupos', [GrupoController::class, 'index'])->name('nomes');
    Route::get('criar-grupos', [GrupoController::class, 'create'])->name('');
    Route::post('incluir-grupos', [GrupoController::class, 'store']);
    Route::get('/editar-grupos/{id}', [GrupoController::class, 'edit']);
    Route::post('/atualizar-grupos/{id}', [GrupoController::class, 'update'])->name('');
    Route::any('/deletar-grupos/{id}', [GrupoController::class, 'destroy']);
    Route::get('/visualizar-grupos/{id}', [GrupoController::class, 'show'])->name('');
});

//Gerenciar Fatos
Route::middleware('rotas:11')->group(function () {
    Route::get('/gerenciar-fatos', [FatosController::class, 'index'])->name('descricao');
    Route::get('/editar-fatos/{id}', [FatosController::class, 'edit'])->name('');
    Route::post('/atualizar-fatos/{id}', [FatosController::class, 'update'])->name('');
    Route::any('/incluir-fatos', [FatosController::class, 'incluir']);
    Route::get('/criar-fatos', [FatosController::class, 'criar'])->name('');
    Route::any('/deletar-fatos/{id}', [FatosController::class, 'destroy'])->name('');
});

// Gerenciar Salas
Route::middleware('rotas:12')->group(function () {
    Route::get('/gerenciar-salas', [SalaController::class, 'index'])->name('salas');
    Route::get('/editar-salas/{id}', [SalaController::class, 'edit'])->name('');
    Route::post('/atualizar-salas/{id}', [SalaController::class, 'update'])->name('');
    Route::get('/criar-salas', [SalaController::class, 'criar'])->name('');
    Route::post('/incluir-salas', [SalaController::class, 'store']);
    Route::any('/deletar-salas/{id}', [SalaController::class, 'destroy'])->name('');
    Route::get('/visualizar-salas/{id}', [SalaController::class, 'show'])->name('');
});

// Incluir Membro
Route::middleware('rotas:13')->group(function () {
    Route::get('/criar-membro', [MembroController::class, 'create'])->name('');
    Route::post('/incluir-membro', [MembroController::class, 'store'])->name('membro.store');
});

// Gerenciar Membros
Route::middleware('rotas:14')->group(function () {
    Route::get('/gerenciar-membro/{id}', [MembroController::class, 'index'])->name('lista');
    Route::get('/editar-membro/{idcro}/{id}', [MembroController::class, 'edit'])->name('');
    Route::any('/atualizar-membro/{idcro}/{id}', [MembroController::class, 'update'])->name('');
    Route::post('/deletar-membro/{idcro}/{id}', [MembroController::class, 'destroy'])->name('membro.destroy');
    Route::post('/inativar-membro/{idcro}/{id}', [MembroController::class, 'inactivate'])->name('membro.inactivate');
    Route::get('/visualizar-membro/{idcro}/{id}', [MembroController::class, 'show'])->name('');
    Route::get('/gerenciar-grupos-membro', [MembroController::class, 'grupos'])->name('');
    Route::get('/ferias-reuniao/{id}/{tp}', [MembroController::class, 'ferias']);
});

// Incluir Membro Grupo
Route::middleware('rotas:29')->group(function () {
    Route::get('/criar-membro-grupo/{id}', [MembroController::class, 'createGrupo'])->name('');
    Route::any('/incluir-membro-grupo/{id}', [MembroController::class, 'storeGrupo'])->name('');
});

// Gerenciar Mediunidades
Route::middleware('rotas:15')->group(function () {
    Route::get('/gerenciar-mediunidades', [MediunidadePessoaController::class, 'index'])->name('names');
    Route::get('/editar-mediunidade/{id}', [MediunidadePessoaController::class, 'edit'])->name('');
    Route::post('/atualizar-mediunidade/{id}', [MediunidadePessoaController::class, 'update'])->name('atualizar-mediunidade');
    Route::get('/criar-mediunidade', [MediunidadePessoaController::class, 'create'])->name('');
    Route::post('/incluir-mediunidade', [MediunidadePessoaController::class, 'store'])->name('');
    Route::any('/deletar-mediunidade/{id}', [MediunidadePessoaController::class, 'destroy'])->name('');
    Route::get('/visualizar-mediunidade/{id}', [MediunidadePessoaController::class, 'show'])->name('');
});

// Gerenciar Encaminhamentos
Route::middleware('rotas:16')->group(function () {
    Route::get('/gerenciar-encaminhamentos', [GerenciarEncaminhamentoController::class, 'index'])->name('gecdex');
    Route::get('/agendar/{ide}/{idtt}', [GerenciarEncaminhamentoController::class, 'agenda'])->name('gecage');
    Route::get('/agendar-tratamento/{ide}', [GerenciarEncaminhamentoController::class, 'tratamento'])->name('gtctra');
    Route::post('incluir-tratamento/{idtr}', [GerenciarEncaminhamentoController::class, 'tratar'])->name('gtctrt');
    Route::get('/visualizar-enc/{ide}', [GerenciarEncaminhamentoController::class, 'visualizar'])->name('gecvis');
    Route::post('/inativar/{ide}', [GerenciarEncaminhamentoController::class, 'inative'])->name('gecina');
    Route::get('/alterar-grupo-tratamento/{id}', [GerenciarEncaminhamentoController::class, 'escolherGrupo']);
    Route::get('/escolher-horario/{id}', [GerenciarEncaminhamentoController::class, 'escolherHorario']);
    Route::any('/trocar-grupo-tratamento/{id}', [GerenciarEncaminhamentoController::class, 'trocarGrupo']);
});

// Jobs de Tratamento
Route::middleware('rotas:17')->group(function () {
    Route::any('/job', [GerenciarTratamentosController::class, 'job']);
});

// Gerenciar Tratamentos
Route::middleware('rotas:18')->group(function () {
    Route::get('/gerenciar-tratamentos', [GerenciarTratamentosController::class, 'index'])->name('gtcdex');
    Route::get('/visualizar-tratamento/{idtr}', [GerenciarTratamentosController::class, 'visualizar'])->name('gecvis');
    Route::any('/presenca-tratatamento/{idtr}', [GerenciarTratamentosController::class, 'presenca']);
    Route::get('/registrar-falta', [GerenciarTratamentosController::class, 'falta'])->name('gtcfal');
    Route::any('/incluir-avulso', [GerenciarTratamentosController::class, 'createAvulso']);
    Route::any('/armazenar-avulso', [GerenciarTratamentosController::class, 'storeAvulso']);
    Route::any('/inativar-tratamento/{id}', [GerenciarTratamentosController::class, 'destroy']);
});

// Gerenciar Reuniões
Route::middleware('rotas:19')->group(function () {
    Route::get('/gerenciar-reunioes', [ReuniaoMediunicaController::class, 'index'])->name('remdex');
    Route::get('/criar-reuniao', [ReuniaoMediunicaController::class, 'create'])->name('remcre');
    Route::post('/nova-reuniao', [ReuniaoMediunicaController::class, 'store'])->name('remore');
    Route::get('/editar-reuniao/{id}', [ReuniaoMediunicaController::class, 'edit']);
    Route::any('/atualizar-reuniao/{id}', [ReuniaoMediunicaController::class, 'update']);
    Route::any('/excluir-reuniao/{id}', [ReuniaoMediunicaController::class, 'destroy']);
    Route::any('/visualizar-reuniao/{id}', [ReuniaoMediunicaController::class, 'show']);
});

// Gerenciar Atendente de Apoio
Route::middleware('rotas:20')->group(function () {
    Route::get('/gerenciar-atendentes-apoio', [AtendimentoApoioController::class, 'index'])->name('indexAtendenteApoio');
    Route::get('/incluir-atendentes-apoio', [AtendimentoApoioController::class, 'create']);
    Route::any('/armazenar-atendentes-apoio', [AtendimentoApoioController::class, 'store']);
    Route::any('/visualizar-atendentes-apoio/{id}', [AtendimentoApoioController::class, 'show']);
    Route::any('/editar-atendentes-apoio/{id}', [AtendimentoApoioController::class, 'edit']);
    Route::any('/atualizar-atendentes-apoio/{id}', [AtendimentoApoioController::class, 'update']);
});

// Gerenciar Plantonistas
Route::middleware('rotas:21')->group(function () {
    Route::get('/gerenciar-atendentes-plantonistas', [AtendentePlantonistaController::class, 'index'])->name('indexAtendentePlantonista');
    Route::get('/incluir-atendentes-plantonistas', [AtendentePlantonistaController::class, 'create']);
    Route::any('/armazenar-atendentes-plantonistas', [AtendentePlantonistaController::class, 'store']);
    Route::any('/visualizar-atendentes-plantonistas/{id}', [AtendentePlantonistaController::class, 'show']);
    Route::any('/editar-atendentes-plantonistas/{id}', [AtendentePlantonistaController::class, 'edit']);
    Route::any('/atualizar-atendentes-plantonistas/{id}', [AtendentePlantonistaController::class, 'update']);
});

// Gerenciar Encaminhamento PTI
Route::middleware('rotas:22')->group(function () {
    Route::get('/gerenciar-encaminhamentos-pti', [GerenciarEncaminhamentoPTIController::class, 'index']);
    Route::get('/agendar-pti/{ide}/{idtt}', [GerenciarEncaminhamentoPTIController::class, 'agenda']);
    Route::get('/agendar-tratamento-pti/{ide}', [GerenciarEncaminhamentoPTIController::class, 'tratamento']);
    Route::post('incluir-tratamento-pti/{idtr}', [GerenciarEncaminhamentoPTIController::class, 'tratar']);
    Route::get('/visualizar-enc-pti/{ide}', [GerenciarEncaminhamentoPTIController::class, 'visualizar']);
    Route::any('/inativar-pti/{ide}', [GerenciarEncaminhamentoPTIController::class, 'inative']);
    Route::get('/alterar-grupo-tratamento-pti/{id}', [GerenciarEncaminhamentoPTIController::class, 'escolherGrupo']);
    Route::get('/escolher-horario-pti/{id}', [GerenciarEncaminhamentoPTIController::class, 'escolherHorario']);
    Route::any('/trocar-grupo-tratamento-pti/{id}', [GerenciarEncaminhamentoPTIController::class, 'trocarGrupo']);
});

// Gerenciar Encaminhamento Integral
Route::middleware('rotas:23')->group(function () {
    Route::get('/gerenciar-encaminhamentos-integral', [GerenciarEncaminhamentoIntegralController::class, 'index']);
    Route::get('/agendar-integral/{ide}/{idtt}', [GerenciarEncaminhamentoIntegralController::class, 'agenda']);
    Route::get('/agendar-tratamento-integral/{ide}', [GerenciarEncaminhamentoIntegralController::class, 'tratamento']);
    Route::post('incluir-tratamento-integral/{idtr}', [GerenciarEncaminhamentoIntegralController::class, 'tratar']);
    Route::get('/visualizar-enc-integral/{ide}', [GerenciarEncaminhamentoIntegralController::class, 'visualizar']);
    Route::any('/inativar-integral/{ide}', [GerenciarEncaminhamentoIntegralController::class, 'inative']);
    Route::get('/alterar-grupo-tratamento-integral/{id}', [GerenciarEncaminhamentoIntegralController::class, 'escolherGrupo']);
    Route::get('/escolher-horario-integral/{id}', [GerenciarEncaminhamentoIntegralController::class, 'escolherHorario']);
    Route::any('/trocar-grupo-tratamento-integral/{id}', [GerenciarEncaminhamentoIntegralController::class, 'trocarGrupo']);
});

// Gerenciar Assistido PTI
Route::middleware('rotas:24')->group(function () {
    Route::get('/gerenciar-pti', [GerenciarPTIController::class, 'index']);
    Route::get('/alta-pti/{id}', [GerenciarPTIController::class, 'update']);
    Route::get('/visualizar-pti/{id}', [GerenciarPTIController::class, 'show']);
});

// Gerenciar Assistido Integral
Route::middleware('rotas:25')->group(function () {
    Route::get('/gerenciar-integral', [GerenciarIntegralController::class, 'index']);
    Route::get('/alta-integral/{id}', [GerenciarIntegralController::class, 'update']);
    Route::get('/visualizar-integral/{id}', [GerenciarIntegralController::class, 'show']);
});

// Gerenciar Perfis
Route::middleware('rotas:26')->group(function () {
    Route::get('/gerenciar-perfis', [GerenciarPerfil::class, 'index']);
    Route::get('/criar-perfis', [GerenciarPerfil::class, 'create']);
    Route::post('/armazenar-perfis', [GerenciarPerfil::class, 'store']);
    Route::get('/visualizar-perfis/{id}', [GerenciarPerfil::class, 'show']);
    Route::get('/editar-perfis/{id}', [GerenciarPerfil::class, 'edit']);
    Route::post('/atualizar-perfis/{id}', [GerenciarPerfil::class, 'update']);
    Route::any('/excluir-perfis/{id}', [GerenciarPerfil::class, 'destroy']);
});

// Gerenciar Rotas Setor
Route::middleware('rotas:27')->group(function () {
    Route::get('/gerenciar-setor', [GerenciarSetor::class, 'index']);
    Route::get('/criar-setor', [GerenciarSetor::class, 'create']);
    Route::post('/armazenar-setor', [GerenciarSetor::class, 'store']);
    Route::get('/visualizar-setor/{id}', [GerenciarSetor::class, 'show']);
    Route::get('/editar-setor/{id}', [GerenciarSetor::class, 'edit']);
    Route::post('/atualizar-setor/{id}', [GerenciarSetor::class, 'update']);
    Route::any('/excluir-setor/{id}', [GerenciarSetor::class, 'destroy']);
});

//Gerenciar Versões
Route::middleware('rotas:28')->group(function () {
    Route::get('/gerenciar-versoes', [GerenciarVersoesController::class, 'index']);
    Route::get('/incluir-versoes', [GerenciarVersoesController::class, 'create']);
    Route::post('/armazenar-versoes', [GerenciarVersoesController::class, 'store']);
    Route::get('/editar-versoes/{id}', [GerenciarVersoesController::class, 'edit']);
    Route::post('/atualizar-versoes/{id}', [GerenciarVersoesController::class, 'update']);
    Route::get('/visualizar-versoes/{id}', [GerenciarVersoesController::class, 'show']);
    Route::any('/excluir-versoes/{id}', [GerenciarVersoesController::class, 'destroy']);

});

// Gerenciar Presença Dirigente
Route::middleware('rotas:30')->group(function () {
    Route::get('/gerenciar-presenca', [PresencaDirigenteController::class, 'index'])->name('');
    Route::get('/dar-presenca', [PresencaDirigenteController::class, 'index'])->name('dar.presenca');
    Route::post('/dar-presenca', [PresencaDirigenteController::class, 'store'])->name('dar.presenca.store');

});

Route::middleware('rotas:30')->group(function () {
    Route::get('/gerenciar-presenca', [PresencaDirigenteController::class, 'index'])->name('');
    Route::get('/dar-presenca', [PresencaDirigenteController::class, 'index'])->name('dar.presenca');
    Route::post('/dar-presenca', [PresencaDirigenteController::class, 'store'])->name('dar.presenca.store');

});

//Relatório de Presença AFI
Route::middleware('rotas:30')->group(function () {
    Route::any('/presenca-afi', [RelatoriosController::class, 'presencaAFI']);
});

