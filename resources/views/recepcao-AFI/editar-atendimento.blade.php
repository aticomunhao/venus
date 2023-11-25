@extends('layouts.app')

@section('head')

<title>Editar Atendimento</title>


@endsection

@section('content')

<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            EDITAR ATENDIMENTO
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal mt-4" method="post" action="/grava-atualizacao/{{$result[0]->ida}}">
                        @csrf
                    <legend style="color:red; font-size:15px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Dados do assistido</legend>
                    <fieldset class="border rounded border-primary p-2">
                    <div class="form-group row">
                    <div class="col-2">Tipo Prioridade
                        <select class="form-select" id="" name="priori" required="required">
                            <option value="{{$result[0]->prid}}">{{$result[0]->prdesc}}</option>
                            <option value=""></option>
                            @foreach($priori as $prioris)
                            <option value="{{$prioris->prid}}">{{$prioris->prdesc}}</option>
                            @endforeach
                        </select>
                        </div>       
                        <div class="col">Nome do assistido
                            <select class="form-select" id="" name="assist" required="required">
                                <option value="{{$result[0]->idas}}">{{$result[0]->nm_1}}</option>
                                <option value=""></option>
                                @foreach($lista as $listas)
                                <option value="{{$listas->id}}">{{$listas->nome_completo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">Nome do representante
                            <select class="form-select" id="" name="repres" >
                                <option value="{{$result[0]->idr}}">{{$result[0]->nm_2}}</option>
                                <option value=""></option>
                                @foreach($lista as $listas)
                                <option value="{{$listas->id}}">{{$listas->nome_completo}}</option>
                                @endforeach
                            </select>
                        </div>     
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-2">Parentesco
                            <select class="form-select" id="" name="parent" >
                                <option value="{{$result[0]->idp}}">{{$result[0]->nome}}</option>
                                <option value=""></option>
                                @foreach($pare as $pares)
                                <option value="{{$pares->idp}}">{{$pares->nome}}</option>
                                @endforeach
                            </select>
                        </div>         
                        <div class="col">AFI preferido
                            <select class="form-select" id="afi_p" name="afi_p" >
                                <option value="{{$result[0]->iap}}">{{$result[0]->nm_3}}</option>
                                <option value=""></option>
                                @foreach($afi as $afis)
                                <option value="{{$afis->iaf}}">{{$afis->nm_afi}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">Tipo AFI
                            <select class="form-select" id="tipo_afi" name="tipo_afi" >
                                <option value="{{$result[0]->pta}}">{{$result[0]->tipo}}</option>
                                <option value=""></option>
                                @foreach($sexo as $sexos)
                                <option value="{{$sexos->idsx}}">{{$sexos->tipo}}</option>
                                @endforeach
                            </select>
                        </div>                                      
                    </div>                   
                    <br>
                </div>
                <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-atendimentos"  role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto" >
                            <button type="submit" class="btn btn-primary" >Confirmar</button>
                        </div>
                        </form>
                        
                    </div>
                    <br>
            </div>
        </div>
    </div>
</div>
<script>
    const afi_p = document.getElementById('afi_p');
    const tipo_afi = document.getElementById('tipo_afi');

    // Adiciona um ouvinte de eventos para o campo1
    afi_p.addEventListener('input', function() {
      // Se campo1 estiver preenchido, desabilita o campo2
      if (afi_p.value.trim() !== '') {
        tipo_afi.disabled = true;
      } else {
        tipo_afi.disabled = false;
      }
    });

    // Adiciona um ouvinte de eventos para o campo2
    tipo_afi.addEventListener('input', function() {
      // Se campo2 estiver preenchido, desabilita o campo1
      if (tipo_afi.value.trim() !== '') {
        afi_p.disabled = true;
      } else {
        afi_p.disabled = false;
      }
    });
  </script>


@endsection

@section('footerScript')

<script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>

@endsection
