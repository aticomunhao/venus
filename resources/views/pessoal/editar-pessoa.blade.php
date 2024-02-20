@extends('layouts.app')

@section('title') Editar Pessoa @endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<div class="container">
    <div class="justify-content-center">
        <div class="col-12">
            <br>
            <fieldset class="border rounded border-primary ">
            <div class="card">
                <div class="card-header">
                    <div class="ROW">
                        <div class="col-12">
                            <span  style="color: rgb(16, 19, 241); font-size:15px;">Editar pessoa</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <form class="form-horizontal mt-4" method="post" action="/executa-edicao/{{$lista[0]->idp}}">
                            @csrf
                                <div class="col-5">Nome completo
                                    <input class="form-control"  type="text" maxlength="40" id="" name="nome" value="{{$lista[0]->nome_completo}}" required="required">
                                </div><br>
                                <div class="col-2">CPF
                                    <input class="form-control" type="numeric" maxlength="11"  value="{{$lista[0]->cpf}}" id="" name="cpf" required="required" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                </div><br>
                                <div class="col-2">Data nascimento
                                    <input class="form-control" type="date" value="{{$lista[0]->dt_nascimento}}" id="" name="dt_nasc" required="required">
                                </div><br>
                                <div class="col-2" style="text-align:left;">Sexo
                                    <select class="form-select" id="" name="sex" required="required">
                                        <option value="{{$lista[0]->sexo}}">{{$lista[0]->tipo}}</option>
                                        
                                        @foreach($sexo as $sexos)
                                        <option @if (old ('sex') == $sexos->id) {{'selected="selected"'}} @endif value="{{ $sexos->id }}">{{$sexos->tipo}}</option>
                                        @endforeach
                                    </select>
                                </div><br>
                            <div class="row">
                                <div class="col-2" style="text-align:left;">Status
                                    <select class="form-select" id="status_pessoa" name="status" required="required">
                                        <option value="{{$status_p[0]->id}}">{{$status_p[0]->tipo}}</option>
                                        <option value="1" {{ $status_p[0]->id == 1 ? 'selected' : '' }}>Ativo</option>
                                        <option value="0" {{ $status_p[0]->id == 0 ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                </div>
                            </div><br>
                            <div class="row">
                            <div class="col-2" style="text-align:left;">Motivo
                                    <select class="form-select" id="tp_motivo" name="motivo" required="required">
                                        <option value=""></option>
                                        <option value="{{$motivo[0]->id}}">{{$motivo[0]->motivo}}</option>
                                        <@foreach($motivo as $motivos)
                                        <option @if(old ('motivo') == $motivos->id) {{'selected="selected"'}} @endif value="{{ $motivos->id }}">{{$motivos->motivo}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="d-grid gap-1 col-2 mx-auto">
                            <a class="btn btn-danger btn-sm" href="/gerenciar-pessoas" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-3 col-2 mx-auto">
                            <button type="submit" class="btn btn-primary btn-sm" >Confirmar</button>
                        </div>
                    </div>
                    <br>
                    </form>
                </div>
            </div>
            </fieldset>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Função para verificar e habilitar/desabilitar o campo "Motivo"
        function verificarStatusMotivo() {
            var statusPessoa = document.getElementById('status_pessoa');
            var motivo = document.getElementById('tp_motivo');

            // Se o status for "Inativo", habilitar o campo "Motivo", caso contrário, desabilitar
            motivo.disabled = statusPessoa.value !== '0';
        }

        // Adicionar um ouvinte de eventos ao campo "Status" para verificar mudanças
        var statusPessoa = document.getElementById('status_pessoa');
        statusPessoa.addEventListener('change', verificarStatusMotivo);

        // Chamar a função inicialmente para configurar o estado inicial
        verificarStatusMotivo();
    });
</script>


@endsection
