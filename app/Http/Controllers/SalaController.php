<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
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
        //
        
     
        DB::table('salas')->insert([
            'nome'=>$request->input('nome'),
            'numero'=>$request->input('numero'),
            'nr_lugares'=>$request->input('nr_lugares'),
            'localizacao'=>$request->input('localizacao'),
            'projetor'=>$request->input('projetor'),
            'computador'=>$request->input('computador'),
            'quadro'=>$request->input('quadro'),
            'ar_condicionado'=>$request->input('ar_condicionado'),
            'ventilador'=>$request->input('ventilador'),
            'som'=>$request->input('som'),
            'computador'=>$request->input('computador'),
            'tela_projetor'=>$request->input('tela_projetor'),
            'controle'=>$request->input('controle'),
            'mesa'=>$request->input('mesa')
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
        //
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
