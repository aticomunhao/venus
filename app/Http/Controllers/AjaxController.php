<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Support\Facades\Http;

class AjaxController extends Controller
{
    public function retornaCidades($id)
    {
        $cidadeDadosResidenciais = DB::table('tp_cidade')
            ->where('id_uf', $id)
            ->get();

        return response()->json($cidadeDadosResidenciais);
    }

    public function getAddressByCep($cep)
    {
        $url = "https://viacep.com.br/ws/$cep/json/";

        /**
         * Exemplo dos tipos de informação que se pode conseguir
         *
         * cep    "70200-640"
         *logradouro:    "SGAS 604"
         *complemento:    ""
         *bairro:    "Asa Sul"
         *localidade:    "Brasília"
         *uf:    "DF"
         *ibge:    "5300108"
         *gia:""
         *ddd:    "61"
         *siafi:    "9701"
         */
        $response = Http::get($url);
        dd($response);
        // Verifica se a requisição foi bem-sucedida
        if ($response->successful()) {
            $data = $response->json();
            return response()->json($data);
        } else {
            // Tratar erro de requisição
            return response()->json(['error' => 'Unable to fetch address'], 400);
        }
    }
   

}
