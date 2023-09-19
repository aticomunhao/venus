@extends('layouts.app')

@section('head')

<title>Cadastrar Atendimento</title>


@endsection

@section('content')

<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal mt-4" method="post" action="/novo-atendimento">
                        @csrf
                    <legend style="color:red; font-size:15px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Dados do assistido</legend>
                    <fieldset class="border rounded border-primary p-2">
                    <div class="form-group row">
                        <div class="col">Nome do assistido
                            <select class="form-select" id="1" name="assist" required="required">
                                <option value=""></option>
                                @foreach($lista as $listas)
                                <option value="{{$listas->id}}">{{$listas->nome_completo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">Nome do representante
                            <select class="form-select" id="2" name="repres" >
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
                            <select class="form-select" id="3" name="parent" >
                                <option value=""></option>
                                @foreach($parentes as $parentess)
                                <option value="{{$parentess->id}}">{{$parentess->nome}}</option>
                                @endforeach
                            </select>
                        </div>         
                        <div class="col">AFI preferido
                            <select class="form-select" id="5" name="afi_p" >
                                <option value=""></option>
                                @foreach($lista as $listas)
                                <option value="{{$listas->id_pessoa}}">{{$listas->nome_completo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">Tipo AFI
                            <select class="form-select" id="6" name="tipo_afi" >
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
                            <a class="btn btn-danger" href="/gerenciar-atendimentos" style="font-weight:bold;" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto" >
                            <button type="submit" class="btn btn-info" style="color:#fff; font-weight:bold;">Confirmar</button>
                        </div>
                        </form>
                        
                    </div>
                    <br>
            </div>
        </div>
    </div>
</div>



@endsection

@section('footerScript')

<script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>

@endsection
