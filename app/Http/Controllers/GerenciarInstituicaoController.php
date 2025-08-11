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
        $uf = DB::table('tp_uf')->get();
        $instituicoes = DB::table('instituicao')
            ->leftJoin('tp_uf', 'instituicao.uf', 'tp_uf.id')
            ->get();

        return view('/instituicao/incluir-instituicao', compact('instituicoes', 'uf'));
    }
    public function store(Request $request)
    {
        $cnpj = preg_replace('/\D/', '', $request->cnpj);
        $cep = preg_replace('/\D/', '', $request->cep);
        //dd($request->all(), $cnpj);
        // Inserção dos dados na tabela instituicao
        DB::table('instituicao')->insert([
            'nome_fantasia' => $request->input('nome_fantasia'),
            'razao_social' => $request->input('razao_social'),
            'inscricao_estadual' => $request->input('insc_est'),
            'nome_contato' => $request->input('nome_cont'),
            'ibge' => $request->input('ibge'),
            'cep' => $cep,
            'logradouro' => $request->input('logradouro'),
            'bairro' => $request->input('bairro'),
            'uf' => $request->input('uf'),
            'cidade' => $request->input('cidade'),
            'complemento' => $request->input('complemento'),
            'unidade' => $request->input('unidade'),
            'gia' => $request->input('gia'),
            'numero' => $request->input('numero'),
            'cnpj' => $cnpj,
            'email_contato' => $request->input('email_contato'),
            'site' => $request->input('site'),
            'status' => '1',
        ]);

        // Redirecionamento com mensagem de sucesso
        app('flasher')->addSuccess('Instituição incluída com sucesso!');
        return redirect('/gerenciar-instituicao');
    }
    public function edit($id)
    {
        $uf = DB::table('tp_uf')->get();
        $instituicao = DB::table('instituicao')
            ->leftJoin('tp_uf', 'instituicao.uf', 'tp_uf.id')
            ->leftJoin('tp_cidade', 'tp_cidade.id_cidade', 'instituicao.cidade')
             ->select(
                'instituicao.id as id',
                'instituicao.nome_fantasia',
                'instituicao.razao_social',
                'instituicao.inscricao_estadual',
                'instituicao.nome_contato',
                'instituicao.ibge',
                'instituicao.cep',
                'instituicao.logradouro',
                'instituicao.bairro',
                'tp_uf.id as uf',
                'tp_uf.sigla as sigla',
                'instituicao.cidade as cidade_id',
                'tp_cidade.descricao as cidade',
                'instituicao.complemento',
                'instituicao.unidade',
                'instituicao.gia',
                'instituicao.numero',
                'instituicao.cnpj',
                'instituicao.email_contato',
                'instituicao.site'
            )
            ->where('instituicao.id', $id)
            ->first();

            if (!$instituicao) {
            app('flasher')->addError('Instituição não encontrada!');
            return redirect('/gerenciar-instituicao');
        }

        return view('/instituicao/editar-instituicao', compact('instituicao', 'uf'));
    }
    public function update(Request $request, $id)
    {
        $cnpj = preg_replace('/\D/', '', $request->cnpj);
        $cep = preg_replace('/\D/', '', $request->cep);

        DB::table('instituicao')->where('id', $id)->update([
            'nome_fantasia' => $request->input('nome_fantasia'),
            'razao_social' => $request->input('razao_social'),
            'inscricao_estadual' => $request->input('insc_est'),
            'nome_contato' => $request->input('nome_cont'),
            'ibge' => $request->input('ibge'),
            'cep' => $cep,
            'logradouro' => $request->input('logradouro'),
            'bairro' => $request->input('bairro'),
            'uf' => $request->input('uf'),
            'cidade' => $request->input('cidade'),
            'complemento' => $request->input('complemento'),
            'unidade' => $request->input('unidade'),
            'gia' => $request->input('gia'),
            'numero' => $request->input('numero'),
            'cnpj' => $cnpj,
            'email_contato' => $request->input('email_contato'),
            'site' => $request->input('site'),
            'status' => '1',
        ]);

        app('flasher')->addSuccess('Instituição atualizada com sucesso!');
        return redirect('/gerenciar-instituicao');
    }
    public function destroy($id)
    {
        DB::table('instituicao')->where('id', $id)->delete();

        app('flasher')->addSuccess('Instituição excluída com sucesso!');
        return redirect()->back();
    }
    public function show($id)
    {
        $instituicao = DB::table('instituicao')
            ->leftJoin('tp_uf', 'instituicao.uf', 'tp_uf.id')
            ->leftJoin('tp_cidade', 'tp_cidade.id_cidade', 'instituicao.cidade')
             ->select(
                'instituicao.id as id',
                'instituicao.nome_fantasia',
                'instituicao.razao_social',
                'instituicao.inscricao_estadual',
                'instituicao.nome_contato',
                'instituicao.ibge',
                'instituicao.cep',
                'instituicao.logradouro',
                'instituicao.bairro',
                'tp_uf.id as uf',
                'tp_uf.sigla as sigla',
                'instituicao.cidade as cidade_id',
                'tp_cidade.descricao as cidade',
                'instituicao.complemento',
                'instituicao.unidade',
                'instituicao.gia',
                'instituicao.numero',
                'instituicao.cnpj',
                'instituicao.email_contato',
                'instituicao.site'
            )
            ->where('instituicao.id', $id)
            ->first();

        if (!$instituicao) {
            app('flasher')->addError('Instituição não encontrada!');
            return redirect('/gerenciar-instituicao');
        }

        return view('/instituicao/visualizar-instituicao', compact('instituicao'));
    }
    public function retornaCidadeDadosResidenciais($id)
    {
        $cidadeDadosResidenciais = DB::table('tp_cidade')
            ->where('id_uf', $id)
            ->get();

        return response()->json($cidadeDadosResidenciais);
    }
}
