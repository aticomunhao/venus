<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\select;

class AtendimentoFraternoController extends Controller
{
    public function index(Request $request)
    {

     
        $atendente = session()->get('usuario.id_pessoa');

        $pref_att = session()->get('usuario.sexo');

        $nome = session()->get('usuario.nome');

        //$now = Carbon::now()->format('Y-m-d H:m:s');
        $now =  Carbon::now()->format('Y-m-d');

        $assistido = DB::table('atendimentos AS at')
                    ->select('at.id AS idat', 'p1.id AS idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'p3.nome_completo AS nm_3', 'at.id_atendente', 'p4.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tx.tipo','pa.nome', 'at.id_prioridade', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id_pessoa')
                    ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')                                           
                    ->groupby('at.id', 'p1.id', 'p2.nome_completo', 'p3.nome_completo', 'p4.nome_completo', 'ts.descricao', 'tx.tipo', 'pa.nome', 'pr.descricao', 'pr.sigla')
                    ->orderby('status_atendimento', 'ASC')
                    ->orderBy( 'at.id_prioridade', 'ASC')
                    ->orderby('at.dh_chegada', 'ASC')
                    ->get();        

                    //dd($pref_att);   
                    

        return view ('/atendimento-assistido/atendendo', compact('assistido', 'atendente', 'now', 'nome'));

        }

        public function history($idat, $idas)
        {
            $assistido = DB::table('atendimentos AS at')
            ->select('at.id AS ida', 'p1.id AS idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'p3.nome_completo AS nm_3', 'at.id_atendente', 'p4.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tx.tipo','pa.nome')
            ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id_pessoa')
            ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
            ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->where('p1.id', $idas)                                     
            ->groupby('at.id', 'p1.id', 'p2.nome_completo', 'p3.nome_completo', 'p4.nome_completo', 'ts.descricao', 'tx.tipo', 'pa.nome')
            ->orderBy('at.dh_chegada', 'desc')
            ->get();

            $atendente = session()->get('usuario.id_pessoa');

            $sit = DB::table('atendimentos AS at')->where('at.id', $idat)->where('at.status_atendimento','>',0)->count();

            if ($sit > 0){

                app('flasher')->addError('Não é permitido atender dois assistidos ao mesmo tempo.');
                
            }else{
                DB::table('atendimentos AS at')
            ->where('status_atendimento', '=', 1)
            ->where('at.id', $idat)
            ->update([
                'status_atendimento' => 2,
                'id_atendente' => $atendente
            ]);  
            }


           

            return view ('\atendimento-assistido\historico-assistido', compact('assistido', 'atendente'));
        }

        public function fimanalise($idat)
        {

            $atendente = session()->get('usuario.id_pessoa');

            $sit = DB::table('atendimentos AS at')->where('at.id', $idat)->where('at.status_atendimento', 2)->count();
            
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

            $now = Carbon::now()->format('Y-m-d H:m:s');

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

        public function encaminha($idat)
        {
            
        }


}
