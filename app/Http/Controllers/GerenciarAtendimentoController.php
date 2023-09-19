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

        $lista = DB::table('atendimentos AS at')
                    ->select('at.id AS ida', 'p1.id AS idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo as nm_2', 'at.id_atendente_pref', 'p3.nome_completo as nm_3', 'at.id_atendente', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tx.tipo', 'pa.nome')
                    ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftjoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id') 
                    ->leftjoin('atendentes AS a', 'p4.id', 'a.id_pessoa');
                   
        $assistido = $request->assist;

        $situacao = $request->status;

        if ($request->assist){
            $lista->where('p1.nome_completo', 'like', "%$request->assist%");
        }

        if ($request->status){
            $lista->where('at.status_atendimento', $request->status);
        }

        $contar = $lista->count('at.id');

        $lista = $lista->orderBy('at.id', 'DESC')->paginate(50);

        $status = DB::select("select
        s.id, 
        s.descricao
        from tipo_status_atendimento s
        "); 


        return view ('/recepcao-AFI/gerenciar-atendimentos', compact('lista', 'status', 'contar'));


    }

    public function create(){

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
        p.id, 
        p.nome_completo,
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

        return view ('/recepcao-AFI/incluir-atendimento', compact('afi', 'sexo', 'parentes', 'lista'));


    }

    public function store(Request $request){

        $dt_hora = Carbon::now();

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
        'status_atendimento'=> 1
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

        $sta_at = DB::table('atendimentos AS a')->where('id','=', $ida)->value('a.status_atendimento');


        if ($sta_at == 2){

            app('flasher')->addError('Somente o atendente pode alterar este status.');        
            return redirect ('/gerenciar-atendimentos');
        }        
        if ($sta_at > 5){

            app('flasher')->addError('O atendimento foi cancelado e não pode ser alterado.');        
            return redirect ('/gerenciar-atendimentos');

        }elseif ($sta_at == 1){

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 3
            ]);

        }elseif ($sta_at == 3){

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 4
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

        }

        app('flasher')->addSuccess('O satus foi alterado com sucesso.');        
        return redirect ('/gerenciar-atendimentos');

    }

    public function cancelar($ida){

            DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                'status_atendimento' => 6
            ]);

        app('flasher')->addSuccess('O status do atendimento foi alterado para "Cancelado".');        
        return redirect ('/gerenciar-atendimentos');

    }


    public function edit($ida){

        $result = DB::table('atendimentos AS at')
                    ->where('at.id', $ida)                  
                    ->select('at.id AS ida', 'p1.id', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo as nm_2', 'at.id_atendente_pref', 'p3.id AS idp', 'p3.nome_completo as nm_3', 'at.id_atendente', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tp.nome',  'at.parentesco', 'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo')
                    ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftJoin('atendentes AS a', 'p4.id', 'a.id_pessoa')
                    ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id' )
                    ->leftJoin('tp_sexo AS tpsx', 'tpsx.id', 'at.pref_tipo_atendente' )
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
                    p.id as idafi, 
                    p.nome_completo as nm_afi,
                    p.ddd,
                    p.celular,
                    a.id_pessoa
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
        

        return view ('\recepcao-AFI/editar-atendimento', compact('result', 'sexo', 'pare', 'afi', 'lista'));

    }

    public function altera(Request $request, $ida){

      
        DB::table('atendimentos AS at')->where('at.id', $ida)->update([
            
            'id_assistido'=>$request->input('assist'),
            'id_representante'=>$request->input('repres'),
            'parentesco'=>$request->input('parent'),
            'id_atendente_pref'=>$request->input('afi_p'),
            'pref_tipo_atendente'=>$request->input('tipo_afi')
 
        ]);
               
 
         app('flasher')->addSuccess('O cadastro de pessoa foi alterado com sucesso.');
         
         return redirect ('/gerenciar-atendimentos');
 
     }

     public function visual($idas){

      
        $result = DB::table('atendimentos AS at')
                    ->where('p1.id', $idas)                  
                    ->select('at.id AS ida', 'tpd.descricao AS ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.id AS idas', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo as nm_2', 'at.id_atendente_pref', 'p3.id AS idp', 'p3.nome_completo as nm_3', 'at.id_atendente', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tp.nome',  'at.parentesco', 'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo')
                    ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftJoin('atendentes AS a', 'p4.id', 'a.id_pessoa')
                    ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id' )
                    ->leftJoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
                    ->leftJoin('tp_ddd AS tpd', 'p1.ddd', 'tpd.id')
                    ->orderBy('dh_chegada', 'ASC')
                    ->get();

        
       
         
         return view ('/recepcao-AFI/visualizar-atendimentos', compact('result'));
 
     }

}