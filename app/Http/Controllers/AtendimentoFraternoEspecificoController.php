<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Coalesce as BinaryOpCoalesce;

use function Laravel\Prompts\select;



class AtendimentoFraternoEspecificoController extends Controller
{
    //



    public function index(Request $request)
    {

     
        $atendente = session()->get('usuario.id_associado');

        $pref_m = session()->get('usuario.sexo');

        $nome = session()->get('usuario.nome');

        $now =  Carbon::now()->format('Y-m-d');

        $grupo = DB::table('atendente_dia AS ad')
        ->leftJoin('grupo AS g', 'ad.id_grupo', 'g.id' )
        ->where('dh_inicio', '>=', $now)->where('ad.id_associado', $atendente)->value('g.nome');
        //dd($grupo);

        $assistido = DB::table('atendimentos AS at')
                            ->select('at.id AS idat', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido AS idas', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'p3.nome_completo AS nm_3', 'at.id_atendente', 'p4.nome_completo AS nm_4', 'at.pref_tipo_atendente AS pta', 'ts.descricao', 'tx.tipo','pa.nome', 'at.id_prioridade', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'at.status_atendimento')
                            ->leftJoin('associado AS a', 'at.id_atendente', 'a.id')
                            ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                            ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                            ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                            ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                            ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                            ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')                    
                            ->where('at.status_atendimento', '<', 5 )
                            ->Where('at.id_atendente', $atendente)                                                                                 
                            ->groupby('at.id', 'p1.id', 'p2.nome_completo', 'p3.nome_completo', 'p4.nome_completo', 'ts.descricao', 'tx.tipo', 'pa.nome', 'pr.descricao', 'pr.sigla')
                            ->orderby('status_atendimento', 'ASC')
                            ->get();

                    

                     return view ('/atendente-fraterno-especifico/atendendo-afe', compact('assistido', 'atendente', 'now', 'nome', 'grupo'));

        }

        public function atende_agora()
        {

            $now =  Carbon::now()->format('Y-m-d');
            $atendente = session()->get('usuario.id_associado');
            $pref_m = session()->get('usuario.sexo');

            $atendendo = DB::table('atendimentos AS at')
            ->leftjoin('membro AS m', 'at.id_atendente', 'm.id' )
            ->leftjoin('associado AS a', 'm.id_associado', 'a.id' )
            ->leftJoin('pessoas AS p', 'a.id_pessoa', 'p.id')
            ->where('id_associado', $atendente)
            ->where('status_atendimento', '<', 5)
            ->count();

            
            
            $assistido = DB::table('atendimentos')->where('status_atendimento', 1)->count();

            $sala = DB::table('atendente_dia AS atd')
            ->where('dh_inicio', $now )
            ->where('id_associado', $atendente )
            ->value('id_sala');
            
          
            if ($atendendo > 0){

                app('flasher')->addError('Você não pode atender dois assistidos ao mesmo tempo.');

                return redirect('/atendendo-afe');

            }elseif ($assistido < 1){

                app('flasher')->addError('Todos os assistidos foram atendidos.');

                return redirect('/atendendo-afe');
                
            }elseif($atendendo < 1 && $sala == null) {

                app('flasher')->addError('O atendente deve estar designado para o trabalho de hoje.');

                return view ('/atendente-fraterno-especifico/atendendo-afe', compact('assistido', 'atendente', 'now', 'nome', 'grupo'));
            
            }elseif ($atendendo < 1 && $sala > 0){

                    DB::table('atendimentos')
                            ->whereNull('id_atendente_pref')
                            ->orWhere('id_atendente_pref', $atendente)           
                            ->whereNull('pref_tipo_atendente')
                            ->orWhere('pref_tipo_atendente', $pref_m )
                            ->orderby('status_atendimento', 'ASC')->orderby('id_prioridade')->orderBy('dh_chegada')
                            ->limit(1)
                            ->update([
                                    'id_atendente' => $atendente,
                                    'id_sala' =>$sala,
                                    'status_atendimento' => 2
                            ]);


                app('flasher')->addSuccess('O assistido foi selecionado com sucesso.');

                return redirect('/atendendo-afe');

            } 

        }


        public function history($idat, $idas)
        {

           $atendimentos = DB::table('atendimentos AS at')->where('id_assistido', $idas)->get('id');
            //dd($atendimentos);
 
            
            $analisa = DB::table('atendimentos AS at')
                        ->select('at.id AS ida', 'at.observacao', 'p1.id AS idas', 'p1.ddd', 'p1.sexo', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'ps1.nome_completo AS nm_3', 'at.id_atendente', 'ps2.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao AS tst', 'tsx.tipo', 'pa.nome', 'p1.dt_nascimento',  't.nm_tca', 't1.nm_tca AS t1', 't2.nm_tca AS t2', 't3.nm_tca AS t3','t4.nm_tca AS t4','t5.nm_tca AS t5','t6.nm_tca AS t6','t7.nm_tca AS t7','t8.nm_tca AS t8','t9.nm_tca AS t9','t10.nm_tca AS t10','t11.nm_tca AS t11','t12.nm_tca AS t12','t13.nm_tca AS t13','t14.nm_tca AS t14','t15.nm_tca AS t15','t16.nm_tca AS t16','t17.nm_tca AS t17','t18.nm_tca AS t18','t19.nm_tca AS t19')
                        
                        ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                        ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                        ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')

                        ->leftJoin('membro AS m', 'at.id_atendente', 'm.id_associado')
                        ->leftJoin('associado AS ad1', 'm.id_associado', 'ad1.id')
                        ->leftJoin('pessoas AS ps1', 'ad1.id_pessoa', 'ps1.id')
                        ->leftJoin('membro AS m1', 'at.id_atendente_pref', 'm1.id_associado')
                        ->leftJoin('associado AS ad2', 'm1.id_associado', 'ad2.id')
                        ->leftJoin('pessoas AS ps2', 'ad1.id_pessoa', 'ps2.id')

                        ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                        ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                        ->leftJoin('tp_sexo AS tsx', 'p1.sexo', 'tsx.id')
                        ->leftJoin('registro_tema AS rt', 'at.id', 'rt.id_atendimento')
                        ->leftJoin('tca AS t', 'rt.ies', 't.id')
                        ->leftJoin('tca AS t1', 'rt.obs', 't1.id')
                        ->leftJoin('tca AS t2', 'rt.coj', 't2.id')
                        ->leftJoin('tca AS t3', 'rt.fam', 't3.id')
                        ->leftJoin('tca AS t4', 'rt.soc', 't4.id')
                        ->leftJoin('tca AS t5', 'rt.prf', 't5.id')
                        ->leftJoin('tca AS t6', 'rt.sau', 't6.id')
                        ->leftJoin('tca AS t7', 'rt.pdg', 't7.id')
                        ->leftJoin('tca AS t8', 'rt.sex', 't8.id')
                        ->leftJoin('tca AS t9', 'rt.adp', 't9.id')
                        ->leftJoin('tca AS t10', 'rt.deq', 't10.id')
                        ->leftJoin('tca AS t11', 'rt.est', 't11.id')
                        ->leftJoin('tca AS t12', 'rt.abo', 't12.id')
                        ->leftJoin('tca AS t13', 'rt.sui', 't13.id')
                        ->leftJoin('tca AS t14', 'rt.dou', 't14.id')
                        ->leftJoin('tca AS t15', 'rt.son', 't15.id')
                        ->leftJoin('tca AS t16', 'rt.esp', 't16.id')
                        ->leftJoin('tca AS t17', 'rt.dpr', 't17.id')
                        ->leftJoin('tca AS t18', 'rt.dqu', 't18.id')
                        ->leftJoin('tca AS t19', 'rt.dts', 't19.id')
                        ->leftJoin('tca AS t20', 'rt.maf', 't20.id')
                        ->where('at.id_assistido', $idas)
                        ->orderBy('at.dh_chegada', 'desc')
                        ->get();

            foreach($analisa as $key => $teste){
                $trata = DB::table('encaminhamento AS enc')
                            ->select('tt.descricao AS tdt')
                            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                            ->where('enc.id_atendimento', $teste->ida)
                            ->whereNotNull('enc.id_tipo_tratamento')
                            ->get();
                 $teste->tratamentos=$trata; 

                 $entre = DB::table('encaminhamento AS enc')
                            ->select('te.descricao AS tde')
                            ->leftJoin('tipo_entrevista AS te', 'enc.id_tipo_entrevista', 'te.id')
                            ->where('enc.id_atendimento', $teste->ida)
                            ->whereNotNull('enc.id_tipo_entrevista')
                            ->get();
                  $teste->entrevistas=$entre; 
            }

             
            //dd($analisa);
            $now = Carbon::now()->format('Y-m-d H:m:s'); 
            
            $atendente = session()->get('usuario.id_associado');

            $nome = DB::table('atendimentos AS at')->select('at.id_atendente')->where('at.id', $idat);

            $grupo = DB::table('atendente_dia AS ad')->select('ad.id_grupo')->where('dh_inicio', '>=', $now)->where('ad.id_associado', $atendente);

            $atendendo = DB::table('atendimentos AS at')->where('at.id', $idat)->value('id_atendente');
        //dd($atendendo);
            $status = DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento');
            
            $sit = DB::table('atendimentos AS at')->where('at.id_atendente', $atendente)->where('at.status_atendimento','<',5)->count();


            if ($sit > 0 && $atendendo == null)
            {
                app('flasher')->addError('Não é permitido atender dois assistidos ao mesmo tempo.');

                return redirect('/atendendo-afe');

            }

            if ($atendendo = $atendente && $status > 1)
            {
                app('flasher')->addInfo('Retomando análise.');

                return view ('/atendimento-assistido-afe/historico-assistido-afe', compact('atendente', 'analisa'));

            }

            if($atendendo = $atendente && $status = 1)
            {
                DB::table('atendimentos AS at')
                        ->where('status_atendimento', '=', 1)
                        ->where('at.id', $idat)
                        ->update([
                            'status_atendimento' => 2,
                            'id_atendente' => $atendente
                        ]);  

            app('flasher')->addSuccess('O status do atendimento foi alterado para em análise.');

            }

            return view ('/atendimento-assistido-afe/historico-assistido-afe', compact('atendente', 'analisa', 'grupo'));
        }           
//dd($assistido);
            

        public function fimanalise($idat)
        {

            $atendente = session()->get('usuario.id_associado');

            $sit = DB::table('atendimentos AS at')
                        ->where('at.id', $idat)
                        ->where('at.status_atendimento', 2)
                        ->count();
            
            $status =  DB::table('atendimentos AS at')
                        ->where('at.id', $idat)
                        ->value('status_atendimento');

            if ($status > 2){

                app('flasher')->addError('Esta ação não pode ser executada, este status já foi ultrapassado.');
        
                return redirect()->back();

            }
            if ($sit = 1){
                DB::table('atendimentos AS at')
                        ->where('status_atendimento', '=', 2)
                        ->where('at.id', $idat)
                        ->update([
                'status_atendimento' => 3,
                'id_atendente' => $atendente
            ]);  
            }

            app('flasher')->addSuccess('O status do atendimento foi alterado para "Aguardando o assistido".');
        
            return redirect()->back();

        }

        public function inicio($idat)
        {

            $now =  Carbon::now()->format('Y-m-d H:m:d');

            $atendente = session()->get('usuario.id_associado');


            if (DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento') > 3){

                app('flasher')->addError('O início do atendimento já foi registrado.');
        
                return redirect()->back();

            }
            elseif (DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento') <= 3){
                
                DB::table('atendimentos AS at')            
                    ->where('at.id', $idat)
                    ->update([
                'status_atendimento' => 4,
                'dh_inicio' => $now                
            ]);
            
            app('flasher')->addSuccess('O status do atendimento foi alterado para "Em atendimento".');

            return redirect()->back();

            }

        }


        public function tratar($idat, $idas)
        {

            $sit = DB::table('atendimentos AS at')
                        ->where('at.id', $idat)
                        ->where('status_atendimento', '<', 4)
                        ->count();

            $atendido = DB::table('pessoas AS p')
                        ->select('nome_completo AS nm')
                        ->where('p.id', $idas)
                        ->get();

            $verifi = DB::table('encaminhamento AS enc')
                        ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                        ->where('at.id', $idat)
                        ->where('id_tipo_encaminhamento', [2,3])
                        ->count();

            if($sit > 0){

                app('flasher')->addError('Para registrar encaminhamentos o atendimento deve estar no mínimo no status "Em atendimento"');

                return redirect()->back();
            
            }
            if($verifi < 1){
                
                $assistido = DB::table('atendimentos AS at')
                            ->select('at.id as idat', 'at.id_assistido as idas', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido','p1.nome_completo AS nm_1', 'at.id_representante', 'at.id_atendente')
                            ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                            ->where('at.id', $idat)
                            ->get();

                return view('/atendimento-assistido/tratamentos', compact('assistido'));

            }elseif($verifi > 0){

                app('flasher')->addError('Os tratamentos já foram registrados para o atendido '. $atendido[0]->nm);

                return redirect()->back();

            } 

            
        }

        public function entrevistar($idat, $idas)
        {

            $sit = DB::table('atendimentos AS at')
                    ->where('at.id', $idat)
                    ->where('status_atendimento', '<', 4)
                    ->count();           

            $atendido = DB::table('pessoas AS p')
                    ->select('nome_completo AS nm')
                    ->where('p.id', $idas)
                    ->get();

            

            $verifi = DB::table('encaminhamento AS enc')
                    ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                    ->where('at.id', $idat)
                    ->where('id_tipo_encaminhamento', [1])
                    ->count();
            //dd($verifi);

            if($sit > 0){

                app('flasher')->addError('Para registrar encaminhamentos o atendimento deve estar no mínimo no status "Em atendimento"');

                return redirect()->back();
            
            }
            if($verifi < 1){

            $assistido = DB::table('atendimentos AS at')
                        ->select('at.id as idat', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido','p1.nome_completo AS nm_1', 'at.id_representante', 'at.id_atendente')
                        ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                        ->where('at.id', $idat)
                        ->get();

            return view('/atendimento-assistido/entrevistas', compact('assistido'));

            }
            elseif($verifi > 0){

                app('flasher')->addError('As entrevistas já foram registradas para o atendido '. $atendido[0]->nm);

                return redirect()->back();

            } 

        }

        public function final($idat)
        {

            $sit = DB::table('atendimentos AS at')->where('at.id', $idat)->where('status_atendimento', '<', 4)->count();


            if($sit > 0){

                app('flasher')->addError('Para finalizar o atendimento o status mínimo é "Em atendimento"');

                return redirect()->back();
            
            }else{

            $assistido = DB::table('atendimentos AS at')
            ->select('at.id as idat', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido','p1.nome_completo AS nm_1', 'at.id_representante', 'at.id_atendente')
            ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->where('at.id', $idat)
            ->get();

            return view('/atendimento-assistido-afe/finalizar-afe', compact('assistido'));

            }

        }

        public function pre_tema($idat)
        {

            $verifi =  $result = DB::table('registro_tema AS rt')
            ->leftJoin('atendimentos AS at', 'rt.id_atendimento', 'at.id')
            ->where('at.id', $idat)->count();

            $assistido = DB::table('atendimentos AS at')
            ->select('at.id as idat', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido','p1.nome_completo AS nm_1', 'at.id_representante', 'at.id_atendente')
            ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->where('at.id', $idat)
            ->get();

            $result = DB::table('registro_tema AS rt')
            ->leftJoin('atendimentos AS at', 'rt.id_atendimento', 'at.id')
            ->where('at.id', $idat)
            ->select('rt.ies', 'rt.obs', 'rt.coj', 'rt.fam', 'rt.soc', 'rt.prf', 'rt.sau', 'rt.pdg', 'rt.sex', 'rt.adp', 'rt.deq', 'rt.est', 'rt.abo', 'rt.sui', 'rt.dou', 'rt.son', 'rt.esp', 'rt.dpr', 'rt.dqu', 'rt.dts', 'rt.maf' )
            ->get();

            //dd($result);

            return view('/atendimento-assistido-afe/tematicas-afe', compact('assistido', 'result', 'verifi'));

        }


        public function enc_trat(Request $request, $idat, $idas)
        {
            $now = Carbon::now()->format('Y-m-d H:m:s');

            $atendente = session()->get('usuario.id_associado');

            $marcados = DB::table('encaminhamento AS enc')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->where('at.id', $idat)->select('*')->first();

            $harmonia = isset($request->pph) ? 1 : 0;
            $desobsessivo = isset($request->ptd) ? 1 : 0;
           // $integral = isset($request->ptig) ? 1 : 0;
           // $intensivo = isset($request->pti) ? 1 : 0;
            $acolher = isset($request->ga) ? 1 : 0;
            $viver = isset($request->gv) ? 1 : 0;
            //$evangelho = isset($request->gel) ? 1 : 0;
            $quimica = isset($request->gdq) ? 1 : 0;
            //dd($harmonia, $desobsessivo, $integral);

            $atendido = DB::table('pessoas AS p')
            ->select('nome_completo AS nm')
            ->where('p.id', $idas)
            ->get();

            $result = DB::table('tratamento AS tr')
            ->leftJoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->where('at.id_assistido', $idas)
            ->where('enc.id_tipo_tratamento', 1)
            ->where('tr.status','<', 4)
            ->count();

            //dd($result > 0 && $desobsessivo > 0);
            if ($result > 0 && $desobsessivo > 0)
            {

                app('flasher')->addError('Existem tratamentos ativos para '. $atendido[0]->nm);

                return redirect()->back();

            }else
            {
  
                if ($harmonia == 1)
                {
                    DB::table('encaminhamento AS enc')->insert([
                        'dh_enc' => $now,    
                        'id_usuario' => $atendente,
                        'id_tipo_encaminhamento'=> 2,
                        'id_atendimento' =>$idat,
                        'id_tipo_tratamento' => 3,
                        'status_encaminhamento' =>  3
                    ]);
    
                    app('flasher')->addSuccess('O encaminhamento para PPH foi criado com sucesso.');
                }    
                if ($desobsessivo == 1)
                {
                    DB::table('encaminhamento AS enc')->insert([
                        'dh_enc' => $now,    
                        'id_usuario' => $atendente,
                        'id_tipo_encaminhamento'=> 2,
                        'id_atendimento' =>$idat,
                        'id_tipo_tratamento' => 1,
                        'status_encaminhamento' =>  1
                    ]);
    
                    app('flasher')->addSuccess('O encaminhamento para PTD foi criado com sucesso.');
    
                }
                if ($acolher == 1)
                {
                    DB::table('encaminhamento AS enc')->insert([
                        'dh_enc' => $now,    
                        'id_usuario' => $atendente,
                        'id_tipo_encaminhamento'=> 3,
                        'id_atendimento' =>$idat,
                        'id_tipo_tratamento' => 7,
                        'status_encaminhamento' =>  3
                    ]);
    
                    app('flasher')->addSuccess('O encaminhamento para o Grupo Acolher foi criado com sucesso.');
    
                }
                if ($viver == 1)
                {
                    DB::table('encaminhamento AS enc')->insert([
                        'dh_enc' => $now,    
                        'id_usuario' => $atendente,
                        'id_tipo_encaminhamento'=> 3,
                        'id_atendimento' =>$idat,
                        'id_tipo_tratamento' => 10,
                        'status_encaminhamento' =>  3
                    ]);
    
                    app('flasher')->addSuccess('O encaminhamento para o Grupo Viver foi criado com sucesso.');
    
                }
                if ($quimica == 1)
                {
                    DB::table('encaminhamento AS enc')->insert([
                        'dh_enc' => $now,    
                        'id_usuario' => $atendente,
                        'id_tipo_encaminhamento'=> 3,
                        'id_atendimento' =>$idat,
                        'id_tipo_tratamento' => 9,
                        'status_encaminhamento' =>  3
                    ]);
    
                    app('flasher')->addSuccess('O encaminhamento para Grupo de Dependência Química foi criado com sucesso.');
    
                } 
                

            }

            return Redirect('/atendendo-afe');
            
        }


        public function enc_entre(Request $request, $idat)
        {
            $now = Carbon::now()->format('Y-m-d H:m:s');

            $atendente = session()->get('usuario.id_pessoa');



            $ame = isset($request->ame) ? 1 : 0;
            $afe = isset($request->afe) ? 1 : 0;
            $diamo = isset($request->diamo) ? 1 : 0;
            $nutres = isset($request->nutres) ? 1 : 0;
            $evangelho = isset($request->gel) ? 1 : 0;

          //  dd($ame, $afe, $diamo, $nutres  );
          

            if ($ame == 1)
            {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,    
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento'=> 1,
                    'id_atendimento' =>$idat,
                    'id_tipo_entrevista' => 5,
                    'status_encaminhamento' =>  1
                ]);

                app('flasher')->addSuccess('O encaminhamento para a AME foi criado com sucesso.');

            }
            if ($afe == 1)
            {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,    
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento'=> 1,
                    'id_atendimento' =>$idat,
                    'id_tipo_entrevista' => 3,
                    'status_encaminhamento' =>  1
                ]);

                app('flasher')->addSuccess('O encaminhamento para o AFE foi criado com sucesso.');

            }
            if ($diamo == 1)
            {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,    
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento'=> 1,
                    'id_atendimento' =>$idat,
                    'id_tipo_entrevista' => 6,
                    'status_encaminhamento' =>  1
                ]);

                app('flasher')->addSuccess('O encaminhamento para a DIAMO foi criado com sucesso.');

            }
            if ($nutres == 1)
            {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,    
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento'=> 1,
                    'id_atendimento' =>$idat,
                    'id_tipo_entrevista' => 4,
                    'status_encaminhamento' =>  1
                ]);

                app('flasher')->addSuccess('O encaminhamento para o NUTRES foi criado com sucesso.');

            }
            if ($evangelho == 1)
            {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,    
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento'=> 1,
                    'id_atendimento' =>$idat,
                    'id_tipo_entrevista' => 8,
                    'status_encaminhamento' =>  1
                ]);

                app('flasher')->addSuccess('O encaminhamento para o Grupo de Evangelho foi criado com sucesso.');

            }

            return Redirect('/atendendo');

        }

        public function finaliza($idat)
        {

            $now = Carbon::now()->format('Y-m-d H:m:s');

            $atendente = session()->get('usuario.id_associado');

            $sit = DB::table('atendimentos AS at')->where('at.id_atendente', $atendente)->where('at.status_atendimento','<',5)->count();

            $atendendo = DB::table('atendimentos AS at')->where('at.id', $idat)->value('id_atendente');

            $status =  DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento');

            if ($status = 4 && $atendendo <> $atendente ){
                
                app('flasher')->addError('Este atendimento não é sua responsabilidade.');
        
                return redirect('/atendendo');
                
            } elseif($status = 4 && $atendendo = $atendente ){
                DB::table('atendimentos AS at')
                        ->where('status_atendimento', '=', 4)
                        ->where('at.id', $idat)
                        ->update([
                            'status_atendimento' => 5,
                            'id_atendente' => $atendente,
                            'dh_fim' => $now
                        ]);  
            }

            app('flasher')->addSuccess('O status do atendimento foi alterado para "Finalizado".');
        
            return redirect('/atendendo-afe');

        }

        public function meus_atendimentos()
        {

            $atendente = session()->get('usuario.id_associado');
            
            $nome = session()->get('usuario.nome');

            $assistido = DB::table('atendimentos AS at')
                    ->select('at.id AS ida', 'at.observacao',   'p1.id AS idas', 'p1.ddd', 'p1.sexo', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'ps1.nome_completo AS nm_3', 'at.id_atendente', 'ps2.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao AS tst', 'tsx.tipo', 'pa.nome', 'at.status_atendimento', 'p1.dt_nascimento',  't.nm_tca', 't1.nm_tca AS t1', 't2.nm_tca AS t2', 't3.nm_tca AS t3','t4.nm_tca AS t4','t5.nm_tca AS t5','t6.nm_tca AS t6','t7.nm_tca AS t7','t8.nm_tca AS t8','t9.nm_tca AS t9','t10.nm_tca AS t10','t11.nm_tca AS t11','t12.nm_tca AS t12','t13.nm_tca AS t13','t14.nm_tca AS t14','t15.nm_tca AS t15','t16.nm_tca AS t16','t17.nm_tca AS t17','t18.nm_tca AS t18','t19.nm_tca AS t19')
                    
                    ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    
                    ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    
                    ->leftJoin('membro AS m', 'at.id_atendente', 'm.id_associado')
                    ->leftJoin('associado AS ad1', 'm.id_associado', 'ad1.id')
                    ->leftJoin('pessoas AS ps1', 'ad1.id_pessoa', 'ps1.id')
                    ->leftJoin('membro AS m1', 'at.id_atendente_pref', 'm1.id_associado')
                    ->leftJoin('associado AS ad2', 'm1.id_associado', 'ad2.id')
                    ->leftJoin('pessoas AS ps2', 'ad1.id_pessoa', 'ps2.id')           





                    ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    ->leftJoin('tp_sexo AS tsx', 'p1.sexo', 'tsx.id')
                    ->leftJoin('registro_tema AS rt', 'at.id', 'rt.id_atendimento')
                    ->leftJoin('tca AS t', 'rt.ies', 't.id')
                    ->leftJoin('tca AS t1', 'rt.obs', 't1.id')
                    ->leftJoin('tca AS t2', 'rt.coj', 't2.id')
                    ->leftJoin('tca AS t3', 'rt.fam', 't3.id')
                    ->leftJoin('tca AS t4', 'rt.soc', 't4.id')
                    ->leftJoin('tca AS t5', 'rt.prf', 't5.id')
                    ->leftJoin('tca AS t6', 'rt.sau', 't6.id')
                    ->leftJoin('tca AS t7', 'rt.pdg', 't7.id')
                    ->leftJoin('tca AS t8', 'rt.sex', 't8.id')
                    ->leftJoin('tca AS t9', 'rt.adp', 't9.id')
                    ->leftJoin('tca AS t10', 'rt.deq', 't10.id')
                    ->leftJoin('tca AS t11', 'rt.est', 't11.id')
                    ->leftJoin('tca AS t12', 'rt.abo', 't12.id')
                    ->leftJoin('tca AS t13', 'rt.sui', 't13.id')
                    ->leftJoin('tca AS t14', 'rt.dou', 't14.id')
                    ->leftJoin('tca AS t15', 'rt.son', 't15.id')
                    ->leftJoin('tca AS t16', 'rt.esp', 't16.id')
                    ->leftJoin('tca AS t17', 'rt.dpr', 't17.id')
                    ->leftJoin('tca AS t18', 'rt.dqu', 't18.id')
                    ->leftJoin('tca AS t19', 'rt.dts', 't19.id')
                    ->leftJoin('tca AS t20', 'rt.maf', 't20.id')
                    ->where('id_atendente', $atendente)
                    ->distinct('at.dh_chegada')       
                    ->orderBy('at.dh_chegada', 'desc')
                    ->get();

            foreach($assistido as $key => $teste){
                $trata = DB::table('encaminhamento AS enc')
                        ->select('tt.descricao AS tdt')
                        ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                        ->where('enc.id_atendimento', $teste->ida)
                        ->whereNotNull('enc.id_tipo_tratamento')
                        ->get();
                 $teste->tratamentos=$trata; 

                 $entre = DB::table('encaminhamento AS enc')
                        ->select('te.descricao AS tde')
                        ->leftJoin('tipo_entrevista AS te', 'enc.id_tipo_entrevista', 'te.id')
                        ->where('enc.id_atendimento', $teste->ida)
                        ->whereNotNull('enc.id_tipo_entrevista')
                        ->get();
                  $teste->entrevistas=$entre; 
            }

            $now = Carbon::now()->format('Y-m-d');

            $grupo = DB::table('atendente_dia AS ad')
            ->leftJoin('grupo AS g', 'ad.id_grupo', 'g.id' )
            ->where('dh_inicio', '>=', $now)->where('ad.id_associado', $atendente)->value('g.nome');

          // dd($grupo);
           

           return view ('/atendimento-assistido-afe/meus-atendimentos-afe', compact('assistido', 'atendente', 'nome', 'grupo'));

        }

               
        public function tematica(Request $request, $idat){

            $r_tema = DB::table('registro_tema')->where('id_atendimento', $idat)->count();

            $now = Carbon::now()->format('Y-m-d H:m:s');

           $maf = isset($request->maf) ? 1 : null;          
           $ies = isset($request->ies) ? 2 : null;
           $obs = isset($request->obs) ? 3 : null;           
           $coj = isset($request->coj) ? 4 : null;
           $fam = isset($request->fam) ? 5 : null;
           $soc = isset($request->soc) ? 6 : null;
           $prf = isset($request->prf) ? 7 : null;
           $sau = isset($request->sau) ? 8 : null;
           $pdg = isset($request->pdg) ? 9 : null;
           $sex = isset($request->sex) ? 10 : null;
           $dts = isset($request->dts) ? 11 : null;
           $adp = isset($request->adp) ? 12 : null;
           $deq = isset($request->deq) ? 13 : null;
           $est = isset($request->est) ? 14 : null;
           $abo = isset($request->abo) ? 15 : null;
           $sui = isset($request->sui) ? 16 : null;
           $dou = isset($request->dou) ? 17 : null;
           $son = isset($request->son) ? 18 : null;
           $esp = isset($request->esp) ? 19 : null;
           $dpr = isset($request->dpr) ? 20 : null;
           $dqu = isset($request->dqu) ? 21 : null;
           
            
           
        
         // dd($ies, $obs, $coj);

            DB::table('atendimentos AS at')->where('id', $idat)->update([

                'observacao' => $request->input('nota') 
            ]);


            if ($r_tema > 0 ){

                app('flasher')->addError("As temáticas do atendimento $idat já foram registradas.");

                return Redirect('/atendendo-afe');

            }else{

            DB::table('registro_tema AS rt')->where('id', $idat)->insert([

                'id_atendimento' => $idat,
                'maf' => $maf,
                'ies' => $ies,
                'obs' => $obs,
                'coj' => $coj,
                'fam' => $fam,
                'soc' => $soc,
                'prf' => $prf,
                'sau' => $sau,
                'pdg' => $pdg,
                'sex' => $sex,
                'dts' => $dts,
                'adp' => $adp, 
                'deq' => $deq,
                'est' => $est,
                'abo' => $abo,
                'sui' => $sui,
                'dou' => $dou,
                'son' => $son,
                'esp' => $esp,
                'dpr' => $dpr,
                'dqu' => $dqu

            ]);

            app('flasher')->addSuccess('Os temas foram salvos com sucesso.');

            return Redirect('/atendendo-afe');
            }

        }



}
