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

    public function index(Request $request){

        $now =  Carbon::now()->format('Y-m-d');

        DB::table('atendimentos')
                    ->where('status_atendimento', '<', 5)
                    ->where('dh_chegada', '<', $now)
                    ->update([
                    'status_atendimento' => 6
        ]);


        $atende = DB::select("select
                    p.id as idatt,
                    p.nome_completo as nm_1,
                    p.ddd,
                    p.celular,
                    a.id_pessoa
                    from atendentes a
                    left join pessoas p on (a.id_pessoa = p.id)
                    ");

        $lista = DB::table('atendimentos AS at')
                    ->select('at.id AS ida', 'p1.id AS idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'at.id_atendente_pref AS iap', 'p3.nome_completo as nm_3', 'at.id_atendente', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente AS pta', 'ts.descricao', 'tx.tipo', 'pa.nome', 'att.id as idatt','at.id_prioridade', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'at.status_atendimento', 's.numero AS nr_sala' )
                    ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id')
                    ->leftJoin('pessoas AS p', 'att.id_pessoa', 'p.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftjoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                    ->leftJoin('salas AS s', 'at.id_sala', 's.id');

        $data_inicio = $request->dt_ini;

        $assistido = $request->assist;

        $situacao = $request->status;


        if ($request->dt_ini){
            $lista->where('at.dh_chegada', '>=', $request->dt_ini);
        }

        if ($request->assist){
            $lista->where('p1.nome_completo', 'ilike', "%$request->assist%");
        }

        if ($request->status){
            $lista->where('at.status_atendimento', $request->status);
        }


        $lista = $lista->orderby('at.status_atendimento', 'ASC')->orderBy( 'at.id_prioridade', 'ASC')->orderby('at.dh_chegada', 'ASC')->paginate(50);

//dd($lista);

        $contar = $lista->count('at.id');

        $st_atend = DB::select("select
        s.id,
        s.descricao
        from tipo_status_atendimento s
        ");




        return view ('/recepcao-AFI/gerenciar-atendimentos', compact('lista', 'st_atend', 'contar', 'atende', 'data_inicio', 'assistido', 'situacao'));


    }

    ///CRIAR UM NOVO ATENDIMENTO

    public function create(){

        $lista = DB::select("select
        p.id as pid,
        p.ddd,
        p.celular,
        p.nome_completo,
        a.id_pessoa
        from pessoas p
        left join atendentes a on (p.id = a.id_pessoa)
        group by pid, a.id_pessoa
        order by nome_completo
        ");

        $priori = DB::select("select
        pr.id as prid,
        pr.descricao as prdesc,
        pr.sigla as prsigla
        from tipo_prioridade pr
        order by prid
        ");

        $afi = DB::select("select
        p.id as idatt,
        p.nome_completo as nm_1,
        p.ddd,
        p.celular,
        a.id_pessoa
        from atendentes a
        left join pessoas p on (a.id_pessoa = p.id)
        ");

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

        return view ('/recepcao-AFI/incluir-atendimento', compact('afi', 'priori', 'sexo', 'parentes', 'lista'));


    }



    public function store(Request $request){

        $usuario = session()->get('usuario.id_pessoa');

        $now = Carbon::now()->format('Y-m-d H:m:s');

        $dt_hora = Carbon::now();

        $assistido = $request->assist;

        $resultado = DB::table('atendimentos')->where('status_atendimento', '<', 5)->where('id_assistido', $assistido)->count();

        //dd($resultado);
        if ($resultado > 0){

            app('flasher')->addError('Não é permitido duplicar o cadastro do assistido.');

            return redirect ('/gerenciar-atendimentos');

        };

            DB::table('atendimentos AS atd')->insert([
                    'dh_chegada'=> ($dt_hora->toDateTimeString() . PHP_EOL),
                    'id_usuario'=> $usuario,
                    'id_atendente_usuario_tem_perfil'=>2,
                    'id_assistido'=>$request->input('assist'),
                    'id_representante'=>$request->input('repres'),
                    'parentesco'=>$request->input('parent'),
                    'id_atendente_pref'=>$request->input('afi_p'),
                    'pref_tipo_atendente'=>$request->input('tipo_afi'),
                    'id_prioridade'=>$request->input('priori'),
                    'status_atendimento'=> 1
                    ]);

            DB::table('historico_venus')->insert([
                    'id_usuario' => $usuario,
                    'data' => $now,
                    'fato' => 5,
                    'id_ref' => $request->input('assist')
                    ]);


        app('flasher')->addSuccess('O cadastro do atendimento foi realizado com sucesso.');

        return redirect ('/gerenciar-atendimentos');

    }


    ////INCLUI UMA NOVA PESSOA
    public function SetPessoa(Request $request){


       DB::table('pessoas AS p')->insert([

        'nome_completo'=>$request->input('nomepes'),

       ]);


        app('flasher')->addSuccess('O cadastro de pessoa foi realizado com sucesso.');

        return redirect ('/gerenciar-atendimentos');

    }


    // public function sobeStatus($ida){

    //     $data = date("Y-m-d H:i:s");


    //     $sta_at = DB::table('atendimentos AS a')->where('id','=', $ida)->value('a.status_atendimento');


    //     if ($sta_at == 1){

    //         app('flasher')->addError('Utilize a opção de vincula o Atendente Fraterno ao atendimento.');
    //         return redirect ('/gerenciar-atendimentos');
    //     }
    //     elseif ($sta_at > 4){

    //         app('flasher')->addError('O atendimento foi cancelado ou finalizado e não pode ser alterado.');
    //         return redirect ('/gerenciar-atendimentos');
    //     }
    //     elseif ($sta_at == 2){

    //         app('flasher')->addError('Somente o Atendente Fraterno pode alterar este status');
    //         return redirect ('/gerenciar-atendimentos');

    //     }elseif ($sta_at == 3){

    //         DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
    //             'status_atendimento' => 4,
    //             'dh_inicio' => $data
    //         ]);

    //     }elseif ($sta_at == 4){

    //         DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
    //             'status_atendimento' => 5,
    //             'dh_fim' => $data
    //         ]);

    //     }

    //     app('flasher')->addSuccess('O satus foi alterado com sucesso.');
    //     return redirect ('/gerenciar-atendimentos');

    // }

    // public function desceStatus($ida){

    //     $sta_at = DB::table('atendimentos AS a')->where('id','=', $ida)->value('a.status_atendimento');


    //     if ($sta_at == 1){

    //         app('flasher')->addWarning('Não existe um status menor.');
    //         return redirect ('/gerenciar-atendimentos');
    //     }
    //     if ($sta_at == 2){

    //         app('flasher')->addInfo('Somente o atendente pode alterar este status.');
    //         return redirect ('/gerenciar-atendimentos');
    //     }
    //     if ($sta_at == 6){

    //         app('flasher')->addWarning('O atendimento foi cancelado e não pode ser alterado.');
    //         return redirect ('/gerenciar-atendimentos');

    //     }elseif ($sta_at == 5){

    //         app('flasher')->addWarning('O atendimento foi Finalizado e não pode ser alterado.');
    //         return redirect ('/gerenciar-atendimentos');

    //     }elseif ($sta_at == 3){

    //         DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
    //             'status_atendimento' => 1
    //         ]);

    //     }elseif ($sta_at == 4){

    //         DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
    //             'status_atendimento' => 1,
    //             'dh_inicio' => null,
    //             'id_atendente' => null
    //         ]);

    //         app('flasher')->addSuccess('O status foi alterado com sucesso.');
    //         return redirect ('/gerenciar-atendimentos');

    //     }

    //     app('flasher')->addError('Aconteceu um problema desconhecido contate o administrador.');
    //     return redirect ('/gerenciar-atendimentos');

    // }

    public function cancelar($ida){

        $status = DB::table('atendimentos AS a')->select('status_atendimento')->where('id', '=', $ida)->value('status_atendimento');

        if ($status > 1){

            app('flasher')->addError('Somente é permitido "Cancelar" atendimentos no status "Aguardando atendimento".');
            return redirect ('/gerenciar-atendimentos');

        }else{

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 6
            ]);

        app('flasher')->addSuccess('O status do atendimento foi alterado para "Cancelado".');
        return redirect ('/gerenciar-atendimentos');

        }
    }

    ////PREPARA PARA EDITAR
    public function edit($ida){

       $status = DB::table('atendimentos AS a')->select('status_atendimento')->where('id', '=', $ida)->value('status_atendimento');

        if ($status > 1){

            app('flasher')->addError('Somente são permitidas alterações quando o status é "Aguardando atendimento".');
            return redirect ('/gerenciar-atendimentos');

        }else{

        $result = DB::table('atendimentos AS at')
                    ->where('at.id', $ida)
                    ->select('at.id AS ida', 'p1.id as idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'at.id_atendente_pref AS iap', 'p3.nome_completo as nm_3', 'at.id_atendente as idaf', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente AS pta', 'ts.descricao', 'tp.nome',  'at.parentesco', 'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo', 'at.id_prioridade', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla' )
                    ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id')
                    ->leftJoin('pessoas AS p', 'att.id_pessoa', 'p.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftjoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
                    ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id')
                    ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                    ->get();

        $lista = DB::select("select
                    p.id,
                    p.ddd,
                    p.celular,
                    p.nome_completo,
                    a.id_pessoa
                    from pessoas p
                    left join atendentes a on (p.id = a.id_pessoa)
                    order by nome_completo
                    ");

        //dd($lista);

        $afi = DB::select("select
                    p.id as iaf,
                    p.nome_completo as nm_afi,
                    p.ddd,
                    p.celular,
                    a.id_pessoa,
                    a.id
                    from atendentes a
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
        

        return view ('/recepcao-AFI/editar-atendimento', compact('result', 'priori', 'sexo', 'pare', 'afi', 'lista'));
        }
    }

    ///////ALTERA UM ATENDIMENTO
    public function altera(Request $request, $ida){


        DB::table('atendimentos AS at')->where('at.id', $ida)->update([

            'id_assistido'=>$request->input('assist'),
            'id_representante'=>$request->input('repres'),
            'parentesco'=>$request->input('parent'),
            'id_atendente_pref'=>$request->input('afi_p'),
            'pref_tipo_atendente'=>$request->input('tipo_afi'),
            'id_prioridade'=>$request->input('priori')

        ]);


         app('flasher')->addSuccess('O cadastro de pessoa foi alterado com sucesso.');

         return redirect ('/gerenciar-atendimentos');

     }

     public function visual($idas){


        $result = DB::table('atendimentos AS at')
                    ->where('p1.id', $idas)
                    ->select('at.id AS ida', 'at.pref_tipo_atendente', 'p1.dt_nascimento', 'at.dh_chegada',  'at.dh_fim', 'at.dh_inicio', 'at.id_assistido', 'at.id_representante', 'at.id_atendente_pref', 'at.id_atendente', 'at.parentesco', 'tdd.descricao AS ddd', 'p1.celular', 'p1.id AS idas', 'p1.nome_completo AS nm_1',  'p2.nome_completo as nm_2',  'p3.id AS idp', 'p3.nome_completo as nm_3',  'p4.nome_completo as nm_4',  'ts.descricao', 'tp.nome',   'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo')
                    ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftjoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
                    ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id')
                    ->leftJoin('tp_ddd AS tdd', 'p1.ddd', 'tdd.id')
                    ->orderBy('dh_chegada', 'DESC')
                    ->get();




         return view ('/recepcao-AFI/visualizar-atendimentos', compact('result'));

    }

    //  public function salvaatend(Request $request, $ida){

    //     $sta_at = DB::table('atendimentos AS a')
    //             ->where('id','=', $ida)
    //             ->value('a.status_atendimento');

    //     $att = $request->atendente;

    //     $sit_afi = DB::table('atendimentos AS at')
    //             ->where('id_atendente','=', $att)
    //             ->where('at.status_atendimento', '<', '5')
    //             ->count();


    //     if ($sta_at == 2){

    //         app('flasher')->addInfo('Somente o atendente pode alterar este status.');
    //         return redirect ('/gerenciar-atendimentos');

    //     }elseif ($sta_at == 3){

    //         app('flasher')->addWarning('O atendimento está direcionado para outro atendente.');
    //         return redirect ('/gerenciar-atendimentos');

    //     }elseif ($sta_at == 5){

    //         app('flasher')->addWarning('O atendimento foi Finalizado e não pode ser alterado.');
    //         return redirect ('/gerenciar-atendimentos');

    //     }elseif ($sta_at == 6){

    //         app('flasher')->addWarning('O atendimento foi cancelado e não pode ser alterado.');
    //         return redirect ('/gerenciar-atendimentos');

    //     }if ($sta_at == 1 && $sit_afi > 0){

    //         app('flasher')->addWarning('O atendente está ocupado.');
    //         return redirect ('/gerenciar-atendimentos');

    //     }if ($sta_at == 1 && $sit_afi == 0 ){

    //         DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
    //             'status_atendimento' => 3,
    //             'id_atendente' => $request->input('atendente')
    //         ]);
    //     }

    //     app('flasher')->addSuccess('O status foi alterado e o atendente incluído.');
    //     return redirect ('/gerenciar-atendimentos');
    // }


    /////GERENCIAMENTO DOS ATENDENTES DO DIA

    public function atendente_dia(Request $request){

        $now = Carbon::now()->format('d/m/Y');

               //dd($now);

        $atende = DB::table('atendente_dia AS atd')
<<<<<<< HEAD
                ->select('atd.id AS nr','att.id_pessoa AS idp', 'atd.id AS idatd', 'atd.id_atendente AS idad', 'atd.id_sala', 'atd.data_hora', 'p.nome_completo AS nm_4',  'p.id', 'tsp.tipo', 'g.id AS idg', 'g.nome AS nomeg', 's.id AS ids', 's.numero AS nm_sala', 'p.status')
                ->leftJoin('atendentes AS att', 'atd.id_atendente','att.id_pessoa')
                ->leftjoin('pessoas AS p', 'att.id_pessoa', 'p.id' )
                ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
                ->leftJoin('salas AS s', 'atd.id_sala', 's.id')
                ->leftJoin('atendente_grupo AS ag', 'att.id', 'ag.id_atendente')
                ->leftJoin('grupo AS g', 'ag.id_grupo', 'g.id');
                
        //dd($atende);

        $data = $request->data;
=======
                ->select('atd.id AS nr','att.id AS ida', 'atd.id AS idatd', 'atd.id_atendente AS idad', 'atd.id_sala', 'atd.data_hora', 'p.nome_completo AS nm_4',  'p.id', 'tsp.tipo', 'g.id AS idg', 'g.nome AS nomeg', 's.id AS ids', 's.numero AS nm_sala')
                ->leftJoin('atendentes AS att', 'atd.id_atendente','att.id_pessoa')
                ->leftjoin('pessoas AS p', 'atd.id_atendente', 'p.id' )
                ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
                ->leftJoin('salas AS s', 'atd.id_sala', 's.id')
                ->leftJoin('grupo AS g', 'att.id_grupo', 'g.id');
>>>>>>> ffcc50c78ff5861fd8b99aa39d82d04bfda08b5f

        $grupo = $request->grupo;

        $atendente = $request->atendente;

        $status = $request->status;


        if ($request->grupo){
        $atende->where('g.id', '=', $request->grupo);
        }

        if ($request->atendente){
        $atende->where('p.nm_4', 'ilike', "%$request->atendente%");
        }

        if ($request->status){
        $atende->where('p.status', $request->status);
        }


        $atende = $atende->orderby('atd.data_hora', 'DESC')->orderby('nm_sala', 'ASC')->paginate(50);


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




        return view ('/recepcao-AFI/gerenciar-atendente-dia', compact('atende', 'st_atend',  'atendente', 'status', 'situacao', 'grupo'));



    }

    ////PREPARA INFORMAÇÕES DO FORMULÁRIO DE EDIÇÃO DA SALA

    

    public function editar_afi($idatd){

        $now =  Carbon::now()->format('Y-m-d');

        $atende = DB::table('atendente_dia AS atd')
        ->select('att.id AS ida', 'atd.id AS idatd', 'atd.id_atendente AS idad', 'atd.id_sala', 'atd.data_hora', 'p.nome_completo AS nm_4',  'p.id', 'tsp.tipo', 'g.id AS idg', 'g.nome AS nomeg', 's.id AS ids', 's.numero AS nm_sala')
        ->leftJoin('atendentes AS att', 'atd.id_atendente','att.id_pessoa')
        ->leftjoin('pessoas AS p', 'atd.id_atendente', 'p.id' )
        ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
        ->leftJoin('salas AS s', 'atd.id_sala', 's.id')
        ->leftJoin('grupo AS g', 'att.id_grupo', 'g.id')
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

        $sala_ocupada = DB::table('atendente_dia AS atd')
                        ->leftJoin('salas AS s', 'atd.id_sala', 's.id')
                        ->where('atd.data_hora', $now)
                        ->pluck('id_sala');
        
   

        $sala = DB::table('salas AS s')
                    ->select('s.id', 's.numero')
                    ->where( 's.id_finalidade', 2)
                    ->where('s.status_sala', 1)
                    ->whereNotIn('s.id', DB::table('atendente_dia AS atd')
                        ->where('atd.data_hora', $now)
                        ->pluck('atd.id_sala'))
                    ->orderBy('numero', 'asc')
                    ->get();
      

          // dd($sala);



        return view ('/recepcao-AFI/editar-atendente-dia', compact('atende', 'st_atend', 'grupo', 'sala'));
    }

    //// SALVAR EM BANCO A EDIÇÃO DA SALA DO AFI

    public function update_afi(Request $request, $idatd){
            
            $now = Carbon::now()->format('Y-m-d');
 

            $sala = $request->sala;



            $sala_dia = DB::table('atendente_dia AS atd')->where('atd.id_sala', $request->sala)->where('atd.data_hora', $now)->count('atd.id');

            if ($sala_dia == 0){

                DB::table('atendente_dia AS atd')->where('id', $idatd)->update([
                            'id_sala' => $request->input('sala')
                        ]);
                        

                app('flasher')->addSuccess('A sala foi alterada com sucesso.');

                return redirect ('/gerenciar-atendente-dia');

            }else{

                app('flasher')->addError('A sala está ocupada.'); 

                return redirect ('/gerenciar-atendente-dia');
            }

            app('flasher')->addError('Saiu aqui deu erro.'); 

            return redirect ('/gerenciar-atendente-dia');

    }



    //////GERENCIAR/DEFINIR OS AFI E SALAS DE ATENDIMENTO DO DIA
            
    public function definir_sala(Request $request){

        
        $now = Carbon::now()->format('Y-m-d');

        $aten = DB::table('atendente_dia AS atd')->select('id_atendente')->where('data_hora', $now)->pluck('id_atendente');

        //dd($aten);

                $atende = DB::table('atendentes AS att')
                ->select('att.id AS idat', 'att.id_pessoa AS idp', 'p.nome_completo AS nm_4',  'p.id AS pid', 'tsp.tipo', 'g.id AS idg', 'g.nome AS nomeg')
                ->leftjoin('pessoas AS p', 'att.id_pessoa', 'p.id' )
                ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
                ->leftJoin('atendente_grupo AS ag', 'att.id', 'ag.id_atendente')
                ->leftJoin('grupo AS g', 'ag.id_grupo', 'g.id')
                ->where('p.status', 1)
                ->whereNotIn('att.id_pessoa', $aten);


                $grupo = $request->grupo;

                $atendente = $request->atendente;

                $status = $request->status;
        
        
                if ($request->grupo){
                $atende->where('g.id', '=', $request->grupo);
                }
        
                if ($request->atendente){
                $atende->where('att.id_pessoa', $request->atendente);
                }
        
                if ($request->status){
                $atende->where('p.status', $request->status);
                }
        
        
                $atende = $atende->orderby('nm_4', 'ASC')->paginate(50);
        

                $st_atend = DB::select("select
                tsp.id,
                tsp.tipo
                from tipo_status_pessoa tsp
                ");

                $situacao = DB::table('tipo_status_pessoa')
                        ->select('id', 'tipo')
                        ->get();

                $grupo = DB::table('grupo AS g')
                        ->select('id', 'nome')
                        ->where('id_tipo_grupo', 3)
                        ->where('data_fim', null)
                        ->orderBy('nome')
                        ->get();

                foreach($atende as $key => $lista){
                $result = DB::table('atendente_grupo AS ag')
                        ->leftJoin('grupo AS g', 'ag.id_grupo', 'g.id')
                        ->leftJoin('atendentes AS att', 'ag.id_atendente', 'att.id_pessoa')
                        ->select('ag.id_grupo', 'g.nome')
                        ->where('g.id_tipo_grupo', 3)
                        ->where('g.data_fim', null)
                        ->where('ag.id_atendente', )
                        ->orderBy('nome')
                        ->get();
                        $lista->grup=$result;
                }

                $sala = DB::table('salas AS s')
                        ->select('s.id', 's.numero')
                        ->where( 's.id_finalidade', 2)
                        ->where('s.status_sala', 1)
                        ->whereNotIn('s.id', DB::table('atendente_dia AS atd')
                            ->where('atd.data_hora', $now)
                            ->pluck('atd.id_sala'))
                        ->orderBy('numero', 'asc')
                        ->get();


                    //dd($atende);

    return view ('/recepcao-AFI/incluir-atendente-dia', compact('atende', 'st_atend',  'situacao', 'grupo', 'sala'));


    }

    ////SALVA O AFI DO DIA E SALA

    public function salva_afi(Request $request, $idat, $idg){

        $sala = $request->sala;

        $now = Carbon::now()->format('Y-m-d');

        //$atendente = DB::table('atendentes AS a')->select('a.id AS ida')->where('id_pessoa', $idat)->get();

        $verif = DB::table('atendente_dia AS atd')->where('data_hora', $now)->where('id_sala', $sala)->count();

        //dd($verif);

        if ($verif == 0){

        DB::table('atendente_dia AS atd')->insert([
                        'id_sala' => $request->input('sala'),
                        'id_grupo' => $request->input('grupo'),
                        'id_atendente' => $idat,
                        'data_hora' => $now
                        ]);

        app('flasher')->addSuccess('O atendente foi incluido e a sala vinculada.');

        return redirect()->back() ;

        }else{

            app('flasher')->addError('Essa sala está ocupada.');

            return redirect()->back() ;
        }
        
    }




    ////EDITAR A SALA DE TRABALHO DO ATENDENTE

    public function gravar_sala(Request $request, $ida){

        $now = Carbon::now()->format('d/m/Y');

        $sit_afi = DB::table('atendente_dia AS atd')->select('id_atendente')->where('atd.data_hora', $now)->count();



        if ($sit_afi > 0){

            app('flasher')->addWarning('O atendente está em outra sala.');

            return redirect()->back() ;

        }else{

        DB::table('atendente_dia AS atd')->where('atd.id', $ida)->insert([

            'id_atendente'=>$request->input('atendente'),
            'id_sala'=>$request->input('sala'),
            'data_hora'=> $now
        ]);
        
        }


    }

    ///APAGAR O ATENDENTE DA LISTA DIÁRIA

    public function delete( $idatd, $idad){

        $usuario = session()->get('usuario.id_pessoa');

        $now = Carbon::now()->format('Y/m/d');

        DB::table('atendente_dia AS atd')->where('id',$idatd)->delete();

        DB::table('historico_venus')->insert([
            'id_usuario' => $usuario,
            'data' => $now,
            'fato' => 35,
            'id_ref' => $idad
            ]);

        app('flasher')->addSuccess('O atendente foi excluído.');

        return redirect('/gerenciar-atendente-dia');

    }

}
