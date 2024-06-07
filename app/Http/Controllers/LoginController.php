<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ModelUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Session;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login/login');
    }

    public function valida(Request $request)
    {
        $cpf = $request->input('cpf');
        $senha = $request->input('senha');

        $result = DB::select("
                        select
                        u.id id_usuario,
                        p.id id_pessoa,
                        a.id id_associado,
                        p.cpf,
                        p.sexo,
                        p.nome_completo,
                        u.hash_senha,
                        string_agg(distinct u_p.id_perfil::text, ',') perfis,
                        string_agg(distinct u_d.id_deposito::text, ',') depositos,
                        string_agg(distinct u_s.id_setor::text, ',') setor
                        from usuario u
                        left join pessoas p on u.id_pessoa = p.id
                        left join associado a on a.id_pessoa = p.id
                        left join usuario_perfil u_p on u.id = u_p.id_usuario
                        left join usuario_deposito u_d on u.id = u_d.id_usuario
                        left join usuario_setor u_s on u.id = u_s.id_usuario
                        where u.ativo is true and p.cpf = '$cpf'
                        group by u.id, p.id, a.id
                        ");

        $perfis = explode(',', $result[0]->perfis);
        $setores = explode(',', $result[0]->setor);
        $array_setores = $setores;
        
        $perfis = DB::table('rotas_perfil')->whereIn('id_perfil', $perfis)->orderBy('id_rotas')->pluck('id_rotas');
        $setores = DB::table('rotas_setor')->whereIn('id_setor', $setores)->orderBy('id_rotas')->pluck('id_rotas');

        $perfis = json_decode(json_encode($perfis), true);
        $setores = json_decode(json_encode($setores), true);

        $rotasAutorizadas = array_intersect($perfis, $setores);

        if (count($result) > 0) {
            $hash_senha = $result[0]->hash_senha;

            if (Hash::check($senha, $hash_senha)) {
                session()->put('usuario', [
                    'id_usuario' => $result[0]->id_usuario,
                    'id_pessoa' => $result[0]->id_pessoa,
                    'id_associado' => $result[0]->id_associado,
                    'nome' => $result[0]->nome_completo,
                    'cpf' => $result[0]->cpf,
                    'sexo' => $result[0]->sexo,
                    'setor' => $array_setores,
                    'acesso' => $rotasAutorizadas,
                ]);

                app('flasher')->addSuccess('Acesso autorizado');
                return view('login/home');
            }
        }
        app('flasher')->addError('Credenciais inválidas');
        return view('login/login');
    }

    public function validaUserLogado()
    {
        $cpf = session()->get('usuario.cpf');

        $result = DB::select("
        select
        u.id id_usuario,
        p.id id_pessoa,
        p.cpf,
        p.sexo,
        p.nome_completo,
        u.hash_senha,
        string_agg(distinct u_p.id_perfil::text, ',') perfis,
        string_agg(distinct u_d.id_deposito::text, ',') depositos,
        string_agg(distinct u_s.id_setor::text, ',') setor
        from usuario u
        left join pessoas p on u.id_pessoa = p.id
        left join usuario_perfil u_p on u.id = u_p.id_usuario
        left join usuario_deposito u_d on u.id = u_d.id_usuario
        left join usuario_setor u_s on u.id = u_s.id_usuario
        where u.ativo is true and p.cpf = '$cpf'
        group by u.id, p.id
        ");

        $perfis = explode(',', $result[0]->perfis);
        $setores = explode(',', $result[0]->setor);
        $array_setores = $setores;

        $perfis = DB::table('rotas_perfil')->whereIn('id_perfil', $perfis)->orderBy('id_rotas')->pluck('id_rotas');
        $setores = DB::table('rotas_setor')->whereIn('id_setor', $setores)->orderBy('id_rotas')->pluck('id_rotas');

        $perfis = json_decode(json_encode($perfis), true);
        $setores = json_decode(json_encode($setores), true);

        $rotasAutorizadas = array_intersect($perfis, $setores);

        if ($cpf = session()->get('usuario.cpf')) {
            session()->put('usuario', [
                'id_usuario' => $result[0]->id_usuario,
                'id_pessoa' => $result[0]->id_pessoa,
                'nome' => $result[0]->nome_completo,
                'cpf' => $result[0]->cpf,
                'sexo' => $result[0]->sexo,
                'setor' => $array_setores,
                'acesso' => $rotasAutorizadas,
            ]);
            return view('/login/home');
        } else {
            return view('login/login')->with('Error', 'O Sr(a) deve informar as credenciais para acessar o sistema');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
    //
}
