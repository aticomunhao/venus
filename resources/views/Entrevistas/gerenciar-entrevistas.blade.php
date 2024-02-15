@extends('layouts.app')

@section('title') Gerenciar Entrevista @endsection

@section('content')

<div class="container">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ENTREVISTA</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('gerenciamento')}}" class="form-horizontal mt-4" method="GET">
                    <div class="row">
                        <div class="col">Nome
                            <select class="form-select" id="" name="nome" type="name">

                            </select>
                        </div>

                        <div class="col">Status
                            <select class="form-select" id="4" name="status" type="number">

                            </select>
                        </div>
                        <div class="col"><br/>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-tratamentos"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                        </div>
                    </div>
                </form>
                <br/>
            </div>
            <hr/>
            <div class="table">Total assistidos:
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col">NOME</th>
                            <th class="col">DATA </th>
                            <th class="col">HORA </th>
                            <th class="col">ENCAMINHAMENTO</th>
                            <th class="col">ENTREVISTADOR</th>
                            <th class="col">REPRESENTANTE</th>
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        @foreach($informacoes as $informacao)
                        <tr>
                            <td>{{$informacao->id}}</td>
                            <td>{{$informacao->nome_pessoa}}</td>
                            <td>{{$informacao->data}}</td>
                            <td>{{$informacao->hora}}</td>
                            <td>{{$informacao->descricao}}</td>
                            <td>{{$informacao->id_entrevistador}}</td>
                            <td>{{$informacao->nome_representante}}</td>
                            <td>{{$informacao->status}}</td>
                            <td>
                                <a href="{{ route('criar-entrevista', ['id' => $informacao->id]) }}" type="button"
                                    class="btn btn-outline-success btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Agendar">
                                    <i class="bi bi-clipboard-check"style="font-size: 1rem; color:#000;"></i>
                                 </a>
                                <a href="/visualizar-entrevista/{{ $informacao->id }}" type="button"
                                    class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Histórico">
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footerScript')


@endsection
