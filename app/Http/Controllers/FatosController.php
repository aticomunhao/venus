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
  
        return redirect('/gerenciar-fatos')->with('msg', 'Descrição editada com sucesso!'); 

    }

    
     public function criar()
    {
        return view ('/administrativo/criar-fatos');
   
    
    }

        public function incluir(Request $request)
        
    {
       

        DB::table('tipo_fato')->insert([
            'descricao' => $request->input('fato')
        ]);

        app('flasher')->addInfo('O cadastro do fato foi realizado com sucesso.');

     

        return redirect('/gerenciar-fatos')->with('msg', 'Incluída com sucesso!');
    }



    
        public function destroy(string $id)
        {
       DB::table('tipo_fato')->where('id', $id)->delete();
       return redirect('/gerenciar-fatos')->with('msg', 'Exluida com sucesso!');
        }
    }






    
    /**
     * Update the specified resource in storage.
     *     public function update(Request $request, string $id)
*/
    

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
    

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     */


