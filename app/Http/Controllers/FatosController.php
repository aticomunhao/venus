<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FatosController extends Controller
{
    public function index() {

        $lista = DB::select('select id, descricao from tipo_fato');

        return view ('/administrativo/gerenciar-fatos' , compact('lista'));
    }



    public function edit($id) {

        $lista = DB::select('select id, descricao from tipo_fato');
        
        return view ('\administrativo\editar-fatos' , compact('lista'));

    }
}


