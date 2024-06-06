<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RegrasRotasMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $rota): Response
    {
        $perfis = session()->get('usuario.perfis');
        $setores = session()->get('usuario.setor');
        $perfis = explode(',', $perfis);
        $setores = explode(',', $setores);

        $perfis = DB::table('rotas_perfil')->whereIn('id_perfil', $perfis)->pluck('id_rotas');
        $setores = DB::table('rotas_setor')->whereIn('id_setor', $setores)->pluck('id_rotas');

        $perfis = json_decode(json_encode($perfis), true);
        $setores = json_decode(json_encode($setores), true);

        $rotasAutorizadas = array_intersect($perfis, $setores);


        if(in_array($rota, $rotasAutorizadas)){
            return $next($request);
        }
        else{
        app('flasher')->addError('Você não tem autorização para acessar esta funcionalidade!');
          return redirect('/login/valida');
        }



    }
}
