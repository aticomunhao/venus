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

    public function checaSession()
    {

        $check = session()->get('usuario') == null ? 0 : 1;
        return $check;
    }


    public function index()
    {
        try {
            return view('login/login');
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('login/login erro.erro-inesperado', compact('code'));
        }
    }

    public function valida(Request $request)
    {

        $cpf = $request->input('cpf');
        $senha = $request->input('senha');

        // Busca se o usuário está na base de dados e traz as informações básicas
        $result = DB::table('usuario as u')
            ->select(
                'u.id as id_usuario',
                'u.id_pessoa',
                'a.id as id_associado',
                'p.cpf',
                'p.sexo',
                'p.nome_completo',
                'u.hash_senha'
            )
            ->leftJoin('associado as a', 'u.id_pessoa', 'a.id_pessoa')
            ->leftJoin('pessoas as p', 'u.id_pessoa', 'p.id')
            ->where('u.ativo', true)
            ->where('u.bloqueado', false)
            ->where('p.cpf', $cpf)
            ->first();

        //dd($result);

        // Garante que um usuário foi encontrado ativado e não bloqueado
        if ($result) {

            $hash_senha = $result->hash_senha;
            if (Hash::check($senha, $hash_senha)) {

                // Busca todos os acessos do Usuário
                $acessoTotal = DB::table('usuario_acesso')
                    ->where('id_usuario', $result->id_usuario)
                    ->get()
                    ->toArray();

                // Traz todos os id_acesso que esse usuário tem
                $acessos = array_unique(array_column($acessoTotal, 'id_acesso'));
                
                // Organiza os setores e perfis conforme as rotas
                $arraySetoresPerfis = array();
                foreach ($acessoTotal as $element) {
                    $arraySetoresPerfis[$element->id_acesso][] = ['id_perfil' => $element->id_perfil, 'id_setor' => $element->id_setor];
                }

                //Insere na sessão os dados basicos de usuario e acesso
                session()->put('usuario', [
                    'id_usuario' => $result->id_usuario,
                    'id_pessoa' => $result->id_pessoa,
                    'id_associado' => $result->id_associado,
                    'acesso' => $acessos
                ]);

                // Insere na sessão os dados de setor para recuperação
                session()->put('acessoInterno', $arraySetoresPerfis);
                
                app('flasher')->addSuccess('Acesso autorizado!');

                if ($cpf == $senha) {
                    return view('/usuario/alterar-senha');
                }
                return view('login/home');
            }

            app('flasher')->addError('Credenciais inválidas');
            return view('login/login');
        }
        app('flasher')->addError('Usuário Não Encontrado!');
        return view('login/login');
    }
    public function validaUserLogado()
    {
        try {
            $cpf = session()->get('usuario.cpf');

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

            if ($cpf = session()->get('usuario.cpf')) {
                $perfis = explode(',', $result[0]->perfis);
                $setores = explode(',', $result[0]->setor);

                $setores = DB::table('setor as st')
                    ->leftJoin('setor as stf', 'st.id', 'stf.setor_pai')
                    ->leftJoin('setor as stn', 'stf.id', 'stn.setor_pai')
                    ->select('st.id as ids', 'stf.id as idf', 'stn.id as idn')
                    ->whereIn('st.id', $setores)
                    ->get();

                $setores = json_decode(json_encode($setores), true);
                $setores = (array_unique(array_merge(array_column($setores, 'ids'), array_column($setores, 'idf'), array_column($setores, 'idn'))));
                $array_setores = $setores;



                $perfis = DB::table('rotas_perfil')->whereIn('id_perfil', $perfis)->orderBy('id_rotas')->pluck('id_rotas');
                $setores = DB::table('rotas_setor')->whereIn('id_setor', $setores)->orderBy('id_rotas')->pluck('id_rotas');

                $perfis = json_decode(json_encode($perfis), true);
                $setores = json_decode(json_encode($setores), true);

                $rotasAutorizadas = array_intersect($perfis, $setores);

                session()->put('usuario', [
                    'id_usuario' => $result[0]->id_usuario,
                    'id_pessoa' => $result[0]->id_pessoa,
                    'id_associado' => $result[0]->id_associado,
                    'nome' => $result[0]->nome_completo,
                    'cpf' => $result[0]->cpf,
                    'sexo' => $result[0]->sexo,
                    'setor' => $array_setores,
                    'acesso' => $rotasAutorizadas,
                    'perfis' => $perfis,
                ]);
                return view('/login/home');
            } else {
                app('flasher')->addError('É necessário realizar o login para acessar!');
                return view('login/login');
            }
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
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
