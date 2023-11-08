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
    
<<<<<<< HEAD
        
            return view('salas/gerenciar-salas' , compact('salas'));
=======
        return view('salas/gerenciar-salas' , compact('salas'));
    
    }

    public function criar()
 
     

    {
        $salas = db::select('select * from salas');


       
        //
        return view('salas/criar-salas', compact('salas'));

        // return view('salas/criar-salas');
>>>>>>> master
        
        }

<<<<<<< HEAD
        public function criar()
    
=======

        }    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $salas = db::select('select * from salas');


        return view('salas/gerenciar-salas', compact('salas'));
>>>>>>> master
        

        {
            $salas = db::select('select * from salas');


                
                
            //
            return view('salas/criar-salas', compact('salas'));

   
            


            }    /**
        * Display the specified resource.
        */
        public function show(string $id)
        {

       


            return view('salas/visualizar-salas');
            
            //
        }


        public function store(Request $request)
        {
           
            
            
            $ativo = isset($request->checked) ? 1 : 0; 
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

<<<<<<< HEAD
            // dd( isset($request->ar_condicionado), $ar_condicionado, $projetor, $computador);
=======

>>>>>>> master
            
        
            DB::table('salas')->insert([

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
              
                
                
              

          

            app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');

    


            
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
           

                    $ativo = isset($request->checked) ? 1 : 0; 

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

                    // dd( isset($request->ar_condicionado), $ar_condicionado, $projetor, $computador);





                
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
                    
            


          
                
                    DB::table('historico_venus')->insert([
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'data' => $data,
                        'fato' => 0
                        
                ]);

                    
                    DB::table('salas')->where('id', $id)->delete();


                    app('flasher')->addSuccess('Excluido com sucesso.');
                    return redirect('/gerenciar-salas');
     
                    
                
                }                

        
    }
