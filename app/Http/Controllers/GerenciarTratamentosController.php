<?php

namespace App\Http\Controllers;

use App\Jobs\DiasCronograma;
use App\Jobs\DiasCronogramaOntem;
use App\Jobs\EntrevistaProamo;
use App\Jobs\Faltas;
use App\Jobs\FaltasTrabalhador;
use App\Jobs\FilaEncaminhamentos;
use App\Jobs\FimSemanas;
use App\Jobs\LimiteFalta;
use App\Jobs\LimiteValidacao;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Exists;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Coalesce as BinaryOpCoalesce;

class GerenciarTratamentosController extends Controller
{
    public function index(Request $request)
    {
        try {

            $now =  Carbon::now()->format('Y-m-d');

            $selectGrupo = explode(' ', $request->grupo);
            $lista = DB::table('tratamento AS tr')
                ->select('tr.id AS idtr', 'tr.status', 'enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tst.nome AS tst', 'enc.id_tipo_tratamento AS idtt', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'p1.cpf AS cpf_assistido', 'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tt.sigla', 'tr.id AS idtr', 'gr.nome AS nomeg', 'td.nome AS nomed', 'rm.h_inicio', 'tr.dt_fim')
                ->leftJoin('encaminhamento AS enc',  'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                ->leftJoin('tipo_status_tratamento AS tst', 'tr.status', 'tst.id')
                ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftjoin('tipo_dia AS td', 'rm.dia_semana', 'td.id')
                ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                ->where('enc.id_tipo_encaminhamento', 2)
                ->where('enc.id_tipo_tratamento', '<>', 3);

            $cronogramas = DB::table('cronograma as cro')
                ->select('cro.id', 'gr.nome', 'td.nome as dia', 'cro.h_inicio', 'cro.h_fim', 's.sigla as setor')
                ->leftJoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
                ->leftJoin('setor as s', 'gr.id_setor', 's.id')
                ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
                ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id')
                ->orderBy('gr.nome')
                ->get();

            $cronogramasDirigente = DB::table('membro')->where('id_associado', session()->get('usuario.id_associado'))->whereIn('id_funcao', [1, 2])->pluck('id_cronograma');


            //Setor DIVAP ou Master Admin



            //dd($cronogramasDirigente, $lista->get());
            // dd($cronogramas);
            $data_enc = $request->dt_enc;

            $diaP = $request->dia;

            $assistido = $request->assist;

            $situacao = $request->status;
            $cron = $request->grupo;


            $cpf = $request->cpf;

            $acesso = DB::table('usuario_acesso')->where('id_usuario', session()->get('usuario.id_usuario'))->where('id_acesso', session()->get('acessoAtual'))->where('id_setor', '51')->first();

            if (!$acesso and !in_array(36, session()->get('usuario.acesso'))) {
                $lista = $lista->whereIn('tr.id_reuniao', $cronogramasDirigente);
                $request->status ?? $situacao = 'all';
            }

            if ($request->dia != null) {
                $lista->where('rm.dia_semana', '=', $request->dia);
            }

            if ($request->dt_enc) {
                $lista->where('enc.dh_enc', '>=', $request->dt_enc);
            }

            if ($request->tratamento) {
                $lista->where('enc.id_tipo_tratamento', $request->tratamento);
            }

            if (current($selectGrupo) != '') {

                if (intval(current($selectGrupo)) != 0) {
                    $lista->where('rm.id', current($selectGrupo));
                } else {

                    $pesquisaNome = array();
                    $pesquisaNome = explode(' ', current($selectGrupo));

                    foreach ($pesquisaNome as $itemPesquisa) {
                        $lista->whereRaw("UNACCENT(LOWER(gr.nome)) ILIKE UNACCENT(LOWER(?))", ["%$itemPesquisa%"]);
                    }
                }

                if ($situacao == 'all') {
                    $lista->whereIn('tr.status', [1, 2]);
                }
            }

            if ($request->assist) {

                $pesquisaNome = array(); // Inicia um array
                $pesquisaNome = explode(' ', $request->assist); // Popula esse array com cada palavra digitada no input
                $margemErro = 0; // Inicializa em 0 uma variável de contagem de erros, usada para validação
                foreach ($pesquisaNome as $itemPesquisa) { // Para cada palavra na pesquisa

                    $bufferPessoa = (clone $lista); // Salva o estado anterior antes de pesquisar
                    $lista =  $lista->whereRaw("UNACCENT(LOWER(p1.nome_completo)) ILIKE UNACCENT(LOWER(?))", ["%$itemPesquisa%"]); // Pesquisa sem acento e sem case sensitive
                    if (count($lista->get()->toArray()) < 1) { // Caso durante o select, o banco não retorne nada
                        $pessoaVazia = (clone $lista); // Guarda em uma variável o que é um estado vazio, para popular a tabela
                        $lista = (clone $bufferPessoa); // Devolve a variável para o estado antes dessa pesquisa
                        $margemErro += 1; // Adiciona 1 na varíavel de contagem para validação

                    }
                }
                if ($margemErro == 0) { // Caso não tenha sofrido nenhum erro, passa direto
                } else if ($margemErro < (count($pesquisaNome) / 2)) { // Caso o número de erros seja inferior a 50% dos dados indicados
                    app('flasher')->addWarning('Nenhum Item Encontrado. Mostrando Pesquisa Aproximada');
                } else {
                    //Transforma a variavel em algo vazio
                    $lista = $pessoaVazia;
                    app('flasher')->addError('Nenhum Item Encontrado!');
                }
            }


            if ($request->cpf) {
                $lista->whereRaw("LOWER(p1.cpf) LIKE LOWER(?)", ["%{$request->cpf}%"]);
            } else {

                if ($request->status && $situacao != 'all') {
                    $lista->where('tr.status', $request->status);
                } elseif ($situacao == 'all') {
                } elseif (current($selectGrupo) == '') {
                    $lista->where('tr.status', 2);
                }
            }

            $contar = $lista->count('enc.id');
            $lista = $lista->orderby('tr.status', 'ASC')
                ->orderby('nm_1', 'ASC')
                ->orderby('at.id_prioridade', 'ASC')
                ->paginate(50)
                ->appends([
                    'assist' => $assistido,
                    'cpf' => $cpf,
                    'dt_enc' => $data_enc,
                    'dia' => $diaP,
                    'status' => $situacao,
                    'grupo' => $cron,
                    'tratamento' => $request->tratamento
                ]);


            $stat = DB::select("select
        ts.id,
        ts.nome
        from tipo_status_tratamento ts
        ");

            $dia = DB::select("select
        id,
        nome
        from tipo_dia
        ");

            $motivo = DB::table('tipo_motivo')->where('vinculado', 1)->get();



            return view('/recepcao-integrada/gerenciar-tratamentos', compact('cron', 'cronogramas', 'cpf', 'lista', 'stat', 'contar', 'data_enc', 'assistido', 'situacao', 'now', 'dia', 'diaP', 'motivo'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }

    public function destroy(Request $request, string $id)
    {

        try {

            $hoje = Carbon::today();
            $tratamento = DB::table('tratamento')->where('id', $id)->first();


            DB::table('tratamento')->where('id', $id)->update(['status' => 6, 'dt_fim' => $hoje]);
            DB::table('encaminhamento')->where('id', $tratamento->id_encaminhamento)->update(['status_encaminhamento' => 4, 'motivo' => $request->motivo,]);


            // Recupera o nome completo da pessoa associado ao id_usuario
            $nomePessoa = DB::table('pessoas')
                ->where('id', session()->get('usuario.id_usuario'))
                ->value('nome_completo');

            // Realiza a inserção na tabela 'historico_venus'
            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $hoje,
                'fato' => 24,
                'obs' => 'Tratamento inativado',
                'pessoa' => $nomePessoa,
            ]);



            app('flasher')->addSuccess('O tratamento foi inativado.');

            return redirect()->back();
        } catch (\Exception $e) {

            app('flasher')->addDanger('Erro ao inativar o tratamento.');


            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }


    public function presenca(Request $request, $idtr)
    {
        try {


            $data_atual = Carbon::now(); // Retorna o DATETIME de agora
            $dia_atual = $data_atual->weekday(); // Retorna qual o dia da semana de hoje

            // Confere a quantidade de presenças do assistido no dia de hoje
            $confere = DB::table('presenca_cronograma AS ds')
                ->leftJoin('dias_cronograma as dc', 'ds.id_dias_cronograma', 'dc.id')
                ->where('dc.data', $data_atual)
                ->where('ds.id_tratamento', $idtr)
                ->count();

            // Retorna todos os dados do tratamento
            $lista = DB::table('tratamento AS tr')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('tr.id', $idtr)
                ->first();


            // Retorna o dia cronograma, usado para gerar a presença para o dia de hoje
            $dia_cronograma = DB::table('dias_cronograma')->where('id_cronograma', $lista->id_reuniao)->where('data', $data_atual)->first();


            if ($confere > 0) {
                app('flasher')->addError('Já foi registrada a presença para este dia.');

                return Redirect()->back();
            } else if ($lista->dia_semana != $dia_atual) {
                app('flasher')->addError('Este assistido não corresponde ao dia de hoje.');
                return Redirect()->back();
            } else {

                // Usado para validar tratamentos PTD atrelados a um tratamento PTI
                $encaminhamentosPTD = DB::table('encaminhamento as enc')
                    ->select('at.id')
                    ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                    ->where('id_assistido', $lista->id_assistido)
                    ->where('id_tipo_tratamento', 1) // PTD
                    ->where('status_encaminhamento', '<', 3) // Apenas Ativos
                    ->first(); // XXX Caso o assistiddo tenha 2 PTD, ignora um deles

                // Caso o tratamento esteja no status AGUARDANDO TRATAMENTO
                if ($lista->status == 1) {

                    // Troca o tratamento para o status EM TRATAMENTO
                    DB::table('tratamento')->where('id', $idtr)->update([
                        'status' => 2
                    ]);

                    // Caso o tratamento seja PTI e tenha tratamentosPTD ativos
                    if ($lista->id_tipo_tratamento == 2 and $encaminhamentosPTD) {

                        DB::table('encaminhamento')->where('id', $encaminhamentosPTD->id)->update([ // Inativa o encaminhamento PTD
                            'status_encaminhamento' => 3
                        ]);
                        DB::table('tratamento')->where('id_encaminhamento', $encaminhamentosPTD->id)->update([ // Inativa o tratamento PTD
                            'status' => 4
                        ]);
                    }
                }


                $acompanhantes = isset($dia_cronograma->nr_acompanhantes)  ? $dia_cronograma->nr_acompanhantes : 0; // Salva o numero atual de acompanhantes
                $nrAcomp = $acompanhantes + $request->acompanhantes; // Soma a quantidade total de acompanhantes

                // Recupera o nome completo da pessoa associado ao id_usuario
                $nomePessoa = DB::table('pessoas')
                    ->where('id', session()->get('usuario.id_usuario'))
                    ->value('nome_completo');

                // Realiza a inserção na tabela 'historico_venus'
                DB::table('historico_venus')->insert([
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'data' => $data_atual,
                    'fato' => 25,
                    'obs' => 'Presença em tratamento',
                    'pessoa' => $nomePessoa,
                ]);

                // Atualiza o número de acompanhantes da reunião
                DB::table('dias_cronograma')
                    ->where('id_cronograma', $lista->id_reuniao)
                    ->where('data', $data_atual)
                    ->update([
                        'nr_acompanhantes' => $nrAcomp
                    ]);

                // Insere a presença na tabela
                DB::table('presenca_cronograma')
                    ->insert([
                        'id_tratamento' => $idtr,
                        'presenca' => true,
                        'id_dias_cronograma' => $dia_cronograma->id
                    ]);


                app('flasher')->addSuccess('Foi registrada a presença com sucesso.');

                return Redirect()->back();
            }

            app('flasher')->addError('Aconteceu um erro inesperado.');

            return Redirect()->back();
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }


    public function visualizar($idtr)
    {

        // Devolve o ID pessoa daquele encaminhamento, para buscar outros encaminhamentos, mesmo que não conectados
        $pessoa = DB::table('tratamento')
            ->leftJoin('encaminhamento', 'tratamento.id_encaminhamento', 'encaminhamento.id')
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
            ->where('tratamento.id', $idtr)
            ->first('id_assistido');

        // Traz todas as informações da view exceto o header com nome, e o footer com as faltas
        $result = DB::table('encaminhamento AS enc')
            ->select(
                'at.id AS ida', // ID atendimento, usado em Dados Atendimento Fraterno
                'at.dh_inicio', // Datetime de Inicio do atendimento
                'at.dh_fim', // Datetime de fim do atendimento
                'enc.id AS ide',
                'gr.nome AS nomeg', // Nome do grupo, mostrado em Dados do Encaminhamento
                'p1.dt_nascimento', // Data de Nascimento Assistido usado em header
                'p1.nome_completo AS nm_1', // Nome do Assistido usado em header
                'p2.nome_completo as nm_2', // Nome do representante, usado em Dados do Atendimento Fraterno
                'p4.nome_completo AS nm_4', // Nome do Atendente, usado em Dados do Atendimento Fraterno
                'p1.id as id_pessoa',
                'pa.nome', // Parentesco do representante com o Assistido (Ex.: Pai, Irmão)
                'rm.h_inicio AS rm_inicio', // Inicio do Cronograma do Tratamento Marcado
                'td.nome as nomedia', // Utilizado em Dados Encaminhamento para o Dia do Grupo
                'tsa.descricao AS tst', // Status do atendimento, em String
                'tse.nome AS tsenc', // Status do encaminhamento, em String
                'tm.tipo AS tpmotivo', // Motivo de cancelamento do encaminhamento
                'tr.id as idt',
                'tr.dt_inicio', // Inicio Real do Tratamento
                'tr.dt_fim as final', // Final do Tratamento
                'tt.descricao AS desctrat', // Tipo de tratamento, usado em Dados do Encaminhamento (Ex.: Passe de Tratamento Desobsessivo)
                'tx.tipo', // Sexo do assistido, usado no header
            )
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
            ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
            ->leftJoin('tipo_status_tratamento AS tse', 'tr.status', 'tse.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'rm.dia_semana', 'td.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('tipo_status_atendimento AS tsa', 'at.status_atendimento', 'tsa.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftjoin('associado AS ass', 'at.id_atendente', 'ass.id')
            ->leftjoin('pessoas AS p4', 'ass.id_pessoa', 'p4.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->Where('tr.id', $idtr)
            ->first();




        $encaminhamentosAlternativos = DB::table('encaminhamento as enc')
            ->select(
                'enc.id as ide',
                'gr.nome',
                'rm.h_inicio',
                'td.nome as dia',
                'tr.id as idt',
                'tr.dt_inicio',
                'tr.dt_fim',
                'tt.descricao',
                'tse.nome as status'
            )
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
            ->leftJoin('tipo_status_tratamento AS tse', 'tr.status', 'tse.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'rm.dia_semana', 'td.id')
            ->where('at.id_assistido', $pessoa->id_assistido) // Todos daquele assistido
            ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
            ->whereNot('enc.id_tipo_tratamento', 3) // Remove da lista o PTH (Passe de Tratamento de Harmonização)
            ->where('tr.status', '<', 3)
            ->whereNot('enc.id', $idtr)
            ->get();

        $emergencia = DB::table('presenca_cronograma as dt')
            ->select(
                'dt.id AS idp',
                'dt.presenca',
                'dc.data',
                'gp.nome'
            )
            ->leftJoin('tratamento as tr', 'dt.id_tratamento', 'tr.id')
            ->leftJoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
            ->leftJoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
            ->leftJoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
            ->where('dt.id_pessoa', '=', $result->id_pessoa)
            ->whereNull('dt.id_tratamento')
            ->orderBy('dc.data','desc')
            ->get()
            ->toArray();


        // Retorna todos os dados de presença do encaminhamento atual
        $list = DB::table('presenca_cronograma as pc')
            ->select('pc.id as idp', 'dc.data', 'pc.presenca', 'gr.nome')
            ->leftJoin('dias_cronograma as dc', 'id_dias_cronograma', 'dc.id')
            ->leftJoin('cronograma as cr', 'dc.id_cronograma', 'cr.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
            ->where('tr.id_encaminhamento', $idtr)
            ->orderBy('dc.data', 'desc')
            ->get();

        // Conta a quantidade de faltas do encaminhamento atual
        $faul = DB::table('tratamento AS tr')
            ->select('dt.presenca')
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
            ->where('enc.id', $idtr)
            ->where('dt.presenca', 0)
            ->count();


        return view('/recepcao-integrada/historico-tratamento', compact('emergencia', 'result', 'list', 'faul', 'encaminhamentosAlternativos'));
    }

    // Edit de Reverter Faltas, utilizado por Tratamentos, Tratamento Integral, Tratamento PTI e Tratamento PROAMO
    public function faltas($idtr)
    {

        // Reconhece qual a rota anterior a essa, utilizado para retornar o usuário para sua tela de origen
        $urlAnterior = str_replace(url('/'), '', url()->previous());

        // Retorna os dados pessoais do assistido para o cabeçalho
        $result = DB::table('tratamento as tr')
            ->select('p.nome_completo as nm_1', 'p.dt_nascimento', 'ts.tipo')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->leftJoin('tp_sexo as ts', 'p.sexo', 'ts.id')
            ->where('tr.id', $idtr)
            ->first();

        // Retorna todos os dados das presenças para utilização na view
        $list = DB::table('presenca_cronograma AS dt')
            ->select(
                'enc.id AS ide',
                'enc.id_tipo_encaminhamento',
                'enc.dh_enc',
                'enc.status_encaminhamento AS tst',
                'tr.id AS idtr',
                'rm.h_inicio AS rm_inicio',
                'dt.id AS idp',
                'dt.presenca',
                'dc.data',
                'gp.nome',
                'dt.id'
            )
            ->leftJoin('tratamento AS tr', 'dt.id_tratamento', 'tr.id')
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
            ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
            ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
            ->where('tr.id', $idtr)
            ->orderBy('dc.data', 'desc')
            ->get();


        // Armazena em uma variável, organizado por ano, todas as presenças do assistido, usado pelo accordion da view
        $arrayPresencas = [];
        foreach ($list as $presenca) {
            $arrayPresencas[date('Y', strtotime($presenca->data))][] = $presenca;
        }

        // Remarca $list com a organização anterior
        $list = $arrayPresencas;


        return view('/recepcao-integrada/reverter-faltas-assisitido', compact('result', 'list', 'urlAnterior'));
    }

    // Update de Reverter Falas
    public function remarcar(Request $request)
    {
        
        $data_atual = Carbon::now();

        // Caso alguma checkbox seja marcada
        if ($request->checkbox) {

            // Para cada checkbox marcada, separa a chave do valor
            foreach ($request->checkbox as $key => $presenca) {

                // Caso o valor da presença não seja TRUE, reconhece como false, em caso que haja NULL
                $booleanPresenca = $presenca ?? false;

                // Insere no banco de dados, onde o ID é igual a chave passada pela view, o inverso do boolean atual
                DB::table('presenca_cronograma')
                    ->where('id', $key)
                    ->update([
                        'presenca' => !$booleanPresenca
                    ]);


                $nomePessoa = DB::table('pessoas')
                    ->where('id', session()->get('usuario.id_pessoa'))
                    ->value('nome_completo');

                // Realiza a inserção na tabela 'historico_venus'
                DB::table('historico_venus')->insert([
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'data' => $data_atual,
                    'fato' => 27,
                    'obs' => 'alterou a presença/falta do assistido',
                    'pessoa' => $nomePessoa,
                    'id_ref' => $key,
                ]);

                app('flasher')->addSuccess('Presença alterada com sucesso.');
            }
        } else { // Caso nenhuma checkbox seja marcada
            app('flasher')->addError('Nenhum item selecionado.');
        }

        return redirect($request->url);
    }

    // Executa manualmente todos os JOBs
    public function job()
    {

        DiasCronogramaOntem::dispatch();
        DiasCronograma::dispatch();
        LimiteFalta::dispatch();
        FimSemanas::dispatch();
        Faltas::dispatch();
        EntrevistaProamo::dispatch();
        FaltasTrabalhador::dispatch();
        FilaEncaminhamentos::dispatch();
        LimiteValidacao::dispatch();

        return redirect()->back();
    }


    public function createAvulso()
    {
        try {

            $hoje = Carbon::today();
            $dia = Carbon::today()->weekday();

            // Busca o nome dos assisitido para o select do avulso (atendimento de emergência)
            $assistidos = DB::table('pessoas')->select('id', 'nome_completo')->orderBy('nome_completo')->get();

            // Busca os cronogramas ativos PTD para o select (atendimento de emergência)
            $reuniao = DB::table('cronograma as cro')
                ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
                ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
                ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
                ->where('cro.id_tipo_tratamento', 1) // Tratamento PTD
                ->where('cro.dia_semana', $dia) // Dia de hoje
                ->where(function ($query) use ($hoje) { // Cronogramas Ativos
                    $query->whereRaw("cro.data_fim < ?", [$hoje])
                        ->orWhereNull('cro.data_fim');
                })
                ->select('cro.id', 'cro.h_inicio', 'cro.h_fim', 'td.nome as nomedia', 'gr.nome', 'sl.numero as sala')
                ->get();


            // Retorna os motivos para a criação do avulso
            $motivo = DB::table('tipo_motivo_presenca')->get();


            return view('recepcao-integrada.incluir-avulso', compact('assistidos', 'reuniao', 'motivo'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }
    public function storeAvulso(Request $request)
    {
        $hoje = Carbon::today();


        $acompanhantes = isset($dia_cronograma->nr_acompanhantes)  ? $dia_cronograma->nr_acompanhantes : 0; // Salva o numero atual de acompanhantes
        $nrAcomp = $acompanhantes + $request->acompanhantes; // Soma a quantidade total de acompanhantes

        // Recolhe o dia cronoograma do grupo selecionado
        $acompanhantes = DB::table('dias_cronograma')
            ->where('id_cronograma', $request->reuniao)
            ->where('data', $hoje);


        $acompanhantesId = $acompanhantes->get();

        // Atualiza o número de acompanhantes da reunião
        $acompanhantes->update([
            'nr_acompanhantes' => $nrAcomp
        ]);


        // Insere a presença do assistido
        DB::table('presenca_cronograma')->insert([
            'presenca' => true,
            'id_pessoa' => $request->assistido,
            'id_dias_cronograma' => $acompanhantesId,
            'id_motivo' => $request->motivo
        ]);

        return redirect('/gerenciar-tratamentos');
    }
    public function visualizarRI(Request $request)
    {

        $now =  Carbon::now()->format('Y-m-d');

        $selectGrupo = explode(' ', $request->grupo);
        
        // Recupera o nome da pessoa, o tratamento e o dia, exibindo essas informações na tela
        $lista = DB::table('tratamento AS tr')
            ->select(
                'tr.id AS idtr',
                'tr.status',
                'enc.id AS ide',
                'enc.id_tipo_encaminhamento',
                'dh_enc',
                'enc.id_atendimento',
                'enc.status_encaminhamento',
                'tst.nome AS tst',
                'enc.id_tipo_tratamento AS idtt',
                'id_tipo_entrevista',
                'at.id AS ida',
                'at.id_assistido',
                'p1.nome_completo AS nm_1',
                'at.id_representante as idr',
                'p2.nome_completo as nm_2',
                'p1.cpf AS cpf_assistido',
                'pa.nome',
                'pr.id AS prid',
                'pr.descricao AS prdesc',
                'pr.sigla AS prsigla',
                'tt.descricao AS desctrat',
                'tt.sigla',
                'tr.id AS idtr',
                'gr.nome AS nomeg',
                'td.nome AS nomed',
                'rm.h_inicio',
                'tr.dt_fim'
            )
            ->leftJoin('encaminhamento AS enc',  'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->leftJoin('tipo_status_tratamento AS tst', 'tr.status', 'tst.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftjoin('tipo_dia AS td', 'rm.dia_semana', 'td.id')
            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
            ->where('enc.id_tipo_encaminhamento', 2)
            ->where('enc.id_tipo_tratamento', '<>', 3);

          // Recupera o grupo, os horários, a sala, a sigla e o setor, exibindo essas informações na tela
        $cronogramas = DB::table('cronograma as cro')
            ->select('cro.id', 'gr.nome', 'td.nome as dia', 'cro.h_inicio', 'cro.h_fim', 's.sigla as setor')
            ->leftJoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id')
            ->get();

        $cronogramasDirigente = DB::table('membro')->where('id_associado', session()->get('usuario.id_associado'))->whereIn('id_funcao', [1, 2])->pluck('id_cronograma');



        $data_enc = $request->dt_enc;

        $diaP = $request->dia;

        $assistido = $request->assist;

        $situacao = $request->status;
        $cron = $request->grupo;


        $cpf = $request->cpf;

        $acesso = DB::table('usuario_acesso')->where('id_usuario', session()->get('usuario.id_usuario'))->where('id_acesso', session()->get('acessoAtual'))->where('id_setor', '51')->first();

        if (!$acesso and !in_array(36, session()->get('usuario.acesso'))) {
            $lista = $lista->whereIn('tr.id_reuniao', $cronogramasDirigente);
            $request->status ?? $situacao = 'all';
        }

        if ($request->dia != null) {
            $lista->where('rm.dia_semana', '=', $request->dia);
        }

        if ($request->dt_enc) {
            $lista->where('enc.dh_enc', '>=', $request->dt_enc);
        }

        if ($request->tratamento) {
            $lista->where('enc.id_tipo_tratamento', $request->tratamento);
        }

        if (current($selectGrupo) != '') {

            if (intval(current($selectGrupo)) != 0) {
                $lista->where('rm.id', current($selectGrupo));
            } else {

                $pesquisaNome = array();
                $pesquisaNome = explode(' ', current($selectGrupo));

                foreach ($pesquisaNome as $itemPesquisa) {
                    $lista->whereRaw("UNACCENT(LOWER(gr.nome)) ILIKE UNACCENT(LOWER(?))", ["%$itemPesquisa%"]);
                }
            }

            if ($situacao == 'all') {
                $lista->whereIn('tr.status', [1, 2]);
            }
        }

        if ($request->assist) {
            $pesquisaNome = array();
            $pesquisaNome = explode(' ', $request->assist);

            foreach ($pesquisaNome as $itemPesquisa) {
                $lista->whereRaw("UNACCENT(LOWER(p1.nome_completo)) ILIKE UNACCENT(LOWER(?))", ["%$itemPesquisa%"]);
            }
        }


        if ($request->cpf) {
            $lista->whereRaw("LOWER(p1.cpf) LIKE LOWER(?)", ["%{$request->cpf}%"]);
        } else {

            if ($request->status && $situacao != 'all') {
                $lista->where('tr.status', $request->status);
            } elseif ($situacao == 'all') {
            } elseif (current($selectGrupo) == '') {
                $lista->where('tr.status', 2);
            }
        }

        $contar = $lista->count('enc.id');
        $lista = $lista->orderby('tr.status', 'ASC')
            ->orderby('nm_1', 'ASC')
            ->orderby('at.id_prioridade', 'ASC')
            ->paginate(50)
            ->appends([
                'assist' => $assistido,
                'cpf' => $cpf,
                'dt_enc' => $data_enc,
                'dia' => $diaP,
                'status' => $situacao,
                'grupo' => $cron,
                'tratamento' => $request->tratamento
            ]);


        $stat = DB::select("select
        ts.id,
        ts.nome
        from tipo_status_tratamento ts
        ");

        $dia = DB::select("select
        id,
        nome
        from tipo_dia
        ");

        $motivo = DB::table('tipo_mot_inat_at_enc')->get();



        return view('recepcao-integrada.visualizarRI', compact(
            'cron',
            'cronogramas',
            'cpf',
            'lista',
            'stat',
            'contar',
            'data_enc',
            'assistido',
            'situacao',
            'now',
            'dia',
            'diaP',
            'motivo'
        ));
    }
}
