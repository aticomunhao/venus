@extends('layouts.app')

@section('head')

@section('title', 'Visualizar Atendentes')

@if (session('msg'))
   <p class="msg">{{ session('msg') }}</p>
@endif

@section('content')

<div class="container p-5 my-5 border ">
    {{-- Teste de Banco @dump($atendentes) --}}

        <div class="row">
            {{--Titulo Gerenciar Atendentes --}}
            <div class="col-8">
                <h1 class="display-4 p-2">{{$results->nome}}</h1>
            </div>

            <table class="table table-hover">
                {{-- <table class="table table-bordered"> Bordered --}}
                {{-- <table class="table table-striped"> Striped --}}
                {{-- <table class="table table-hover"> hover --}}
                {{-- <table class="table table-dark"> dark, for dark mode --}}

                {{-- Cabeçalio --}}
                <thead class="table-primary">
                    <tr>
                        <th>Nome</th>
                        <th>Grupo</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <legend style="color:red; font-size:14px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Histórico de atendimentos</legend>
                    <fieldset class="border rounded border-secondary p-4">
                    <div class="form-group row">
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Assistido:</label>
                            <input type="text" id="" value="{{$result[0]->nm_1}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-1">
                            <label for="disabledTextInput" class="form-label">DDD:</label>
                            <input type="text" id="" value="{{$result[0]->ddd}}" style="text-align:center;" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-3">
                            <label for="disabledTextInput" class="form-label">Celular:</label>
                            <input type="tel" id="phone"  name="phone"  value="{{number_format($result[0]->celular, 0, ',', '-')}}"   class="form-control" placeholder="Disabled input" disabled>
                        </div>
                    </div>
                    </fieldset>
                    <br>
                    <legend style="color:blue; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Lista de atendimentos</legend>
                    <?php $a=1; $b=1; $c=1; $d=1; $e=1; ?>
                    @foreach($result as $results)
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="{{$a++}}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{$b++}}" aria-expanded="false" aria-controls="flush-collapse{{$c++}}">
                            {{date('d-m-Y', strtotime($results->dh_chegada))}}
                            </button>
                            </h2>
                            <div id="flush-collapse{{$d++}}" class="accordion-collapse collapse" aria-labelledby="{{$e++}}" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                                <td class="col-3">REPRESENTANTE</td>
                                                <td class="col-1">PARENTESCO</td>
                                                <td class="col-3">ATENDENTE</td>
                                                <td class="col-1">DT/H INÍCIO</td>
                                                <td class="col-1">DT/H FIM</td>
                                                <td class="col-2">STATUS</td>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center;font-size:11px">
                                                <td>{{$results->nm_2}}</td>
                                                <td>{{$results->nome}}</td>
                                                <td>{{$results->nm_4}}</td>
                                                <td>{{$results->dh_inicio}}</td>
                                                <td>{{$results->dh_fim}}</td>
                                                <td>{{$results->descricao}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    @endforeach
                    <br>
                    <div class="row">
                        <div class="col">
                            <a class="btn btn-danger" href="/gerenciar-atendimentos" style="text-align:right;" role="button">Fechar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section('footerScript')

<script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>

@endsection
