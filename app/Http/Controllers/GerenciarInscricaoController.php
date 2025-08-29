<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GerenciarInscricaoController extends Controller
{

    protected $setaut;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->setaut = collect(session('acessoInterno'))
                ->flatten(1)
                ->pluck('id_setor')
                ->unique()
                ->toArray();

            return $next($request);
        });
    }

     public function index(Request $request)
    {       

       $inscricao = DB::table('inscricao AS i')
            ->select(
                'i.id AS idi',
                'p.nome_completo',
                'p.cpf',
                'cro.id AS idc',
                'gr.nome AS nomeg',
                'cro.dia_semana AS idd',
                'cro.id_sala',
                'cro.id_tipo_tratamento',
                'cro.id_tipo_semestre',
                'cro.h_inicio',
                'td.nome AS nomed',
                'cro.h_fim',
                'cro.max_atend',
                'cro.max_trab',
                'cro.data_inicio',
                'cro.data_fim',
                'gr.status_grupo AS idst',
                'tt.descricao AS trnome',
                'tt.sigla AS trsigla',
                's.sigla as stsigla',
                'tse.sigla as sesigla',
                'sa.numero',
                't.descricao',
                'tm.nome as nmodal',
                'ts.nome as nsemana',
                'tt.descricao as tipo',
                'tt.id as idt',
                'tt.id_tipo_grupo',
                'tsi.tipo',
                'tsi.id AS statusid',
                DB::raw("(CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END) as status")
            )
            ->leftJoin('pessoas AS p', 'i.id_pessoa', 'p.id' )
            ->leftJoin('historico_inscricao AS hi', 'i.id', 'hi.id_inscricao')
            ->leftJoin('cronograma AS cro', 'hi.id_cronograma_novo', 'cro.id' )
            ->leftJoin('tipo_tratamento AS tt', 'cro.id_tipo_tratamento', 'tt.id')
            ->leftJoin('tipo_observacao_reuniao AS t', 'cro.observacao', 't.id')
            ->leftJoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor AS s', 'gr.id_setor', 's.id')
            ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id')
            ->leftJoin('tipo_modalidade AS tm', 'cro.id_tipo_modalidade', 'tm.id')
            ->leftJoin('tipo_semana AS ts', 'cro.id_tipo_semana', 'ts.id')
            ->leftJoin('tipo_semestre AS tse', 'tt.id_semestre', 'tse.id')
            ->leftJoin('tipo_status_inscricao AS tsi', 'i.status', 'tsi.id')
            ->where('gr.id_tipo_grupo', 2)
            ->whereIn('gr.id_setor', $this->setaut)
            ->whereIn('hi.id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('historico_inscricao')
                    ->groupBy('id_inscricao');
            });



        // Obtém os valores de pesquisa da requisição
        $semana = $request->input('semana', null);
        $grupo = $request->input('grupo', null);
        $tipo_tratamento = $request->input('tipo_tratamento', null);
        $semestre = $request->input('semestre', null);
        $setor = $request->input('setor', null);
        $status = $request->input('status', null);
        $modalidade = $request->input('modalidade', null);
        $pessoa = $request->input('pessoa', null);
        $cpf = $request->input('cpf', null);


        //dd($tipo_tratamento, $semestre );
        // Aplica filtro por semana
        if ($semana != '') {
            // Se o valor de semana não for vazio, aplica o filtro
            $inscricao->where('cro.dia_semana', '=', $semana);
        }

        if ($grupo) {
            $inscricao->where('cro.id_grupo', $grupo);
        }


        if ($request->filled('tipo_tratamento')) {
            $descricao = DB::table('tipo_tratamento')
                ->where('id', $request->input('tipo_tratamento'))
                ->value('descricao');

            $ids = DB::table('tipo_tratamento')
                ->where('descricao', $descricao)
                ->pluck('id');

            $inscricao->whereIn('cro.id_tipo_tratamento', $ids);
        }

        if ($semestre) {
            $inscricao->when($semestre, function ($query, $semestre) {
            return $query->where('tt.id_semestre', $semestre);
            });
        }

        if ($setor) {
            $inscricao->where('gr.id_setor', $setor);
        }
        // Aplica filtro por status com base na expressão CASE WHEN
        $statusCaseWhen = DB::raw("CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END");
        // dd($reuniao->get());
        if ($status) {
            switch ($status) {
                case 1:
                    $inscricao->where($statusCaseWhen, 'Ativo');
                    break;
                case 2:
                    $inscricao->where($statusCaseWhen, 'Inativo');
                    break;
                case 3:
                    $inscricao->where($statusCaseWhen, 'Experimental');
                    break;
                case 4:
                    $inscricao->where($statusCaseWhen, 'Em ferias');
                    break;
            }
        }

        // Aplica filtro por setor
        if ($modalidade) {
            $inscricao->where('tm.id', $modalidade);
        }
        // Filtrar por nome da pessoa
        if ($pessoa != '') {
            // Se o valor de pessoa não for vazio, aplica o filtro
            $inscricao->where('p.nome_completo', $pessoa);
        }
         // Filtrar por cpf da pessoa
        if ($cpf != '') {
            // Se o valor de cpf não for vazio, aplica o filtro
            $inscricao->where('p.cpf', $cpf);
        }
        // Conta o número de registros
        $contar = $inscricao->distinct()->count('i.id');

        // Aplica a paginação e mantém os parâmetros de busca na URL
        $inscricao = $inscricao            
            ->orderBy('status', 'ASC')
            ->orderBy('cro.id_tipo_tratamento', 'ASC')
            ->orderBy('nomeg', 'ASC')
            ->groupBy('tt.id', 'tsi.id', 'i.id', 'cro.id', 'p.nome_completo', 'gr.nome', 'tt.id', 'tt.sigla', 'td.nome', 'tse.sigla', 't.descricao', 'gr.status_grupo', 'tt.descricao', 's.sigla', 'sa.numero', 'tm.nome', 'ts.nome', 'p.cpf', 'tsi.tipo')
            ->paginate(10)
            ->appends([
                'status' => $status,
                'semana' => $semana,
                'grupo' => $grupo,
                'setor' => $setor,
                'tipo_tratamento' => $tipo_tratamento,
                'modalidade' => $modalidade
            ]);
        
            //dd($inscricao->statusid);



        // Carregar a lista de grupos para o Select2
        $grupos = DB::table('cronograma as c')
        ->leftJoin('grupo AS g', 'c.id_grupo', 'g.id')
        ->leftJoin('setor AS s', 'g.id_setor', 's.id')
        ->select(
            'g.id AS idg',
            'g.nome AS nomeg',
            's.sigla'
        )
        ->where('g.id_tipo_grupo', 2)
        ->orderBy('g.nome', 'asc')
        ->get()
        ->unique('idg') // aqui garantimos que o ID do grupo seja único
        ->values();     // reindexa os itens do array

               // Obtém os dados para os filtros
        $situacao = DB::table('tipo_status_inscricao')->select('id AS ids', 'tipo AS descs')->get();

        $tipo_tratamento = DB::table('tipo_tratamento AS tt')
        ->select('tt.id AS idt','tt.descricao', 'tt.sigla AS tipo')
        ->orderBy('tt.sigla')
        ->distinct('tt.sigla')
        ->get();

        $tipo_semestre = DB::table('tipo_tratamento AS tt')
        ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
        ->whereNotNull('tt.id_semestre')
        ->select('ts.id AS ids', 'ts.sigla')
        ->orderBy('ts.id')
        ->get();


        $tmodalidade = DB::table('tipo_modalidade')
        ->select('id AS idm', 'nome AS nomem')
        ->get();

        $tpdia = DB::table('tipo_dia')
            ->select('id AS idtd', 'nome AS nomed')
            ->orderByRaw('CASE WHEN id = 0 THEN 1 ELSE 0 END, idtd ASC')
            ->get();

        // Carregar a lista de setores para o Select2
        $setores = DB::table('setor')->orderBy('nome', 'asc')->get();

        $motivo = DB::table('tipo_motivo_status_pessoa')->select('id', 'motivo')->orderBy('motivo')->get();

         return view('/inscricao/gerenciar-inscricao', compact('tipo_semestre', 'motivo', 'cpf', 'pessoa', 'inscricao', 'tpdia', 'situacao', 'status', 'contar', 'semana', 'grupos', 'setores', 'tmodalidade', 'modalidade', 'tipo_tratamento'));
    }

    public function formar(Request $request)
    {

        $turma = DB::table('cronograma as c')     
                    ->leftJoin('historico_inscricao AS hi', 'c.id', 'hi.id_cronograma_novo') 
                    ->leftJoin('inscricao AS i', 'hi.id_inscricao', 'i.id')
                    ->leftJoin('grupo AS g', 'c.id_grupo', 'g.id')
                    ->leftJoin('setor AS s', 'g.id_setor', 's.id')
                    ->leftJoin('tipo_dia AS td', 'c.dia_semana', 'td.id')
                    ->leftJoin('tipo_tratamento AS tt', 'c.id_tipo_tratamento', 'tt.id')
                    ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
                    ->leftJoin('tipo_modalidade AS tm', 'c.id_tipo_modalidade', 'tm.id')
                    ->leftJoin('tipo_observacao_reuniao AS obs', 'c.observacao', 'obs.id')
                    ->leftJoin('salas AS sa', 'c.id_sala', 'sa.id')
                    ->select(
                        'c.id AS idc',
                        'g.id AS idg',
                        'tt.id AS idt',
                        'g.nome AS nomeg',
                        'sa.numero AS sala',
                        'c.id_tipo_tratamento AS id_tratamento',
                        'c.h_inicio',
                        'c.h_fim',
                        'obs.descricao AS observacao',
                        'td.nome AS dia_semana',
                        's.sigla',
                        'c.max_atend',
                        'tt.descricao AS desct',
                        'tt.sigla AS siglat',
                        'ts.sigla AS siglas',
                        'tm.nome AS nomem',
                        DB::raw('c.max_atend - count(hi.id_cronograma_novo) AS vaga')                        
                    )
                    ->where('g.id_tipo_grupo', 2)
                    ->whereIn('g.id_setor', $this->setaut)
                    ->groupBy('c.id', 'tt.id', 'td.id', 'g.id', 's.sigla', 'td.nome', 'tt.descricao', 'tt.sigla', 'ts.sigla', 'tm.nome', 'obs.descricao', 'sa.numero')
                    ->orderBy('td.id', 'asc')
                    ->get()
                    ->values();     // reindexa os itens do array

        $turmasAgrupadas = $turma->groupBy('modalidade')
        ->map(function ($grupoModalidade) {
        // dd($grupoModalidade);
            return $grupoModalidade->groupBy(function ($item) {
                
                return $item->desct . ' - ' . $item->siglas;
                
            });
        });

   

        $atividade = DB::table('tipo_tratamento AS tt')
                            ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
                            ->leftJoin('tipo_grupo AS tg', 'tt.id_tipo_grupo', 'tg.id')
                            ->whereNotNull('tt.id_semestre')
                            ->select('tt.id AS ida', 'tt.descricao', 'tt.id_tipo_grupo AS tpg', 'ts.sigla AS siglas', 'tt.sigla AS siglat', 'ts.id AS ids')
                            ->orderBy('ts.id')
                            ->get();

        $tmodalidade = DB::table('tipo_modalidade')
                        ->select('id AS idm', 'nome AS nomem')
                        ->get();

         $pessoa = DB::table('pessoas AS p')
                        ->select('id', 'nome_completo', 'cpf')
                        ->get();


        return view('/inscricao.incluir-inscricao', compact('turmasAgrupadas', 'turma','tmodalidade', 'atividade', 'pessoa')); 

    }

    public function criar(Request $request)
    {
         //dd($request->input('curso'));
        $now = Carbon::now()->format('Y-m-d');
        $id_pessoa = $request->input('id_pessoa'); 
        $crono = $request->input('curso');

        //dd($id_pessoa, $crono);

        $novoCurso = DB::table('cronograma AS c')
                    // ->leftJoin('tipo_tratamento AS tt', 'c.id_tipo_tratamento', 'tt.id')
                    ->where('c.id', $crono)
                    ->select('c.dia_semana', 'c.h_inicio', 'c.h_fim')
                    ->first();
        //dd($novoCurso);

        $EmCurso = DB::table('cronograma AS c')
                    ->leftJoin('tipo_tratamento AS tt', 'c.id_tipo_tratamento', 'tt.id')
                    ->leftJoin('grupo AS g', 'c.id_grupo', 'g.id')
                    ->leftJoin('historico_inscricao AS hi', 'c.id', 'hi.id_cronograma_novo' )
                    ->leftJoin('inscricao AS i', 'hi.id_inscricao', 'i.id')
                    ->leftJoin('pessoas AS p', 'i.id_pessoa', 'p.id')
                    ->where('p.id', $id_pessoa)
                    ->where('tt.id_tipo_grupo', 2)
                    ->select('c.id_tipo_tratamento', 'c.dia_semana', 'c.h_inicio', 'c.h_fim')
                    ->count();

  //dd($EmCurso);

        if (!$novoCurso) {
               
                app('flasher')->addError('Esse curso não existe');
                return redirect()->back();
        }

        if ($EmCurso > 0) {
               
                app('flasher')->addError('Não são permitidas duas inscrições no mesmo semestre!!!');
                return redirect()->back();               
        }

           
       // Busca todos os cronogramas ativos ligados à pessoa
        $ligados = DB::table('cronograma AS c')
            ->leftJoin('membro AS m', 'c.id', 'm.id_cronograma')
            ->leftJoin('grupo AS g', 'c.id_grupo', 'g.id')
            ->leftJoin('associado AS a', 'm.id_associado', 'a.id')
            ->leftJoin('pessoas AS p', 'a.id_pessoa', 'p.id')
            // ->leftJoin('tipo_tratamento AS tt', 'c.id_tipo_tratamento', 'tt.id')
            ->where('p.id', $id_pessoa)
            ->whereNull('c.data_fim')
            ->select('g.nome', 'c.id', 'c.dia_semana', 'c.h_inicio', 'c.h_fim')
            ->get();

        //dd($ligados);

        // Verifica sobreposição
        $conflito = $ligados->first(function ($existente) use ($novoCurso) {
            $existenteInicio = Carbon::parse($existente->h_inicio);
            $existenteFim = Carbon::parse($existente->h_fim);
            $novoInicio = Carbon::parse($novoCurso->h_inicio);
            $novoFim = Carbon::parse($novoCurso->h_fim);

            return $existente->dia_semana == $novoCurso->dia_semana &&
                $existenteInicio->lt($novoFim) &&
                $existenteFim->gt($novoInicio);
        });

        if ($conflito) {
            
            app('flasher')->addError("O aluno trabalha ou estuda no grupo '{$conflito->nome}' no mesmo dia e horário.");
            return redirect()->back();
        }


        DB::table('inscricao AS i')
                ->insert([
                    'id_pessoa' => $id_pessoa,
                    'data_inscricao' => $now,
                    'status' => 1
                ]);

        // Pega o último ID gerado pela sequence inscricao_id_seq
        $idInscricao = DB::getPdo()->lastInsertId('inscricao_id_seq');

        // Inserir no historico_inscricao
        DB::table('historico_inscricao')->insert([
            'id_inscricao'          => $idInscricao,
            'id_cronograma_anterior'=> $crono,
            'id_cronograma_novo'    => $crono,
            'data_alteracao'        => Carbon::now(),
            'motivo'                => null,
        ]);

        app('flasher')->addSuccess("A inscrição foi incluida com sucesso!!!");

        return redirect('/gerenciar-inscricao');

    }

    public function trocar($idi, $idc)
    {

        $aluno = DB::table('inscricao AS i')
                    ->leftJoin('pessoas AS p', 'i.id_pessoa', 'p.id')                    
                    ->leftJoin('historico_inscricao AS hi', 'i.id', 'hi.id_inscricao')
                    ->leftJoin('cronograma AS c', 'hi.id_cronograma_novo', 'c.id' )
                    ->leftJoin('grupo AS g', 'c.id_grupo', 'g.id')
                    ->leftJoin('tipo_tratamento AS tt', 'c.id_tipo_tratamento', 'tt.id')
                    ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
                    ->leftJoin('tipo_modalidade AS tm', 'c.id_tipo_modalidade', 'tm.id')
                    ->select(
                        'i.id AS idi',
                        'c.id AS idc',
                        'c.id_tipo_tratamento AS id_tratamento',
                        'c.h_inicio',
                        'c.h_fim',
                        'tt.descricao AS desct',
                        'tt.sigla AS siglat',
                        'ts.sigla AS siglas',
                        'tm.nome AS nomem',
                        'p.nome_completo'                     
                    )
                    ->where('g.id_tipo_grupo', 2)
                    ->where('i.id', $idi)
                    ->where('hi.id_cronograma_novo', $idc)
                    ->get();

        $turma = DB::table('cronograma as c')
                    ->leftJoin('historico_inscricao AS hi', 'c.id', 'hi.id_cronograma_novo')
                    ->leftJoin('inscricao AS i', 'hi.id_inscricao', 'i.id')
                    ->leftJoin('pessoas AS p', 'i.id_pessoa', 'p.id' )
                    ->leftJoin('grupo AS g', 'c.id_grupo', 'g.id')
                    ->leftJoin('setor AS s', 'g.id_setor', 's.id')
                    ->leftJoin('tipo_dia AS td', 'c.dia_semana', 'td.id')
                    ->leftJoin('tipo_tratamento AS tt', 'c.id_tipo_tratamento', 'tt.id')
                    ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
                    ->leftJoin('tipo_modalidade AS tm', 'c.id_tipo_modalidade', 'tm.id')
                    ->leftJoin('tipo_observacao_reuniao AS obs', 'c.observacao', 'obs.id')
                    ->leftJoin('salas AS sa', 'c.id_sala', 'sa.id')
                    ->select(
                        'c.id AS idc',
                        'i.id AS idi',
                        'g.id AS idg',
                        'g.nome AS nomeg',
                        'sa.numero AS sala',
                        'c.id_tipo_tratamento AS id_tratamento',
                        'c.h_inicio',
                        'c.h_fim',
                        'obs.descricao AS observacao',
                        'td.nome AS dia_semana',
                        's.sigla',
                        'c.max_atend',
                        'tt.descricao AS desct',
                        'tt.sigla AS siglat',
                        'ts.sigla AS siglas',
                        'tm.nome AS nomem',
                        'p.nome_completo',
                        DB::raw('c.max_atend - count(hi.id_cronograma_novo) AS vaga')                        
                    )
                    ->where('g.id_tipo_grupo', 2)
                    ->groupBy('c.id', 'i.id', 'td.id', 'g.id', 's.sigla', 'td.nome', 'tt.descricao', 'tt.sigla', 'ts.sigla', 'tm.nome', 'obs.descricao', 'sa.numero', 'p.nome_completo')
                    ->orderBy('td.id', 'asc')
                    ->get()
                    ->values();     // reindexa os itens do array

        $turmasAgrupadas = $turma->groupBy('modalidade')
        ->map(function ($grupoModalidade) {
        // dd($grupoModalidade);
            return $grupoModalidade->groupBy(function ($item) {
                
                return $item->desct . ' - ' . $item->siglas;
                
            });
        });
        

        return view('/inscricao.trocar-inscricao', compact('turmasAgrupadas', 'turma', 'aluno')); 


    }


     public function update(Request $request, $idi, $idc)
    {
        //dd($request->input('curso'), $idi, $idc);
        DB::table('historico_inscricao AS hi')
            ->insert([
                'id_cronograma_novo'     => $request->input('curso'),
                'id_cronograma_anterior' => $idc,
                'id_inscricao'           => $idi,
                'data_alteracao'         => Carbon::now(),
                'motivo'                 => null,
            ]);

        app('flasher')->addSuccess('Alteração de turma realizada com sucesso');

        return redirect('/gerenciar-inscricao');


    }

    public function visualizar($idi)
    {
      
         $inscricao = DB::table('inscricao AS i')
            ->select(
                'i.id AS idi',
                'p.nome_completo',
                'p.cpf',
                'cro.id AS idr',
                'gr.nome AS nomeg',
                'cro.dia_semana AS idd',
                'cro.id_sala',
                'cro.id_tipo_tratamento',
                'cro.id_tipo_semestre',
                'cro.h_inicio',
                'td.nome AS nomed',
                'cro.h_fim',
                'cro.max_atend',
                'cro.max_trab',
                'cro.data_inicio',
                'cro.data_fim',
                'gr.status_grupo AS idst',
                'tt.descricao AS trnome',
                'tt.sigla AS trsigla',
                's.sigla as stsigla',
                'tse.sigla as sesigla',
                'sa.numero',
                'tl.sigla AS sloc',
                't.descricao AS observacao',
                'tm.nome as nmodal',
                'ts.nome as nsemana',
                'tt.descricao as tipo',
                'tt.id as idt',
                'tt.id_tipo_grupo',
                'tsi.tipo AS tp_si',
                'tmsp.motivo AS tp_msi',
                DB::raw("(CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END) as status")
            )
            ->leftJoin('pessoas AS p', 'i.id_pessoa', 'p.id' )
            ->leftJoin('historico_inscricao AS hi', 'i.id', 'hi.id_inscricao')
            ->leftJoin('cronograma AS cro', 'hi.id_cronograma_novo', 'cro.id' )
            ->leftJoin('tipo_tratamento AS tt', 'cro.id_tipo_tratamento', 'tt.id')
            ->leftJoin('tipo_observacao_reuniao AS t', 'cro.observacao', 't.id')
            ->leftJoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor AS s', 'gr.id_setor', 's.id')
            ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id')
            ->leftJoin('tipo_modalidade AS tm', 'cro.id_tipo_modalidade', 'tm.id')
            ->leftJoin('tipo_semana AS ts', 'cro.id_tipo_semana', 'ts.id')
            ->leftJoin('tipo_semestre AS tse', 'tt.id_semestre', 'tse.id')
            ->leftJoin('tipo_status_inscricao AS tsi', 'i.status', 'tsi.id')
            ->leftJoin('tipo_localizacao AS tl', 'sa.id_localizacao', 'tl.id')
            ->leftJoin('tipo_motivo_status_pessoa AS tmsp', 'i.motivo', 'tmsp.id')
            ->where('i.id', $idi)
            ->get();

            return view('/inscricao.visualizar', compact('inscricao'));

    }

    public function inativar(Request $request, $idi)
    {
    
        $mot_inativa = $request->input('motivo_inat');

        DB::table('inscricao')
            ->where('id', $idi)
            ->update([
                'status' => 3,
                'motivo' => $mot_inativa
            ]);

        app('flasher')->addSuccess("A inscrição foi inativada!");

        return redirect('/gerenciar-inscricao');

    }


    public function destroy($idi)
    {

        $presenca = DB::table('presenca_aula AS pa')->where('pa.id_inscricao', $idi)->count();

        if ($presenca > 0)
        {
            app('flasher')->addError('Exclusão não permitida: existe uma presença registrada!');

            return redirect('/gerenciar-inscricao');
        }else{

        DB::table('inscricao')->where('id', $idi)->delete();

        app('flasher')->addSuccess('A inscrição foi excluida!');

        return redirect('/gerenciar-inscricao');
        }

    }


}
