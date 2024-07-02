@extends('layouts.app')

@section('head')

<title>Cadastrar Atendimento</title>

@endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
            <div class="card-header">
                    <div class="row justify-content-between">
                        <div class="col">
                            INCLUIR ATENDIMENTO
                        </div>
                        <div class="col-2">
                            <a href="/gerenciar-pessoas" class="btn btn-warning btn-sm w-100"
                            style="box-shadow: 1px 2px 5px #000000; margin:5px;">Nova Pessoa</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal mt-4" method="post" action="/novo-atendimento">
                        @csrf


                    <div class="input-group row">
                        <div class="col-3">Tipo Prioridade
                            <select class="form-select" id="" name="priori" required="required">
                                <option value=""></option>
                                @foreach($priori as $prioris)
                                <option value="{{$prioris->prid}}">{{$prioris->prdesc}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">Atendido

                            <select class="form-select lista" id="" name="assist" required="required">
                                <option value=""></option>
                                @foreach($lista as $listas)
                                <option value="{{$listas->pid}}">{{$listas->nome_completo}}</option>
                                @endforeach
                            </select>

                        </div>


                        <div class="col-1">Menor de 18 anos
                            <label for="menor" class="form-check-label"></label>
                            <input id="menor" type="checkbox" name="menor" data-size="small" data-toggle="toggle"  data-onstyle="success"  data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        </div>

                    </div>
                    <br>
                    <div class="form-group row">
                    <div class="col">Representante/Responsável
                            <select class="form-select lista" id="" name="repres" >
                                <option value=""></option>
                                @foreach($lista as $listas)
                                <option value="{{$listas->pid}}">{{$listas->nome_completo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">Parentesco
                            <select class="form-select" id="" name="parent" >
                                <option value=""></option>
                                @foreach($parentes as $parentess)
                                <option value="{{$parentess->id}}">{{$parentess->nome}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">Tipo AFI
                            <select class="form-select" id="tipo_afi" name="tipo_afi" >
                                <option value=""></option>
                                @foreach($sexo as $sexos)
                                <option value="{{$sexos->id}}">{{$sexos->tipo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-1">Ped especial
                            <label for="especial" class="form-check-label"></label>
                            <input id="especial" type="checkbox" data-size="small" data-toggle="toggle"  data-onstyle="success"  data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" >

                        </div>
                        <div class="col" id="hiddenField" style="display: none;">AFI preferido
                            <select class="form-select" id="afi_p" name="afi_p" >
                                <option value=""></option>
                                @foreach($afi as $afis)
                                <option value="{{$afis->ida}}">{{$afis->nm_1}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                </div>
                <center>
                <div class="row col-10">
                            <div class="d-grid gap-1 col mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-atendimentos" role="button">Cancelar</a>
                            </div>

                            <div class="d-grid gap-2 col mx-auto" >
                                <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                            </div>
                            </form>

                        </div>
                    </center>
                    <br>
            </div>
        </div>
    </div>
</div>

<script>
       jQuery(document).ready(function () {
            jQuery('#especial').change(function () {
                if ($(this).prop('checked')) {
                    $('#hiddenField').show();
                } else {
                    $('#hiddenField').hide();
                }
            });
        });
</script>

<script>
    jQuery(document).ready(function() {
        jQuery('.lista').select2({

            theme: 'bootstrap-5'
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>

@endsection

@section('footerScript')


@endsection
