@extends('layouts.app')

@section('content')
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">               
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            INCLUIR PESSOA
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal mt-4" method="POST" action="/criar-pessoa" >
                    @csrf
                    <div class="form-group row">
                        <div class="col-4" style="text-align:left;">
                            <label for="validationCustom01" class="form-label">Nome</label>
                            <input class="form-control" type="text" maxlength="45" oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="" name="nome" value="{{old('nome')}}" required="required">                       
                        </div>
                        <div class="col-1">
                        </div>      
                        <div class="col-2" style="text-align:left;">
                        <label for="validationCustom02" class="form-label">CPF</label>
                            <input class="form-control" type="numeric" maxlength="11" placeholder="888.888.888-88"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" value="{{old('cpf')}}" id="" name="cpf" required="required" >
                        </div>
                        <div class="col-2">
                        </div>
                        <div class="col-2" style="text-align:left;">
                        <label for="validationCustom03" class="form-label">Data nascimento</label>
                            <input class="form-control" type="date" id="" name="dt_na" value="{{old('dt_na')}}" required="required" >
                        </div>
                        <div class="col-2">
                        </div>
                    </div><br>
                    <div class="form-group row">
                        <div class="col-2" style="text-align:left;">
                        <label for="validationCustom04" class="form-label">Sexo</label>
                            <select class="form-select" id="" name="sex" required="required">
                                <option value=""></option>
                                <@foreach($sexo as $sexos)
                                <option @if (old ('sex') == $sexos->id) {{'selected="selected"'}} @endif value="{{ $sexos->id }}">{{$sexos->tipo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                        </div>
                        <div class="col-1" style="text-align:left;">
                        <label for="validationCustom05" class="form-label">DDD</label>
                        <select class="form-select" id="" name="ddd" required="required">
                                        <option value=""></option>
                                        <@foreach($ddd as $ddds)
                                        <option @if(old ('ddd') == $ddds->id) {{'selected="selected"'}} @endif value="{{ $ddds->id }}">{{$ddds->descricao}}</option>
                                        @endforeach
                                    </select>
                        </div>
                        <div class="col-2" style="text-align:left;">
                        <label for="validationCustom06" class="form-label">Nr Celular</label>
                            <input class="form-control" maxlength="9" type="numeric" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="Ex.: 99999-9999" value="{{old('celular')}}" id="" name="celular" required="required" >
                        </div>
                        <div class="col-2">
                        </div>
                    </div>
                    <br>
                    <hr>                                  
                    <div class="form-group row">
                        <div class="col" style="text-align: right;">
                            <a class="btn btn-danger" href="/gerenciar-pessoas" role="button">Cancelar</a>
                        </div>
                        <div class="col" style="text-align: left;">                        
                            <button type="submit" class="btn btn-primary" style="background-color:#007bff; color:#fff;">Confirmar</button>
                        </div>
                    </form>
                    </div>
                </div>                    
            </div>
        </div>
    </div>
</div>

@endsection