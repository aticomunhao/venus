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
        
     
        DB::table('salas')->insert([
            'nome'=>$request->input('nome'),
            'numero'=>$request->input('numero'),
            'nr_lugares'=>$request->input('nr_lugares'),
            'localizacao'=>$request->input('localizacao'),
            'tamanho_sala'=>$request->input('tamanho_sala'),
            'projetor'=>$request->has('projetor')? 1:0,
            'computador'=>$request->has('computador') ? 1:0,
            'quadro'=>$request->has('quadro') ? 1:0,
            'ar_condicionado' => $request->has('ar_condicionado') ? 1:0,
            'ventilador'=>$request->has('ventilador') ?1:0,
            'som'=>$request->has('som') ? 1:0,
            'computador'=>$request->has('computador')? 1:0,
            'tela_projetor'=>$request->has('tela_projetor')? 1:0,
            'controle'=>$request->has('controle') ?1:0,
            'luz_azul'=>$request->has('luz_azul')? 1:0,
            'bebedouro'=>$request->has('bebedouro')? 1:0,
            'armarios'=>$request->has('armarios')? 1:0
            
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
            // Sala::findOrFail($request->id)->update([

            Sala::findOrFail($request->id)->update([
                'nome' => $request->nome,
                'numero' => $request->numero,
                'nr_lugares'=>$request->nr_lugares,
                'localizacao'=>$request->localizacao,
                'projetor'=>$request->projetor,
                'quadro'=>$request->quadro,
                'tela_projetor'=>$request->tela_projetor,
                'ventilador'=>$request->ventilador,
                'ar_condicionado' =>$request->ar_condicionado,
                'computador'=>$request->computador,
                'controle'=>$request->controle,
                'som'=>$request->som,
                'luz_azul'=>$request->luz_azul,
                'bebedouro'=>$request->bebedouro,
                'armarios'=>$request->armarios,
                'tamanho_sala'=>$request->tamanho_sala


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
