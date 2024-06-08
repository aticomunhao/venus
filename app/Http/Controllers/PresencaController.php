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

            try {
            $now = Carbon::now()->format('Y-m-d');
        
            $lista = DB::table('atendimentos as atd')
            ->select('p.nome_completo', 'p.cpf', 'atd.id', 'atd.dh_marcada')
            ->leftJoin('pessoas as p', 'atd.id_assistido', 'p.id')
            ->where('status_atendimento',7)
            ->where('afe', true);



        
            if ($request->nome_pesquisa) {
                $lista = $lista->where('nome_completo', 'ilike', "%$request->nome_pesquisa%");
            }
        
         
           
            $lista = $lista->get();
        
            
        
            $stat = DB::table('tipo_status_tratamento')->select('id', 'nome')->get();
            $dia = DB::table('tipo_dia')->select('id', 'nome')->get();

        
            return view('presenças.gerenciar-presenca', compact('lista', 'stat', 'now', 'dia'));
        }
        catch(\Exception $e){

            $code = $e->getCode( );
            return view('gerenciar-presenca erro.erro-inesperado', compact('code'));
                }
            }
    
        public function criar(Request $request, string $idtr) {
            
        try{
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
        
        catch(\Exception $e){

            $code = $e->getCode( );
            return view('administrativo-erro.erro-inesperado', compact('code'));
                }
            }


            public function destroy( $id)
            {
              
         $deletar = DB::table('atendimentos')->where('id', $id)->get();
        $teste = session()->get('usuario');

        $verifica = DB::table('historico_venus')->where('fato', $id)->count('fato');


        $data = date("Y-m-d H:i:s");





        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 36,
            'obs' => $id

        ]);


        DB::table('atendimentos')->where('id', $id)->delete();


        app('flasher')->addError('Excluido com sucesso.');
      

                return redirect('/gerenciar-presenca');


            }
        }
 







