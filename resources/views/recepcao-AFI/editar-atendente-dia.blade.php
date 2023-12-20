@extends('layouts.app')

@section('title') Editar Atendente Dia @endsection

@section('content')

<br/>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            EDITAR ATENDENTE DO DIA
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <legend style="color:#525252; font-size:12px; font-family:sans-serif">Dados do AFI</legend>
                    <fieldset class="border rounded border-secondary p-4">
                        <div class="form-group row">
                            <div class="col-2">
                                <label for="disabledTextInput" class="form-label">Número:</label>
                                <input type="number" id="" value="{{$atende[0]->idatd}}" class="form-control" placeholder="Disabled input" disabled>
                            </div>
                            <div class="col">
                                <label for="disabledTextInput" class="form-label">Grupo:</label>
                                <input type="text" id="" value="{{$atende[0]->nomeg}}" style="text-align:center;" class="form-control" placeholder="Disabled input" disabled>
                            </div>
                            <div class="col">
                                <label for="disabledTextInput" class="form-label">Nome AFI:</label>
                                <input type="text" id="" value="{{$atende[0]->nm_4}}"   class="form-control" placeholder="Disabled input" disabled>
                            </div>
                        </div>
                    </fieldset>
                <div>
                <form  class="form-horizontal mt-4" method="POST" action="/altera-atendente-dia/{{$atende[0]->idatd}}">
                    @csrf
                    <div class="row">
                        <div class="col"></div>
                        <div class="col">Número da Sala:
                            <select class="form-select text-center" id="" name="sala" type="number">
                                <option value="{{$atende[0]->id_sala}}">{{$atende[0]->nm_sala}}</option>
                                @foreach ($sala as $salas)
                                <option @if(old('sala')==$salas->id) {{'selected="selected"'}} @endif value="{{ $salas->id }}">{{$salas->numero}}</option>
                                @endforeach               
                            </select>
                        </div>                    
                        <div class="col"></div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-atendente-dia" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto" >
                            <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                            </form>
                        </div>
                    </div>                
            </div>
        </div>
    </div>
</div>                       


@endsection

@section('footerScript')  


@endsection
