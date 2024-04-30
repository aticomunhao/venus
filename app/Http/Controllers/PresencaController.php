<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tipo_fato;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Support\Carbon;

    class PresencaController extends Controller
    {
      
        public function index(Request $request)
        {
            $now = Carbon::now()->format('Y-m-d');
        
            $lista = DB::table('atendimentos as atd')
            ->select('p.nome_completo', 'p.cpf', 'atd.id', 'atd.dh_marcada')
            ->leftJoin('pessoas as p', 'atd.id_assistido', 'p.id')
            ->where('status_atendimento',7)
            ->where('afe', true)->get();

        


   
        
            if ($request->dia) {
                $lista->where('rm.dia_semana', $request->dia);
            }
        
            if ($request->dt_enc) {
                $lista->where('enc.dh_enc', '>=', $request->dt_enc);
            }
        
            if ($request->assist) {
                $lista->where('p1.nome_completo', 'ilike', "%$request->assist%");
            }
        
            if ($request->status) {
                $lista->where('tr.status', $request->status);
            }
            
           

        
            
        
            $stat = DB::table('tipo_status_tratamento')->select('id', 'nome')->get();
            $dia = DB::table('tipo_dia')->select('id', 'nome')->get();

        
            return view('presenças.gerenciar-presenca', compact('lista', 'stat', 'now', 'dia'));
        }
        
    
        public function criar(Request $request, string $idtr) {
            
        
            $now = Carbon::now();
            $presenca = isset($request->presenca) ? true : false;
        
            DB::table('atendimentos')
            ->where('atendimentos.id', $idtr)
                ->update([
                    'dh_chegada' =>  $now,
                    'status_atendimento' => 1,
                
                ]);
        
            app('flasher')->addSuccess('Foi registrada a presença com sucesso.');
        
            return redirect('/gerenciar-presenca');
        }
        
        public function show ($idtr){

            $result = DB::table('tratamento AS tr')
                            ->select('enc.id AS ide', 'tr.id AS idtr', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido','p1.dt_nascimento', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tx.tipo', 'p4.nome_completo AS nm_4', 'at.dh_inicio', 'at.dh_fim', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'gr.nome AS nomeg', 'rm.h_inicio AS rm_inicio', 'tm.tipo AS tpmotivo', 'sat.descricao AS statat')
                            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                            ->leftJoin('tipo_status_atendimento AS sat', 'at.status_atendimento', 'sat.id')
                            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                            ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
                            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                            ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
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
    
    
            return view('Presenças.visualizar-presenca', compact('result', 'list', 'faul'));
    
        }

        public function edit($id) {

           


            return view ('editar-presenca' , compact(''));

        }


        public function update(Request $request, string $id)
        {

        

            return redirect('/gerenciar-presenca');

        }


       

            public function incluir(Request $request)

        {






            return redirect('/gerenciar-presenca');
        }




            public function destroy( $id)
            {
              




                return redirect('/gerenciar-presenca');


            }



 }







