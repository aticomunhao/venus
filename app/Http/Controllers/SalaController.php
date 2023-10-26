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

        return view('/salas/criar-sala');
        


        }    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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