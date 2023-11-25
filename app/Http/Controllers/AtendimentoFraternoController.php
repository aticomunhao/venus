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

class AtendimentoFraternoController extends Controller
{

    public function index(Request $request)
    {

     
        $atendente = session()->get('usuario.id_pessoa');

        $pref_att = session()->get('usuario.sexo');

        $nome = session()->get('usuario.nome');

        $now =  Carbon::now()->format('Y-m-d');
        //dd($atendente);

        $assistido = DB::table('atendimentos AS at')
                    ->select('at.id AS idat', 'p1.id AS idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'p3.nome_completo AS nm_3', 'at.id_atendente', 'p4.nome_completo AS nm_4', 'at.pref_tipo_atendente AS pta', 'ts.descricao', 'tx.tipo','pa.nome', 'at.id_prioridade', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'at.status_atendimento')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id_pessoa')
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

            return view ('/atendimento-assistido/atendendo', compact('assistido', 'atendente', 'now', 'nome'));

        }

        public function atende_agora()
        {
            $atendente = session()->get('usuario.id_pessoa');
            $pref_att = session()->get('usuario.sexo');
            $atendendo = DB::table('atendimentos')->where('id_atendente', $atendente)->where('status_atendimento', '<', 5)->count();
            $assistido = DB::table('atendimentos')->where('status_atendimento', 1)->count();
            

            if ($atendendo > 0){

                app('flasher')->addError('Você não pode atender dois assistidos ao mesmo tempo.');

                return redirect('/atendendo');

            }elseif ($assistido < 1){

                app('flasher')->addError('Todos os assistidos foram atendidos.');

                return redirect('/atendendo');


            }elseif ($atendendo < 1){

                DB::table('atendimentos')
                    ->where('status_atendimento', '1')
                    ->whereNull('id_atendente_pref')
                    ->orWhere('id_atendente_pref', $pref_att)           
                    ->whereNull('pref_tipo_atendente')
                    ->orWhere('pref_tipo_atendente', $atendente )
                    ->orderby('id_prioridade')->orderBy('dh_chegada')
                    ->limit(1)
                    ->update([
                            'id_atendente' => $atendente
                    ]);

                app('flasher')->addSuccess('O assistido foi selecionando com sucesso.');

                return redirect('/atendendo');

            }


        }


        public function history($idat, $idas)
        {
            $assistido = DB::table('atendimentos AS at')
            ->select('at.id AS ida', 'p1.id AS idas', 'p1.ddd', 'p1.sexo', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'p3.nome_completo AS nm_3', 'at.id_atendente', 'p4.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tsx.tipo', 'pa.nome', 'at.status_atendimento', 'p1.dt_nascimento')
            ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id_pessoa')
            ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
            ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tp_sexo AS tsx', 'p1.sexo', 'tsx.id' )
            ->where('p1.id', $idas)                                    
            ->groupby('at.id', 'p1.id', 'p2.nome_completo', 'p3.nome_completo', 'p4.nome_completo', 'ts.descricao', 'pa.nome', 'tsx.tipo')
            ->orderBy('at.dh_chegada', 'desc')
            ->get();

            $atendente = session()->get('usuario.id_pessoa');

            $nome = DB::table('atendimentos AS at')->select('at.id_atendente')->where('at.id', $idat);

            $atendendo = DB::table('atendimentos AS at')->where('at.id', $idat)->value('id_atendente');
        //dd($atendendo);
            $status = DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento');
            
            $sit = DB::table('atendimentos AS at')->where('at.id_atendente', $atendente)->where('at.status_atendimento','<',5)->count();

            if ($sit > 0 && $atendendo == null)
            {
                app('flasher')->addError('Não é permitido atender dois assistidos ao mesmo tempo.');

                return redirect('/atendendo');

            }

            if ($atendendo = $atendente && $status > 1)
            {
                app('flasher')->addInfo('Retomando análise.');

                return view ('/atendimento-assistido/historico-assistido', compact('assistido', 'atendente'));

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

            return view ('/atendimento-assistido/historico-assistido', compact('assistido', 'atendente'));
        }           
//dd($assistido);
            

        public function fimanalise($idat)
        {

            $atendente = session()->get('usuario.id_pessoa');

            $sit = DB::table('atendimentos AS at')->where('at.id', $idat)->where('at.status_atendimento', 2)->count();
            
            $status =  DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento');

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

            $now =  Carbon::now()->format('Y-m-d');

            $atendente = session()->get('usuario.id_pessoa');

            $sit = DB::table('atendimentos AS at')->where('at.id', $idat)->where('at.status_atendimento', 3)->count();
            
            if ($sit = 1){
                DB::table('atendimentos AS at')
            ->where('status_atendimento', '=', 3)
            ->where('at.id', $idat)
            ->update([
                'status_atendimento' => 4,
                'id_atendente' => $atendente,
                'dh_inicio' => $now
            ]);  
            }

            app('flasher')->addSuccess('O status do atendimento foi alterado para "Em atendimento".');
        
            return redirect()->back();

        }


        public function tratar($idat)
        {

            $assistido = DB::table('atendimentos AS at')
            ->select('at.id AS idat', 'p1.id AS idas', 'p1.ddd', 'p1.sexo', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'p3.nome_completo AS nm_3', 'at.id_atendente', 'p4.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tsx.tipo', 'pa.nome', 'at.status_atendimento', 'p1.dt_nascimento')
            ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id_pessoa')
            ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
            ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tp_sexo AS tsx', 'p1.sexo', 'tsx.id' )
            ->where('at.id', $idat)                                    
            ->groupby('at.id', 'p1.id', 'p2.nome_completo', 'p3.nome_completo', 'p4.nome_completo', 'ts.descricao', 'pa.nome', 'tsx.tipo')
            ->orderBy('at.dh_chegada', 'desc')
            ->get();

            return view('/atendimento-assistido/tratamento', compact('assistido'));

        }


        public function enc_trat(Request $request, $idat)
        {
            $now = Carbon::now()->format('Y-m-d H:m:s');

            $atendente = session()->get('usuario.id_pessoa');

            $harmonia = isset($request->pph) ? 1 : 0;
            $desobsessivo = isset($request->ptd) ? 1 : 0;
            $integral = isset($request->ptig) ? 1 : 0;
            $intensivo = isset($request->pti) ? 1 : 0;

            //dd($harmonia, $desobsessivo, $integral);

            if ($harmonia == 1)
            {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,    
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento'=> 2,
                    'id_atendimento' =>$idat,
                    'id_tipo_tratamento' => 3,
                    'status_encaminhamento' =>  1
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
            if ($integral == 1)
            {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,    
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento'=> 2,
                    'id_atendimento' =>$idat,
                    'id_tipo_tratamento' => 6,
                    'status_encaminhamento' =>  1
                ]);

                app('flasher')->addSuccess('O encaminhamento para PTIg foi criado com sucesso.');

            }
            if ($intensivo == 1)
            {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,    
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento'=> 2,
                    'id_atendimento' =>$idat,
                    'id_tipo_tratamento' => 2,
                    'status_encaminhamento' =>  1
                ]);

                app('flasher')->addSuccess('O encaminhamento para PTI foi criado com sucesso.');

            }

            return Redirect('/atendendo');
            
        }


        public function enc_entre(Request $request, $idat)
        {
            $now = Carbon::now()->format('Y-m-d H:m:s');

            $atendente = session()->get('usuario.id_pessoa');

            $ame = isset($request->ame) ? 1 : 0;
            $afe = isset($request->afe) ? 1 : 0;
            $diamo = isset($request->diamo) ? 1 : 0;
            $nutres = isset($request->nutres) ? 1 : 0;

            //dd($integral, $especifico, $armonia, $tratamento  );

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
                    'id_tipo_encaminhamento'=> 2,
                    'id_atendimento' =>$idat,
                    'id_tipo_entrevista' => 4,
                    'status_encaminhamento' =>  1
                ]);

                app('flasher')->addSuccess('O encaminhamento para o NUTRES foi criado com sucesso.');

            }

            return Redirect('/atendendo');

        }

        public function finaliza($idat)
        {

            $now = Carbon::now()->format('Y-m-d H:m:s');

            $atendente = session()->get('usuario.id_pessoa');

            $sit = DB::table('atendimentos AS at')->where('at.id_atendente', $atendente)->where('at.status_atendimento','<',5)->count();

            $atendendo = DB::table('atendimentos AS at')->where('at.id', $idat)->value('id_atendente');

            $status =  DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento');

            if ($status = 4 && $atendendo <> $atendente ){
                
                app('flasher')->addError('Este atendimento não é sua responsabilidade.');
        
                return redirect()->back();
                
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
        
            return redirect()->back();

        }

        public function meus_atendimentos()
        {

            $atendente = session()->get('usuario.id_pessoa');
            
            $nome = session()->get('usuario.nome');

            $assistido = DB::table('atendimentos AS at')
            ->select('at.id AS ida', 'p1.id AS idas', 'p1.ddd', 'p1.sexo', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'p3.nome_completo AS nm_3', 'at.id_atendente', 'p4.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tsx.tipo', 'pa.nome', 'at.status_atendimento', 'p1.dt_nascimento')
            ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id_pessoa')
            ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
            ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tp_sexo AS tsx', 'p1.sexo', 'tsx.id' )
            ->where('id_atendente', $atendente)                                    
            ->groupby('at.id', 'p1.id', 'p2.nome_completo', 'p3.nome_completo', 'p4.nome_completo', 'ts.descricao', 'pa.nome', 'tsx.tipo')
            ->orderBy('at.dh_chegada', 'desc')
            ->get();

            return view ('/atendimento-assistido/meus-atendimentos', compact('assistido', 'atendente', 'nome'));

            

        }

}
