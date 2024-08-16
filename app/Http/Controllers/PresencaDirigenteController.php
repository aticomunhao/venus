<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tipo_fato;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Support\Carbon;


class PresencaDirigenteController extends Controller
{
    // Exibe a tela de dar presença
    public function index(Request $request)
    {
        // Obtém todos os grupos
        $grupos = DB::table('grupos')->get();
        $pessoas = [];

        // Verifica se um grupo foi selecionado e busca as pessoas desse grupo
        if ($request->has('grupo') && !empty($request->input('grupo'))) {
            $pessoas = DB::table('pessoas')
                ->where('grupo_id', $request->input('grupo'))
                ->get();
        }

        return view('gerenciarpresencardirigente', compact('grupos', 'pessoas'));
    }

    // Marca a presença de uma pessoa
    public function store(Request $request)
    {
        $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
        ]);

        // Insere a presença na tabela 'presencas'
        DB::table('presencas')->insert([
            'pessoa_id' => $request->input('pessoa_id'),
            'data_presenca' => now(),
        ]);

        return redirect()->back()->with('success', 'Presença marcada com sucesso!');
    }
}
