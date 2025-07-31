<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class GerenciarEstudosExternosController extends Controller
{
    public function index(Request $request)
    {
        $lista = DB::table('cursos_externos as ce')
            ->leftJoin('pessoas as p', 'ce.id_pessoa', 'p.id')
            ->select(
                'p.nome_completo as nome_completo',
                'id_tipo_atividade',
                'instituicao',
                'data_inicio',
                'data_fim',
                'ce.status',
                'documento_comprovante',
                'ce.setor',
                'ce.id',
            )
            ->get();


        return view('/estudos-externos/gerenciar-estudos-externos', compact('lista'));
    }
}
