<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;


class SalaController extends Controller
{
    public function index() {
  
    
        return view('salas/gerenciar-salas');
    
    }

    public function criar()
    {
        
       
        //

        return view('/salas/criar-salas');
        


        }    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
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
            'pc'=>$request->input('pc'),
            'quadro'=>$request->input('quadro'),
            'ar-condicionado'=>$request->input('ar-condicionado'),
            'ventilador'=>$request->input('ventilador'),
            'som'=>$request->input('som'),
            'pc'=>$request->input('pc'),
            'tela'=>$request->input('tela'),
            'controle'=>$request->input('controle'),
            'mesa'=>$request->input('mesa')
        ]);
        app('flasher')->addInfo('O cadastro do fato foi realizado com sucesso.');
        return redirect('/salas/cr-salas');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}



//       