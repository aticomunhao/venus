<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Psy\debug;

class SalaController extends Controller
{
    public function index() {

        $salas = db::select('select * from salas');
  
    
        return view('salas/gerenciar-salas' , compact('salas'));
    
    }

    public function criar()
    {
        
       
        //


        return view('salas/criar-salas');
        


        }    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('salas/gerenciar-salas');
        
        //
    }


     public function store(Request $request)
    {
        //DD
        $ativo = isset($request->checked) ? 1 : 0;   
     
        DB::table('salas')->insert([
            'nome'=>$request->input('nome'),
            'numero'=>$request->input('numero'),
            'nr_lugares'=>$request->input('nr_lugares'),
            'localizacao'=>$request->input('localizacao'),
            'tamanho_sala'=>$request->input('tamanho_sala'),
            'projetor'=>$request->boolean('projetor')? 1:0,
            'computador'=>$request->boolean('computador') ? 1:0,
            'quadro'=>$request->boolean('quadro') ? 1:0,
            'ar_condicionado' => $request->boolean('ar_condicionado') ? 1:0,
            'ventilador'=>$request->boolean('ventilador') ?1:0,
            'som'=>$request->boolean('som') ? 1:0,
            'computador'=>$request->boolean('computador')? 1:0,
            'tela_projetor'=>$request->boolean('tela_projetor')? 1:0,
            'controle'=>$request->boolean('controle') ?1:0,
            'luz_azul'=>$request->boolean('luz_azul')? 1:0,
            'bebedouro'=>$request->boolean('bebedouro')? 1:0,
            'armarios'=>$request->boolean('armarios')? 1:0
            
               ]);

        //dd($data);

        app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');

   

//        return view('salas/gerenciar-salas');

        
        return redirect('gerenciar-salas');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
      {
        $sala = DB::select("select * from salas where id = $id"); 
            
    
            return view ('salas/editar-salas' , compact('sala'));

          

        }

    

   
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)

        {
            
            

            $ar_condicionado = isset($request->ar_condicionado) ? 1 : 0;
            $projetor = isset($request->projetor) ? 1 : 0;
            $quadro = isset($request->quadro) ? 1 : 0;
            $tela_projetor = isset($request->tela_projetor) ? 1 : 0;
            $ventilador = isset($request->ventilador) ? 1 : 0;
            $computador = isset($request->computador) ? 1 : 0;
            $controle = isset($request->controle) ? 1 : 0;
            $som = isset($request->som) ? 1 : 0;
            $luz_azul = isset($request->luz_azul) ? 1 : 0;
            $bebedouro = isset($request->bebedouro) ? 1 : 0;
            $armarios = isset($request->armarios) ? 1 : 0;

    //dd($ar_condicionado);
            
            Sala::findOrFail($request->id)->update([
                'nome' => $request->input('nome'),
                'numero' => $request->input('numero'),
                'nr_lugares'=>$request->input('nr_lugares'),
                'localizacao'=>$request->input('localizacao'),          
                'projetor'=>$projetor,
                'quadro'=>$quadro,
                'tela_projetor'=>$tela_projetor,
                'ventilador'=> $ventilador,
                'ar_condicionado' => $ar_condicionado,
                'computador'=> $computador,
                'controle'=> $controle,
                'som'=> $som,
                'luz_azul'=> $luz_azul,
                'bebedouro'=> $bebedouro,
                'armarios'=> $armarios,
                'tamanho_sala'=>$request->input('tamanho_sala')
                ]) ;

                   
               
             return redirect ('gerenciar-salas');
     
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $teste=session()->get('usuario');
              
        $verifica=DB::table('historico_venus') -> where('fato',$id)->count('fato');
       
       
        $data = date("Y-m-d H:i:s");
        
        


        // if( $verifica == 0 ) {
            // dd($verifica);
            
            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $data,
                'fato' => 0
                
            ]);

                 
        DB::table('salas')->where('id', $id)->delete();


        app('flasher')->addSuccess('Excluido com sucesso.');
        return redirect('/gerenciar-salas');

                // }
            
                // app('flasher')->addInfo('o fato não pode ser excluido pois existe a referência na tabela historico.');

                // return redirect('/gerenciar-salas');
                
            
            }                

    
}
