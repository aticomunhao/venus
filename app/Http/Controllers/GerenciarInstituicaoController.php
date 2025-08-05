<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class GerenciarInstituicaoController extends Controller
{
    public function index(Request $request)
    {
        $lista = DB::table('instituicao')
            ->select('id', 'nome_fantasia', 'razao_social', 'cnpj', 'email_contato', 'site', 'status')
            ->when($request->nome_fantasia, function ($query, $value) {
                return $query->where('nome_fantasia', $value);
            })
            ->when($request->razao_social, function ($query, $value) {
                return $query->where('razao_social', $value);
            })
            ->when($request->email_contato, function ($query, $value) {
                return $query->where('email_contato', $value);
            })
            ->when($request->cnpj, function ($query, $value) {
                return $query->where('cnpj', 'like', '%' . $value . '%');
            })
            ->when($request->site, function ($query, $value) {
                return $query->where('site', $value);
            })
            ->when($request->status, function ($query, $value) {
                return $query->where('status', $value);
            })
            ->orderBy('nome_fantasia')
            ->get();

        // Para popular os selects
        $pesquisa = DB::table('instituicao')
            ->select('nome_fantasia', 'razao_social', 'cnpj', 'email_contato', 'site', 'status')
            ->get();

        return view('/instituicao/gerenciar-instituicao', compact('lista', 'pesquisa'));
    }
    public function create()
    {
        $instituicoes = DB::table('instituicao')
            ->select('id', 'nome_fantasia', 'razao_social', 'cnpj', 'email_contato', 'site', 'status')
            ->get();

        return view('/instituicao/incluir-instituicao', compact('instituicoes'));
    }
    public function store(Request $request)
    {
        // Inserção dos dados na tabela instituicao
        DB::table('instituicao')->insert([
            'nome_fantasia' => $request->input('nome_fantasia'),
            'razao_social' => $request->input('razao_social'),
            'cnpj' => $request->input('cnpj'),
            'email_contato' => $request->input('email_contato'),
            'site' => $request->input('site'),
            'status' => '1',
        ]);

        // Redirecionamento com mensagem de sucesso
        app('flasher')->addSuccess('Instituição incluída com sucesso!');
        return redirect('/gerenciar-instituicao');
    }
}
