<?php

namespace App\Http\Controllers;

use App\Jobs\DiasCronograma;
use App\Jobs\DiasCronogramaOntem;
use App\Jobs\FaLtas;
use App\Jobs\FimSemanas;
use App\Jobs\LimiteFalta;
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
    public function index(Request $request){
        try{

        $now =  Carbon::now()->format('Y-m-d');


        $lista = DB::table('tratamento AS tr')
                    ->select('tr.id AS idtr', 'tr.status','enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tst.nome AS tst', 'enc.id_tipo_tratamento AS idtt', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tt.sigla', 'tr.id AS idtr', 'gr.nome AS nomeg', 'td.nome AS nomed', 'rm.h_inicio', 'tr.dt_fim' )
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
                    ->leftjoin('tipo_dia AS td', 'rm.dia_semana','td.id')
                    ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                    ->where('enc.id_tipo_encaminhamento', 2)
                    ->where('enc.id_tipo_tratamento', '<>', 3);

        $data_enc = $request->dt_enc;

        $diaP = $request->dia;

        $assistido = $request->assist;

        $situacao = $request->status;

        if ($request->dia != null){
            $lista->where('rm.dia_semana', '=', $request->dia);
        }

        if ($request->dt_enc){
            $lista->where('enc.dh_enc', '>=', $request->dt_enc);
        }

        if ($request->assist) {
            $lista->whereRaw("UNACCENT(LOWER(p1.nome_completo)) ILIKE UNACCENT(LOWER(?))", ["%{$request->assist}%"]);
        }

        if ($request->status and $request->status != 'all'){
            $lista->where('tr.status', $request->status);
        }
        elseif($request->status == 'all'){

        }
        else{
            $lista->where('tr.status', 2);
        }

        $lista = $lista->orderby('tr.status', 'ASC')->orderby('at.id_prioridade', 'ASC')->orderby('nm_1', 'ASC')->paginate(50);
        //dd($lista)->get();

        $contar = $lista->count('enc.id');


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

        $motivo = DB::table('tipo_motivo')->get();



        return view ('/recepcao-integrada/gerenciar-tratamentos', compact('lista', 'stat', 'contar', 'data_enc', 'assistido', 'situacao', 'now', 'dia', 'diaP', 'motivo'));


    }

    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }

    public function destroy(Request $request, string $id){

try{
        $hoje = Carbon::today();
        $tratamento = DB::table('tratamento')->where('id', $id)->first();


        DB::table('tratamento')->where('id', $id)->update(['status' => 6, 'motivo' => $request->motivo, 'dt_fim' => $hoje]);
        DB::table('encaminhamento')->where('id', $tratamento->id_encaminhamento)->update(['status_encaminhamento' => 4]);


        return redirect()->back();


    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }


    public function presenca(Request $request, $idtr){
       // try{

        $infoTrat = DB::table('tratamento')->leftJoin('encaminhamento', 'tratamento.id_encaminhamento', 'encaminhamento.id')->where('tratamento.id', $idtr)->first();

        $data_atual = Carbon::now();
        $dia_atual = $data_atual->weekday();

        $confere = DB::table('presenca_cronograma AS ds')
        ->leftJoin('dias_cronograma as dc', 'ds.id_dias_cronograma', 'dc.id')
        ->where('dc.data', $data_atual)
        ->where('ds.id_tratamento', $idtr)
        ->count();

        $lista = DB::table('tratamento AS tr')
        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
        ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
        ->where('tr.id', $idtr)
        ->first();



        $dia_cronograma = DB::table('dias_cronograma')->where('id_cronograma', $lista->id_reuniao)->where('data', $data_atual)->first();

        $acompanhantes = DB::table('dias_cronograma')->where('id_cronograma', $request->reuniao)->where('data', $data_atual)->first();


        if($confere > 0){

            app('flasher')->addError('Já foi registrada a presença para este dia.');

            return Redirect('/gerenciar-tratamentos');

        }
        else if($lista->dia_semana != $dia_atual){

            app('flasher')->addError('Este assistido não corresponde ao dia de hoje.');

            return Redirect('/gerenciar-tratamentos');

        }else{


            $encaminhamentosPTD = DB::table('encaminhamento')->where('id_atendimento', $lista->id_atendimento)->where('id_tipo_tratamento', 1)->where('status_encaminhamento', 4)->first();

            if($infoTrat->status == 1){
                DB::table('tratamento')->where('id', $idtr)->update([
                    'status' => 2
                ]);

                if($infoTrat->id_tipo_tratamento == 2){
                    DB::table('encaminhamento')->where('id', $encaminhamentosPTD->id)->update([
                        'status_encaminhamento' => 5
                    ]);
                }

            }


        $presenca = isset($request->presenca) ? true : false;

        $acompanhantes = isset($acompanhantes->nr_acompanhantes)  ? $acompanhantes->nr_acompanhantes : 0;
        $nrAcomp = $acompanhantes + $request->acompanhantes;


        DB::table('dias_cronograma')
        ->where('id_cronograma', $lista->id_reuniao)
        ->where('data', $data_atual)
        ->update([
            'nr_acompanhantes' => $nrAcomp
        ]);

        DB::table('presenca_cronograma')
        ->insert([
            'id_tratamento' => $idtr,
            'presenca' => true,
            'id_dias_cronograma' => $dia_cronograma->id
        ]);





        app('flasher')->addSuccess('Foi registrada a presença com sucesso.');

        return Redirect('/gerenciar-tratamentos');
        }

        app('flasher')->addError('Aconteceu um erro inesperado.');

        return Redirect('/gerenciar-tratamentos');
    }
    // catch(\Exception $e){

    //     $code = $e->getCode( );
    //     return view('tratamento-erro.erro-inesperado', compact('code'));
    //         }
    //     }


    public function visualizar($idtr){
       // try{
        $result = DB::table('tratamento AS tr')
                        ->select('enc.id AS ide', 'tr.id AS idtr', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido','p1.dt_nascimento', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tx.tipo', 'p4.nome_completo AS nm_4', 'at.dh_inicio', 'at.dh_fim', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'gr.nome AS nomeg', 'td.nome as nomedia', 'rm.h_inicio AS rm_inicio', 'tm.tipo AS tpmotivo', 'sat.descricao AS statat','sl.numero as sala')
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                        ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                        ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                        ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                        ->leftjoin('associado as ass', 'at.id_atendente', 'ass.id')
                        ->leftjoin('pessoas AS p4', 'ass.id_pessoa', 'p4.id')
                        ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                        ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                        ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                        ->leftJoin('tipo_status_atendimento AS sat', 'at.status_atendimento', 'sat.id')
                        ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                        ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                        ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
                        ->leftJoin('salas as sl', 'rm.id_sala', 'sl.id')
                        ->leftJoin('tipo_dia as td', 'rm.dia_semana', 'td.id')
                        ->where('tr.id', $idtr)
                        ->get();

        $list = DB::table('tratamento AS tr')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.presenca', 'dc.data', 'gp.nome')
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                        ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
                        ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
                        ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
                        ->where('tr.id', $idtr)
                        ->get();

        $faul = DB::table('tratamento AS tr')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp',  'dt.presenca')
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                        ->where('tr.id', $idtr)
                        ->where('dt.presenca', 0)
                        ->count();

        return view('/recepcao-integrada/historico-tratamento', compact('result', 'list', 'faul'));

    }
   // catch(\Exception $e){

    //    $code = $e->getCode( );
    //    return view('tratamento-erro.erro-inesperado', compact('code'));
          //  }
     //   }
    public function job() {
        try{
        DiasCronogramaOntem::dispatch();
        DiasCronograma::dispatch();
        LimiteFalta::dispatch();
        FimSemanas::dispatch();
        Faltas::dispatch();
        return redirect()->back();
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }


    public function createAvulso(){
        try{
        //dd($request->all());
        $hoje = Carbon::today();
        $dia = Carbon::today()->weekday();

       $assistidos = DB::table('pessoas')->select('id', 'nome_completo')->orderBy('nome_completo')->get();

       $reuniao = DB::table('cronograma as cro')
       ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
       ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
       ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
       ->where('cro.id_tipo_tratamento', 1) 
       ->where('cro.dia_semana', $dia)
       ->where(function($query) use ($hoje) {
        $query->whereRaw("cro.data_fim < ?", [$hoje])
              ->orWhereNull('cro.data_fim');
    })
       ->select('cro.id', 'cro.h_inicio', 'cro.h_fim', 'td.nome as nomedia', 'gr.nome', 'sl.numero as sala')
       ->get();

   // dd($reuniao);

        $motivo = DB::table('tipo_motivo_presenca')->get();


        return view('recepcao-integrada.incluir-avulso', compact('assistidos', 'reuniao', 'motivo'));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }
        public function storeAvulso(Request $request)
        {
            $hoje = Carbon::today();
            $acompanhantes = DB::table('dias_cronograma')
                ->where('id_cronograma', $request->reuniao)
                ->where('data', $hoje)
                ->first();
        
            // Verifica se o registro existe
            if (!$acompanhantes) {
                // Se não existir, crie um novo registro
                $acompanhantesId = DB::table('dias_cronograma')->insertGetId([
                    'id_cronograma' => $request->reuniao,
                    'data' => $hoje,
                    'nr_acompanhantes' => $request->acompanhantes,
                ]);
            } else {
                // Se existir, atualiza o número de acompanhantes
                $nrAcomp = $acompanhantes->nr_acompanhantes + $request->acompanhantes;
        
                DB::table('dias_cronograma')
                    ->where('id_cronograma', $request->reuniao)
                    ->where('data', $hoje)
                    ->update([
                        'nr_acompanhantes' => $nrAcomp
                    ]);
        
                $acompanhantesId = $acompanhantes->id; // Use o ID existente
            }
        
            // Insere a presença do assistido
            DB::table('presenca_cronograma')->insert([
                'presenca' => true,
                'id_pessoa' => $request->assistido,
                'id_dias_cronograma' => $acompanhantesId,
                'id_motivo' => $request->motivo
            ]);
        
            return redirect('/gerenciar-tratamentos');
        }
        
        

}
