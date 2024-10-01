@extends('layouts.app')
@section('title', 'Incluir Pessoa')
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
                        <div class="row">
                            <div class="col">
                                <div class="mb-4" style="text-align:left;">
                                    <label for="validationCustom01" class="form-label">Nome</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input class="form-control" type="text" maxlength="80" oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="" name="nome" value="{{old('nome')}}" required="required">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col">
                                <div class="mb-4" style="text-align:left;">
                                    <label for="validationCustom02" class="form-label">CPF</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input class="form-control" type="numeric" maxlength="11" placeholder="888.888.888-88"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" value="{{old('cpf')}}" id="" name="cpf" required="required" >
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-4" style="text-align:left;">
                                    <label for="validationCustom04" class="form-label">Sexo</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select" id="" name="sex" required="required">
                                        <option value=""></option>
                                        @foreach($sexo as $sexos)
                                            <option @if (old ('sex') == $sexos->id) {{'selected="selected"'}} @endif value="{{ $sexos->id }}">{{$sexos->tipo}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col">
                                <div class="mb-4" style="text-align:left;">
                                    <label for="validationCustom03" class="form-label">Data Nascimento</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input class="form-control" type="date" id="" name="dt_na" value="{{old('dt_na')}}" required="required" >
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="mb-4" style="text-align:left;">
                                    <label for="validationCustom05" class="form-label">DDD</label>
                                    <select class="form-select" id="" name="ddd">
                                        <option value=""></option>
                                        @foreach($ddd as $ddds)
                                            <option @if(old ('ddd') == $ddds->id) {{'selected="selected"'}} @endif value="{{ $ddds->id }}">{{$ddds->descricao}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-5" style="text-align:left;">
                                    <label for="validationCustom06" class="form-label">Nr Celular</label>
                                    <input class="form-control" maxlength="9" type="numeric" name="celular" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" >
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="row mt-1 justify-content-center">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-pessoas" role="button">Cancelar</a>
                            </div>
                            <div class="d-grid gap-2 col-4 mx-auto">
                                <button type="submit" class="btn btn-primary">Confirmar</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
