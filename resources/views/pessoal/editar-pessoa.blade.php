@extends('layouts.app')

@section('title') Editar Pessoa @endsection

@section('content')

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
                                <div class="form-group row">
                                    <div class="col-1" style="text-align:left;">DDD
                                        <select class="form-select" id="" name="ddd" required="required">
                                            <option value="{{$lista[0]->did}}">{{$lista[0]->ddesc}}</option>
                                            <@foreach($ddd as $ddds)
                                            <option @if(old ('ddd') == $ddds->id) {{'selected="selected"'}} @endif value="{{ $ddds->id }}">{{$ddds->descricao}}</option>
                                            @endforeach
                                        </select>
                                    </div><br>
                                    <div class="col-2" style="text-align:left;">Nr Celular
                                        <input class="form-control" maxlength="9" type="numeric" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="Ex.: 99999-9999" value="{{$lista[0]->celular}}" id="" name="celular" required="required" >
                                    </div>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-5" style="text-align:left;">email
                                    <input class="form-control" type="email" maxlength="45" id="" name="email" value="{{$lista[0]->email}}" required="required" >
                                </div>                            
                                <div class="col-2" style="text-align:left;">Status
                                    <select class="form-select" id="" name="status" required="required">
                                        <option value="{{$status_p[0]->id}}">{{$status_p[0]->status}}</option>
                                        <@foreach($status_p as $statp)
                                        <option @if(old ('status_p') == $statp->id) {{'selected="selected"'}} @endif value="{{ $statp->id }}">{{$statp->status}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><br>
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


@endsection