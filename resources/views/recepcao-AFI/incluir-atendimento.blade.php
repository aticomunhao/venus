@extends('layouts.app')

@section('head')

<title>Cadastrar Atendimento</title>


@endsection

@section('content')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" />  
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
            <div class="card-header">
                    <div class="row">
                        <div class="col">
                            INCLUIR ATENDIMENTO
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal mt-4" method="post" action="/novo-atendimento">
                        @csrf
                    <legend style="color:#525252; font-size:12px; font-family:sans-serif">Dados do Atendido</legend>
                    <fieldset class="border rounded border-primary p-2">
                    <div class="form-group row">
                        <div class="col-3">Tipo Prioridade
                            <select class="form-select" id="" name="priori" required="required">
                                <option value=""></option>
                                @foreach($priori as $prioris)
                                <option value="{{$prioris->prid}}">{{$prioris->prdesc}}</option>
                                @endforeach
                            </select>
                        </div>         
                        <div class="col">Nome do Atendido
                            <select class="form-select lista" id="" name="assist" required="required">
                                <option value=""></option>
                                @foreach($lista as $listas)
                                <option value="{{$listas->pid}}">{{$listas->nome_completo}}</option>
                                @endforeach
                            </select>
                        </div>                       
                    </div>
                    <br>
                    <div class="form-group row">
                    <div class="col">Nome do representante
                            <select class="form-select lista" id="" name="repres" >
                                <option value=""></option>
                                @foreach($lista as $listas)
                                <option value="{{$listas->pid}}">{{$listas->nome_completo}}</option>
                                @endforeach
                            </select>
                        </div>     
                        <div class="col-2">Parentesco
                            <select class="form-select" id="" name="parent" >
                                <option value=""></option>
                                @foreach($parentes as $parentess)
                                <option value="{{$parentess->id}}">{{$parentess->nome}}</option>
                                @endforeach
                            </select>
                        </div>         
                        <div class="col">AFI preferido
                            <select class="form-select" id="afi_p" name="afi_p" >
                                <option value=""></option>
                                @foreach($afi as $afis)
                                <option value="{{$afis->idatt}}">{{$afis->nm_1}}</option>
                                @endforeach
                            </select>
                        </div>                        
                        <div class="col-3">Tipo AFI
                            <select class="form-select" id="tipo_afi" name="tipo_afi" >
                                <option value=""></option>
                                @foreach($sexo as $sexos)
                                <option value="{{$sexos->id}}">{{$sexos->tipo}}</option>
                                @endforeach
                            </select>                                                 
                        </div>                        
                    </div>                   
                    <br>
                </div>
                <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-atendimentos" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto" >
                            <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                        </div>
                        </form>
                        
                    </div>
                    <br>
            </div>
        </div>
    </div>
</div>



<script>
    jQuery(document).ready(function() {
        jQuery('.lista').select2({
    
            height: '150%',
            width: "100%",           
        });
    });
</script>


<script>
    const campo1 = document.getElementById('afi_p');
    const campo2 = document.getElementById('tipo_afi');

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


@endsection
