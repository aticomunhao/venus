<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

use function Laravel\Prompts\select;

class GerenciarAtendimentoController extends Controller
{

    ////GERENCIAR ATENDIMENTOS DO DIA

    public function index(Request $request)
    {
        try{

        $now =  Carbon::now()->format('Y-m-d');

        DB::table('atendimentos')
            ->where('status_atendimento', '<', 5)
            ->where('dh_chegada', '<', $now)
            ->update([
                'status_atendimento' => 6
            ]);


        $atende = DB::select("select
                    m.id_associado
                    from membro m
                    left join associado ad on (m.id_associado = ad.id)
                    left join pessoas p on (ad.id_pessoa = p.id)
                    where m.id_funcao = 6
                    ");


        $lista = DB::table('atendimentos AS at')
            ->select('at.id as ida', 'p1.id as idas', 'p.nome_completo as nm_3', 'at.status_atendimento', 'at.id_prioridade', 'at.dh_chegada', 'tx.tipo', 'tp.descricao as prdesc', 'p1.nome_completo as nm_1', 'p2.nome_completo as nm_2', 'p3.nome_completo as nm_4', 'sl.numero as nr_sala', 'ts.descricao')
            ->leftJoin('associado as ass', 'at.id_atendente', 'ass.id')
            ->leftJoin('associado as ass1', 'at.id_atendente_pref', 'ass1.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('pessoas as p3', 'ass1.id_pessoa', 'p3.id')
            ->leftJoin('tp_sexo as tx', 'at.pref_tipo_atendente', 'tx.id')
            ->leftJoin('tipo_prioridade as tp', 'at.id_prioridade', 'tp.id')
            ->leftJoin('pessoas as p1', 'at.id_assistido', 'p1.id')
            ->leftJoin('pessoas as p2', 'at.id_representante', 'p2.id')
            ->leftJoin('salas as sl', 'at.id_sala', 'sl.id')
            ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id');


        $data_inicio = $request->input('dt_ini', Carbon::today()->toDateString());

        // Filtra pela data de início, se fornecida, caso contrário, usa a data atual


        $data_inicio = $request->dt_ini;

        $assistido = $request->assist;

        $situacao = $request->status;


        if ($request->input('dt_ini')) {

            $lista->whereDate('dh_chegada', $request->input('dt_ini'));
        } elseif ($request->assist or $request->status) {
        } else {
            $lista->whereDate('dh_chegada', Carbon::today()->toDateString());
        }

        if ($request->assist) {
            $lista->where('p1.nome_completo', 'ilike', "%$request->assist%");
        }

        if ($request->status) {
            $lista->where('at.status_atendimento', $request->status);
        }



        $lista = $lista->orderby('at.status_atendimento', 'ASC')->orderBy('at.id_prioridade', 'ASC')->orderby('at.dh_chegada', 'ASC');

        $lista = $lista->paginate(50);

        $contar = $lista->count('at.id');

        $st_atend = DB::select("select
        s.id,
        s.descricao
        from tipo_status_atendimento s
        ");



        return view('/recepcao-AFI/gerenciar-atendimentos', compact('lista', 'st_atend', 'contar', 'atende', 'data_inicio', 'assistido', 'situacao', 'now'));
    }

    catch(\Exception $e){

        $code = $e->getCode( );
            return view('tratamento-erro.erro-inesperado', compact('code'));
                }

        }

    ///CRIAR UM NOVO ATENDIMENTO

    public function create()
    {
        try{

        $hoje = Carbon::today();
        $lista = DB::select("select
        p.id as pid,
        p.ddd,
        p.celular,
        p.nome_completo,
        m.id_associado
        from pessoas p
        left join membro m on (p.id = m.id_associado)
        group by pid, m.id_associado
        order by nome_completo
        ");

        $priori = DB::select("select
        pr.id as prid,
        pr.descricao as prdesc,
        pr.sigla as prsigla
        from tipo_prioridade pr
        order by prid
        ");

        $afi = DB::table('atendente_dia as at')
            ->leftJoin('associado as a', 'at.id_associado', '=', 'a.id')
            ->leftJoin('pessoas as p', 'a.id_pessoa', '=', 'p.id')
            ->leftJoin('membro as m', 'm.id', '=', 'a.id')
            ->whereNull('at.dh_fim')
            ->where('at.dh_inicio','>', $hoje)
            ->select(
                'm.id_associado',
                'p.id as idp',
                'p.nome_completo as nm_1',
                'p.ddd',
                'p.celular',
                'm.id_associado as ida',
            )
            ->get();



        $sexo = DB::select("select
        id,
        tipo,
        sigla
        from tp_sexo
        ");

        $parentes = DB::select("select
        id,
        nome
        from tp_parentesco
        ");

        return view('/recepcao-AFI/incluir-atendimento', compact('afi', 'priori', 'sexo', 'parentes', 'lista'));
    }

 catch(\Exception $e){

    $code = $e->getCode( );
    return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }

    public function store(Request $request)
    {

        try{
        $usuario = session()->get('usuario.id_pessoa');

        $now = Carbon::now()->format('Y-m-d H:m:s');

        $dt_hora = Carbon::now();

        $assistido = $request->assist;

        $resultado = DB::table('atendimentos')->where('status_atendimento', '<', 5)->where('id_assistido', $assistido)->count();

        //dd($resultado);
        if ($resultado > 0) {

            app('flasher')->addError('Não é permitido duplicar o cadastro do assistido.');

            return redirect('/gerenciar-atendimentos');
        };

        $menor = isset($request->menor) ? 1 : 0;

        //dd($menor);

        DB::table('atendimentos AS atd')->insert([
            'dh_chegada' => ($dt_hora->toDateTimeString() . PHP_EOL),
            'id_usuario' => $usuario,
            'id_assistido' => $request->input('assist'),
            'id_representante' => $request->input('repres'),
            'parentesco' => $request->input('parent'),
            'id_atendente_pref' => $request->input('afi_p'),
            'pref_tipo_atendente' => $request->input('tipo_afi'),
            'id_prioridade' => $request->input('priori'),
            'menor_auto' => $menor,
            'status_atendimento' => 1
        ]);

        DB::table('historico_venus')->insert([
            'id_usuario' => $usuario,
            'data' => $now,
            'fato' => 5,
            'id_ref' => $request->input('assist')
        ]);


        app('flasher')->addSuccess('O cadastro do atendimento foi realizado com sucesso.');

        return redirect('/gerenciar-atendimentos');
    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
            DB::rollBack();
	    return redirect()->back();

        }}

    ////INCLUI UMA NOVA PESSOA
    public function SetPessoa(Request $request)
    {

        try{
        DB::table('pessoas AS p')->insert([

            'nome_completo' => $request->input('nomepes'),

        ]);


        app('flasher')->addSuccess('O cadastro de pessoa foi realizado com sucesso.');

        return redirect('/gerenciar-atendimentos');
    }

    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
        DB::rollBack();
    return redirect()->back();
            }

        }
    public function cancelar($ida)
    {
        try{
        $status = DB::table('atendimentos AS a')->select('status_atendimento')->where('id', '=', $ida)->value('status_atendimento');

        if ($status > 1) {

            app('flasher')->addError('Somente é permitido "Cancelar" atendimentos no status "Aguardando atendimento".');
            return redirect('/gerenciar-atendimentos');
        } else {

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 6
            ]);

            app('flasher')->addSuccess('O status do atendimento foi alterado para "Cancelado".');
            return redirect('/gerenciar-atendimentos');
        }
    }
    catch(\Exception $e){
        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
        DB::rollBack();
    return redirect()->back();
            }

        }
    ////PREPARA PARA EDITAR
    public function edit($ida)
    {
        try {
        $status = DB::table('atendimentos AS a')->select('status_atendimento')->where('id', '=', $ida)->value('status_atendimento');

        if ($status > 1) {

            app('flasher')->addError('Somente são permitidas alterações quando o status é "Aguardando atendimento".');
            return redirect('/gerenciar-atendimentos');
        } else {

            $result = DB::table('atendimentos AS at')
                ->where('at.id', $ida)
                ->select('at.id AS ida', 'p1.id as idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'at.id_atendente_pref AS iap', 'p3.nome_completo as nm_3', 'at.id_atendente as idaf', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente AS pta', 'ts.descricao', 'tp.nome',  'at.parentesco', 'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo', 'at.id_prioridade', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla')
                ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                ->leftJoin('membro AS m', 'at.id_atendente', 'm.id_associado')
                ->leftJoin('pessoas AS p', 'm.id_associado', 'p.id')
                ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                ->leftjoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
                ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id')
                ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                ->get();

            $lista = DB::table('pessoas')->get();

            //dd($lista);

            $afi = DB::select("select
                    p.id as iaf,
                    p.nome_completo as nm_afi,
                    p.ddd,
                    p.celular,
                    a.id_pessoa,
                    a.id as ida
                    from associado a
                    left join pessoas p on (a.id_pessoa = p.id)
                    order by nm_afi
                    ");

            $sexo = DB::select("select
                    id as idsx,
                    tipo,
                    sigla
                    from tp_sexo
                    ");

            $pare = DB::select("select
                    id as idp,
                    nome
                    from tp_parentesco
                    ");

            $priori = DB::select("select
                    pr.id as prid,
                    pr.descricao as prdesc,
                    pr.sigla as prsigla
                    from tipo_prioridade pr
                    order by id
                    ");


            return view('/recepcao-AFI/editar-atendimento', compact('result', 'priori', 'sexo', 'pare', 'afi', 'lista'));
        }
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }

        }
    ///////ALTERA UM ATENDIMENTO
    public function altera(Request $request, $ida)
    {
        try{
        $afi = DB::table('associado')->where('id_pessoa', $request->input('afi_p'))->first();


        DB::table('atendimentos AS at')->where('at.id', $ida)->update([

            'id_assistido' => $request->input('assist'),
            'id_representante' => $request->input('repres'),
            'parentesco' => $request->input('parent'),
            'id_atendente_pref' => $afi ? $afi->id : $request->input('afi_p'),
            'pref_tipo_atendente' => $request->input('tipo_afi'),
            'id_prioridade' => $request->input('priori')

        ]);


        app('flasher')->addSuccess('O cadastro de pessoa foi alterado com sucesso.');

        return redirect('/gerenciar-atendimentos');
    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
        DB::rollBack();
    return redirect()->back();
            }

        }

    public function visual($idas)
    {

    try{
        $result = DB::table('atendimentos AS at')
            ->where('p1.id', $idas)
            ->select('at.id AS ida', 'at.pref_tipo_atendente', 'p1.dt_nascimento', 'at.dh_chegada',  'at.dh_fim', 'at.dh_inicio', 'at.id_assistido', 'at.id_representante', 'at.id_atendente_pref', 'at.id_atendente', 'at.parentesco', 'tdd.descricao AS ddd', 'p1.celular', 'p1.id AS idas', 'p1.nome_completo AS nm_1',  'p2.nome_completo as nm_2',  'p3.id AS idp', 'p3.nome_completo as nm_3',  'p4.nome_completo as nm_4',  'ts.descricao', 'tp.nome',   'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo')
            ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
            ->leftJoin('membro AS m', 'at.id_atendente', 'm.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftjoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
            ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id')
            ->leftJoin('tp_ddd AS tdd', 'p1.ddd', 'tdd.id')
            ->orderBy('dh_chegada', 'DESC')
            ->get();




        return view('/recepcao-AFI/visualizar-atendimentos', compact('result'));
    }

    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }

        }


    //===============================================================//
    //        AQUI COMEÇA O GERENCIAMENTO DOS ATENDENTES DO DIA
    //==============================================================//

    public function atendente_dia(Request $request)
    {
        try{

        $now = Carbon::now()->format('Y-m-d');

        //dd($now);

        $atende = DB::table('atendente_dia as atd')
            ->select('atd.id AS nr', 'atd.id AS idatd', 'atd.id_associado AS idad', 'atd.id_sala', 'atd.dh_inicio', 'atd.dh_fim', 'p.nome_completo AS nm_4',  'p.id', 'tsp.tipo', 'g.id AS idg', 'g.nome AS nomeg', 's.id AS ids', 's.numero AS nm_sala', 'p.status')
            ->leftJoin('associado as a', 'atd.id_associado', '=', 'a.id')
            ->leftjoin('pessoas AS p', 'a.id_pessoa', 'p.id')
            ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
            ->leftJoin('salas AS s', 'atd.id_sala', 's.id')
            ->leftJoin('grupo AS g', 'atd.id_grupo', 'g.id');


        $data = $request->data;

        $grupo = $request->grupo;

        $atendente = $request->atendente;

        $status = $request->status;

        // dd($status);

        if ($request->data) {
            $atende->where('atd.dh_inicio', '=', $request->data);
        }

        if ($request->grupo) {
            $atende->where('g.id', '=', $request->grupo);
        }

        if ($request->atendente) {
            $atende->where('p.nome_completo', 'ilike', "%$request->atendente%");
        }

        if ($request->status) {
            $atende->where('p.status', '=', intval($request->status));
        }


        $atende = $atende->orderby('nr', 'DESC')->orderby('nm_sala', 'ASC')->distinct('nr')->get();

        //dd($atende);

        $situacao = DB::table('tipo_status_pessoa')
            ->select('id', 'tipo')
            ->get();

        $grupo = DB::table('grupo')
            ->select('id', 'nome')
            ->where('id_tipo_grupo', 3)
            ->where('status_grupo', 1)
            ->orderBy('id')
            ->get();

        //dd($now, $atende);


        return view('/recepcao-AFI/gerenciar-atendente-dia', compact('atende', 'atendente', 'status', 'situacao', 'grupo', 'data', 'now'));
    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
            DB::rollBack();
	    return redirect()->back();
            }

        }
    ////PREPARA INFORMAÇÕES DO FORMULÁRIO DE EDIÇÃO DA SALA



    public function editar_afi($idatd)
    {
        try{
        $now = Carbon::today();
        $no = Carbon::today()->addDay(1);

        $atende = DB::table('atendente_dia as atd')
            ->select('atd.id AS nr', 'atd.id AS idatd', 'atd.id_associado AS idad', 'atd.id_sala', 'atd.dh_inicio', 'atd.dh_fim', 'p.nome_completo AS nm_4',  'p.id', 'tsp.tipo', 'g.id AS idg', 'g.nome AS nomeg', 's.id AS ids', 's.numero AS nm_sala', 'p.status')
            ->leftJoin('associado as a', 'atd.id_associado', '=', 'a.id')
            ->leftjoin('pessoas AS p', 'a.id_pessoa', 'p.id')
            ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
            ->leftJoin('salas AS s', 'atd.id_sala', 's.id')
            ->leftJoin('grupo AS g', 'atd.id_grupo', 'g.id')
            ->where('atd.id', $idatd)
            ->get();

        $st_atend = DB::select("select
        tsp.id,
        tsp.tipo
        from tipo_status_pessoa tsp
        ");

        $situacao = DB::table('tipo_status_pessoa')
            ->select('id', 'tipo')
            ->get();

        $grupo = DB::table('grupo')
            ->select('id', 'nome')
            ->where('id_tipo_grupo', 3)
            ->where('status_grupo', 1)
            ->orderBy('nome')
            ->get();


        $salaAtendendo = DB::table('atendente_dia AS atd')
            ->leftjoin('associado AS a', 'atd.id_associado', 'a.id',)
            ->where('dh_inicio', '>=', $now)
            ->where('dh_inicio', '<', $no)
            ->where('dh_fim', '=', null)
            ->pluck('id_sala');

        $salaAFE = DB::table('atendimentos')
            ->where('dh_marcada', '>=', $now)
            ->where('dh_marcada', '<', $no)
            ->where('status_atendimento', 7)
            ->pluck('id_sala');




        $sala = DB::table('salas AS s')
            ->select('s.id', 's.numero')
            ->where('s.id_finalidade', 2)
            ->where('s.status_sala', 1)
            ->whereNotIn('id', $salaAtendendo)
            ->whereNotIn('id', $salaAFE)
            ->orderBy('numero')
            ->get();

        // dd($sala);



        return view('/recepcao-AFI/editar-atendente-dia', compact('atende', 'st_atend', 'grupo', 'sala'));
    }
    catch(\Exception $e){


        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
        DB::rollBack();
    return redirect()->back();
            }}
    //// SALVAR EM BANCO A EDIÇÃO DA SALA DO AFI

    public function update_afi(Request $request, $idatd)
    {
        try{
        $now = Carbon::now()->format('Y-m-d');


        $sala = $request->sala;



        $sala_dia = DB::table('atendente_dia AS atd')->where('atd.id_sala', $request->sala)->where('atd.dh_inicio', $now)->count('atd.id');

        if ($sala_dia == 0) {

            DB::table('atendente_dia AS atd')->where('id', $idatd)->update([
                'id_sala' => $request->input('sala')
            ]);


            app('flasher')->addSuccess('A sala foi alterada com sucesso.');

            return redirect('/gerenciar-atendente-dia');
        } else {

            app('flasher')->addError('A sala está ocupada.');

            return redirect('/gerenciar-atendente-dia');
        }

        app('flasher')->addError('Saiu aqui deu erro.');

        return redirect('/gerenciar-atendente-dia');
    
    }
    catch(\Exception $e){


        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
        DB::rollBack();
    return redirect()->back();
                }
            }
    
    //////GERENCIAR/DEFINIR OS AFI E SALAS DE ATENDIMENTO DO DIA

    public function definir_sala(Request $request)
    {

        try{
        $now = Carbon::today();
        $no = Carbon::today()->addDay(1);

        $aten = DB::table('atendente_dia AS atd')
            ->leftjoin('associado AS a', 'atd.id_associado', 'a.id',)
            ->where('dh_inicio', '>=', $now)
            ->where('dh_inicio', '<', $no)
            ->where('dh_fim', '=', null)
            ->pluck('id_associado');


        //dd($aten);

        $atende = DB::table('membro AS m')
            ->distinct('m.id_associado')
            ->select('m.id AS idat', 'm.id_associado AS ida', 'p.nome_completo AS nm_4',  'p.id AS pid', 'tsp.tipo')
            ->leftjoin('associado AS a', 'm.id_associado', 'a.id')
            ->leftjoin('pessoas AS p', 'a.id_pessoa', 'p.id')
            ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
            ->where('p.status', 1)
            ->whereNotIn('m.id_associado', $aten);


        //$grupo = $request->grupo;

        $atendente = $request->atendente;

        $status = $request->status;


        //if ($request->grupo){
        //$atende->where('g.id', '=', $request->grupo);
        //}

        if ($request->atendente) {
            $atende->where('m.id_associado', $request->atendente);
        }

        if ($request->status) {
            $atende->where('p.status', $request->status);
        }


        $atende = $atende->orderby('m.id_associado', 'ASC', 'nm_4', 'ASC')->paginate(50);

        //dd($atende, $aten);

        $st_atend = DB::select("select
        tsp.id,
        tsp.tipo
        from tipo_status_pessoa tsp
        ");

        $situacao = DB::table('tipo_status_pessoa')
            ->select('id AS ids', 'tipo')
            ->get();

        $grupo = DB::table('grupo AS g')
            ->select('id', 'nome')
            ->where('id_tipo_grupo', 3)
            ->where('data_fim', null)
            ->orderBy('nome')
            ->get();

        foreach ($atende as $key => $lista) {

            $result = DB::table('membro AS m')
                ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
                ->leftJoin('grupo AS g', 'cro.id_grupo', 'g.id')
                ->where('m.id_associado', '=', $lista->ida) // Filtro para o ID de associado atual
                ->where('g.id_tipo_grupo', 3)
                ->whereNull('g.data_fim')
                ->select('m.id_associado', 'cro.id_grupo', 'g.nome AS gnome')
                ->groupBy('m.id_associado', 'cro.id_grupo', 'g.nome')
                ->get();
            $lista->grup = $result;
        }

        $salaAtendendo = DB::table('atendente_dia AS atd')
            ->leftjoin('associado AS a', 'atd.id_associado', 'a.id',)
            ->where('dh_inicio', '>=', $now)
            ->where('dh_inicio', '<', $no)
            ->where('dh_fim', '=', null)
            ->pluck('id_sala');

        $salaAFE = DB::table('atendimentos')
            ->where('dh_marcada', '>=', $now)
            ->where('dh_marcada', '<', $no)
            ->whereIn('status_atendimento', [1, 7])
            ->pluck('id_sala');





        $sala = DB::table('salas AS s')
            ->select('s.id', 's.numero')
            ->where('s.id_finalidade', 2)
            ->where('s.status_sala', 1)
            ->whereNotIn('id', $salaAtendendo)
            ->whereNotIn('id', $salaAFE)
            ->orderBy('numero')
            ->get();


        // dd($atende);

        return view('/recepcao-AFI/incluir-atendente-dia', compact('atende', 'st_atend',  'situacao', 'grupo', 'sala'));
    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
            DB::rollBack();
	    return redirect()->back();
                }
        }
    ////SALVA O AFI DO DIA E SALA

    public function salva_afi(Request $request, $ida)
    {
        try{
        $sala = $request->sala;

        $now = Carbon::now();

        //$atendente = DB::table('atendentes AS a')->select('a.id AS ida')->where('id_pessoa', $idat)->get();

        $verif = DB::table('atendente_dia AS atd')->where('dh_inicio', $now)->where('id_sala', $sala)->count();

        //dd($verif);


        if ($verif == 0) {

            DB::table('atendente_dia AS atd')->insert([
                'id_sala' => $request->input('sala'),
                'id_grupo' => $request->input('grupo'),
                'id_associado' => $ida,
                'dh_inicio' => $now
            ]);

            app('flasher')->addSuccess('O atendente foi incluido e a sala vinculada.');

            return redirect()->back();
        } else {

            app('flasher')->addError('Essa sala está ocupada.');

            return redirect()->back();
        }
    }


    catch(\Exception $e){
        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
            DB::rollBack();
	    return redirect()->back();
                }
        }

    ////EDITAR A SALA DE TRABALHO DO ATENDENTE

    public function gravar_sala(Request $request, $ida)
    {
        try{
        $now = Carbon::now()->format('d/m/Y');

        $sit_afi = DB::table('atendente_dia AS atd')->select('id_associado')->where('atd.dh_inicio', $now)->count();



        if ($sit_afi > 0) {

            app('flasher')->addWarning('O atendente está em outra sala.');

            return redirect()->back();
        } else {

            DB::table('atendente_dia AS atd')->where('atd.id', $ida)->insert([

                'id_associado' => $request->input('atendente'),
                'id_sala' => $request->input('sala'),
                'dh_inicio' => $now
            ]);
        }
    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
            DB::rollBack();
	    return redirect()->back();
                }
        }
    ///APAGAR O ATENDENTE DA LISTA DIÁRIA

    public function delete($idatd, $idad)
    {
        try{
        $usuario = session()->get('usuario.id_pessoa');

        $now = Carbon::now()->format('Y/m/d');

        DB::table('atendente_dia AS atd')->where('id', $idatd)->delete();

        DB::table('historico_venus')->insert([
            'id_usuario' => $usuario,
            'data' => $now,
            'fato' => 35,
            'id_ref' => $idad
        ]);

        app('flasher')->addSuccess('O atendente foi excluído.');

        return redirect('/gerenciar-atendente-dia');
    }
    catch(\Exception $e){

        $code = $e->getCode( );
            return view('tratamento-erro.erro-inesperado', compact('code'));
                }
        }
    public function finaliza_afi(string $id)
    {
        try{
        $now = Carbon::now();
        DB::table('atendente_dia')->where('id', $id)->update(['dh_fim' => $now]);

        app('flasher')->addSuccess('Turno Finalizado com Sucesso.');
        return redirect()->back();
    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
        DB::rollBack();
    return redirect()->back();
            }
        }}
