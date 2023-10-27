<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tipo_fato;
use Illuminate\Database\DBAL\TimestampType;

    class FatosController extends Controller
    {
        public function index() {

            $lista = DB::select('select id, descricao from tipo_fato ORDER BY id ASC') ;
        

            return view ('/administrativo/gerenciar-fatos' , compact('lista'));
        }

        public function edit($id) {
        
            $lista = DB::select("select * from tipo_fato where id = $id"); 
            
        
            return view ('\administrativo\editar-fatos' , compact('lista'));

        }


        public function update(Request $request, string $id)
        {

            Tipo_fato::findOrFail($request->id)->update([ 'descricao' => $request->descricao ]) ;
    
            return redirect('/gerenciar-fatos'); 

        }

        
        public function criar()
        {
            app('flasher')->addInfo('O cadastro do fato foi realizado com sucesso.');
            return view ('/administrativo/criar-fatos');
    
        
        }

            public function incluir(Request $request)
            
        {
        

            DB::table('tipo_fato')->insert([
                'descricao' => $request->input('fato')
            ]);

            app('flasher')->addInfo('O cadastro do fato foi realizado com sucesso.');

        

            return redirect('/gerenciar-fatos');
        }



        
            public function destroy( $id)
            {
                $teste=session()->get('usuario');
              
                $verifica=DB::table('historico_venus') -> where('fato',$id)->count('fato');
               
               
                $data = date("Y-m-d H:i:s");
                
                


                if( $verifica == 0 ) {
                    // dd($verifica);
                    
                    DB::table('historico_venus')->insert([
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'data' => $data,
                        'fato' => 6
                        
                    ]);
        
                         
       DB::table('tipo_fato')->where('id', $id)->delete();


       app('flasher')->addInfo('Excluido com sucesso.');
       return redirect('/gerenciar-fatos');

                }
              
                app('flasher')->addInfo('o fato não pode ser excluido pois existe a referência na tabela historico.');

                return redirect('/gerenciar-fatos');
                 
             
            }

    

 }






        
        