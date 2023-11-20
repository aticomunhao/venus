<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

use function Laravel\Prompts\select;

class GerenciarAtendimentoController extends Controller
{

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
                    ->select('at.id AS ida', 'p1.id AS idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'at.id_atendente_pref', 'p3.nome_completo as nm_3', 'at.id_atendente', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tx.tipo', 'pa.nome', 'att.id as idatt','at.id_prioridade', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla' )
                    ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id')
                    ->leftJoin('pessoas AS p', 'att.id_pessoa', 'p.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftjoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id');

        $data_inicio = $request->dt_ini;

        $assistido = $request->assist;

        $situacao = $request->status;


        if ($request->dt_ini){
            $lista->where('at.dh_chegada', '>=', $request->dt_ini);
        }

        if ($request->assist){
            $lista->where('p1.nome_completo', 'like', "%$request->assist%");
        }

        if ($request->status){
            $lista->where('at.status_atendimento', $request->status);
        }


        $lista = $lista->orderby('status_atendimento', 'ASC')->orderBy( 'at.id_prioridade', 'ASC')->orderby('at.dh_chegada', 'ASC')->paginate(50);

        $contar = $lista->count('at.id');

        $status = DB::select("select
        s.id,
        s.descricao
        from tipo_status_atendimento s
        ");




        return view ('/recepcao-AFI/gerenciar-atendimentos', compact('lista', 'status', 'contar', 'atende', 'data_inicio', 'assistido', 'situacao'));


    }

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
        ");

        $priori = DB::select("select
        pr.id as prid,
        pr.descricao as prdesc,
        pr.sigla as prsigla
        from tipo_prioridade pr
        ");
       //dd($lista);

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

        //dd($lista);

        return view ('/recepcao-AFI/incluir-atendimento', compact('afi', 'priori', 'sexo', 'parentes', 'lista'));


    }

    public function store(Request $request){

        $dt_hora = Carbon::now();

        $assistido = $request->assist;

        $resultado = DB::table('atendimentos')->where('status_atendimento', '<', 5)->where('id_assistido', $assistido)->count();

        //dd($resultado);
        if ($resultado > 0){

            app('flasher')->addError('Não é permitido duplicar o cadastro do assistido.');

            return redirect ('/gerenciar-atendimentos');

        };


        //dd($dt_hora);
       DB::table('atendimentos AS atd')->insert([
        'dh_chegada'=> ($dt_hora->toDateTimeString() . PHP_EOL),
        'id_usuario'=> 1,
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
        'id_usuario' => 1,
        'data' => $dt_hora,
        'fato' => 5,
        'pessoa' => $request->input('assist')
    ]);


        app('flasher')->addSuccess('O cadastro do atendimento foi realizado com sucesso.');

        return redirect ('/gerenciar-atendimentos');

    }


    public function SetPessoa(Request $request){


       DB::table('pessoas AS p')->insert([

        'nome_completo'=>$request->input('nomepes'),

       ]);


        app('flasher')->addSuccess('O cadastro de pessoa foi realizado com sucesso.');

        return redirect ('/gerenciar-atendimentos');

    }


    public function sobeStatus($ida){

        $data = date("Y-m-d H:i:s");


        $sta_at = DB::table('atendimentos AS a')->where('id','=', $ida)->value('a.status_atendimento');


        if ($sta_at == 1){

            app('flasher')->addError('Utilize a opção de vincula o Atendente Fraterno ao atendimento.');
            return redirect ('/gerenciar-atendimentos');
        }
        elseif ($sta_at > 4){

            app('flasher')->addError('O atendimento foi cancelado ou finalizado e não pode ser alterado.');
            return redirect ('/gerenciar-atendimentos');
        }
        elseif ($sta_at == 2){

            app('flasher')->addError('Somente o Atendente Fraterno pode alterar este status');
            return redirect ('/gerenciar-atendimentos');

        }elseif ($sta_at == 3){

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 4,
                'dh_inicio' => $data
            ]);

        }elseif ($sta_at == 4){

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 5,
                'dh_fim' => $data
            ]);

        }

        app('flasher')->addSuccess('O satus foi alterado com sucesso.');
        return redirect ('/gerenciar-atendimentos');

    }

    public function desceStatus($ida){

        $sta_at = DB::table('atendimentos AS a')->where('id','=', $ida)->value('a.status_atendimento');


        if ($sta_at == 1){

            app('flasher')->addWarning('Não existe um status menor.');
            return redirect ('/gerenciar-atendimentos');
        }
        if ($sta_at == 2){

            app('flasher')->addInfo('Somente o atendente pode alterar este status.');
            return redirect ('/gerenciar-atendimentos');
        }
        if ($sta_at == 6){

            app('flasher')->addWarning('O atendimento foi cancelado e não pode ser alterado.');
            return redirect ('/gerenciar-atendimentos');

        }elseif ($sta_at == 5){

            app('flasher')->addWarning('O atendimento foi Finalizado e não pode ser alterado.');
            return redirect ('/gerenciar-atendimentos');

        }elseif ($sta_at == 3){

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 1
            ]);

        }elseif ($sta_at == 4){

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 1,
                'dh_inicio' => null,
                'id_atendente' => null
            ]);

            app('flasher')->addSuccess('O status foi alterado com sucesso.');
            return redirect ('/gerenciar-atendimentos');

        }

        app('flasher')->addError('Aconteceu um problema desconhecido contate o administrador.');
        return redirect ('/gerenciar-atendimentos');

    }

    public function cancelar($ida){

        dd(session());

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 6
            ]);

        app('flasher')->addSuccess('O status do atendimento foi alterado para "Cancelado".');
        return redirect ('/gerenciar-atendimentos');

    }


    public function edit($ida){

        $result = DB::table('atendimentos AS at')
                    ->where('at.id', $ida)
                    ->select('at.id AS ida', 'p1.id as idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'at.id_atendente_pref AS iap', 'p3.nome_completo as nm_3', 'at.id_atendente as idaf', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tp.nome',  'at.parentesco', 'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo', 'at.id_prioridade', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla' )
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
                    ");


        return view ('/recepcao-AFI/editar-atendimento', compact('result', 'priori', 'sexo', 'pare', 'afi', 'lista'));

    }

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
                    ->select('at.id AS ida', 'at.pref_tipo_atendente', 'at.dh_chegada',  'at.dh_fim', 'at.dh_inicio', 'at.id_assistido', 'at.id_representante', 'at.id_atendente_pref', 'at.id_atendente', 'at.parentesco', 'tdd.descricao AS ddd', 'p1.celular', 'p1.id AS idas', 'p1.nome_completo AS nm_1',  'p2.nome_completo as nm_2',  'p3.id AS idp', 'p3.nome_completo as nm_3',  'p4.nome_completo as nm_4',  'ts.descricao', 'tp.nome',   'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo')
                    ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id')
                    ->leftJoin('pessoas AS p', 'att.id_pessoa', 'p.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'att.id_pessoa', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'att.id_pessoa', 'p4.id')
                    ->leftjoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
                    ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id')
                    ->leftJoin('tp_ddd AS tdd', 'p1.ddd', 'tdd.id')
                    ->orderBy('dh_chegada', 'DESC')
                    ->get();




         return view ('/recepcao-AFI/visualizar-atendimentos', compact('result'));

    }

     public function salvaatend(Request $request, $ida){

        $sta_at = DB::table('atendimentos AS a')
                ->where('id','=', $ida)
                ->value('a.status_atendimento');

        $att = $request->atendente;

        $sit_afi = DB::table('atendimentos AS at')
                ->where('id_atendente','=', $att)
                ->where('at.status_atendimento', '<', '5')
                ->count();

        //dd($sit_afi);


        if ($sta_at == 2){

            app('flasher')->addInfo('Somente o atendente pode alterar este status.');
            return redirect ('/gerenciar-atendimentos');

        }elseif ($sta_at == 3){

            app('flasher')->addWarning('O atendimento está direcionado para outro atendente.');
            return redirect ('/gerenciar-atendimentos');

        }elseif ($sta_at == 5){

            app('flasher')->addWarning('O atendimento foi Finalizado e não pode ser alterado.');
            return redirect ('/gerenciar-atendimentos');

        }elseif ($sta_at == 6){

            app('flasher')->addWarning('O atendimento foi cancelado e não pode ser alterado.');
            return redirect ('/gerenciar-atendimentos');

        }if ($sta_at == 1 && $sit_afi > 0){

            app('flasher')->addWarning('O atendente está ocupado.');
            return redirect ('/gerenciar-atendimentos');

        }if ($sta_at == 1 && $sit_afi == 0 ){

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 3,
                'id_atendente' => $request->input('atendente')
            ]);
        }

        app('flasher')->addSuccess('O status foi alterado e o atendente incluído.');
        return redirect ('/gerenciar-atendimentos');
    }

}
