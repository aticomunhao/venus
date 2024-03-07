@extends('layouts.app')

@section('head')

<title>Cadastrar Reunião Mediúnica</title>

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
                            CADASTRAR REUNIÃO MEDIÚNICA
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal mt-4" method="post" action="/nova-reuniao">
                        @csrf


                        <div class="row">
                            <div class="col-xl-4 col-md-6">Grupo
                                <select class="form-select slct" id="" type="number" name="grupo" required="required">
                                    

                                    @foreach($grupo as $grupos)
                                    <option value="{{$grupos->idg}}"  >{{$grupos->nome}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-6 mt-md-0 mt-4">Tipo de Tratamento
                                <select class="form-select slct" id="" type="number" name="tratamento" required="required">
                                    

                                    @foreach($tratamento as $tratamentos)
                                    <option value="{{$tratamentos->idt}}" >{{$tratamentos->descricao}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-6 mt-xl-0 mt-4">Dia da semana
                                <select class="form-select slct" id="" type="number" name="dia" required="required">
                                    

                                    @foreach($dia as $dias)
                                    <option value="{{$dias->idd}}" >{{$dias->nome}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-6 mt-xl-0 mt-4">Max atendimentos
                                <input type="number" class="form-control" id="" min="1" max="800" name="max_atend" required="required" value="">
                                
                            </div>
                            <br>


                            <div class="col-lg-4 col-md-6 mt-4 mt-4">Nome Sala
                                <select class="form-select slct" id="" type="number" name="nome" required="required">
                                    

                                    @foreach($sala as $salas)
                                    <option value="{{$salas->ids}}" >{{$salas->nome}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-2 col-md-6 mt-4">Número Sala
                                <select class="form-select slct" id="" type="number" name="numero" required="required">
                                    

                                    @foreach($sala as $salas)
                                    <option value="{{$salas->ids}}" >{{$salas->numero}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md col-12 mt-4">Hora de início
                                <input class="form-control" type="time"   name="h_inicio" required="required" value="">
                                
                            </div>
                            <div class="col-xl  col mt-4">Hora de fim
                                <input class="form-control" type="time"   name="h_fim" required="required" value="">
                                
                            </div>

                        </div>
                    </div>
                <div class="row">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-reunioes" role="button">Cancelar</a>
                    </div>
                    <div class="d-grid gap-2 col-4 mx-auto" >
                        <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                    </div>
                    </form>
                </div>
                <br/>
            </div>
        </div>
    </div>
</div>

<script>
    //Deixa o select status como padrao vazio
        $(".slct").prop("selectedIndex", -1);

</script>
@endsection

@section('footerScript')

<script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>

@endsection
