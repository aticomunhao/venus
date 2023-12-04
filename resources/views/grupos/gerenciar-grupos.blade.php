@extends('layouts.app')

@section('content')
<div class="container">
    <br>
    <div class="row" style="text-align:center;">
        <h4 class="card-title col-12" style="font-size:20px; text-align: left; color: gray; font-family:calibri; margin:0px">
            GERENCIAR GRUPOS
        </h4>
        <div class="col">
            <form action="{{ route('nomes') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <label for="nome_pesquisa"></label>
                        <input class="form-control form-control-lg" type="text" id="nome_pesquisa" name="nome_pesquisa"  placeholder="Pesquisar nome" value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group" style="margin-top: 4%">
                            <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:0px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-grupos"><input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                        </form>

                             <a href="/criar-grupos" class="btn btn-success btn-sm ms-2" id="name" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;"  type="button">Novo grupo</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table">
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">NOME</th>
                            <th class="col-1"> HORA INÍCIO</th>
                              <th class="col-1"> HORA FIM</th>
                              <th class="col"> MÁXIMO ATENDIMENTO</th>
                              <th class="col">TIPO GRUPO</th>
                             <th class="col">STATUS GRUPO</th>
                             <th class="col">TIPO TRATAMENTO</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grupo as $grupos)
                        <tr>
                          <td> {{$grupos->nome}} </td>
                           <td> {{$grupos->h_inicio}} </td>
                          <td> {{$grupos->h_fim}} </td>
                          <td> {{$grupos->max_atend}} </td>
                          <td> {{$grupos->nm_tipo_grupo}} </td>
                          <td> {{$grupos->status_grupo}} </td>
                         <td> {{$grupos->descricao}} </td>
                          <th>


                                <a href="/editar-grupos/{{$grupos->id}}" type="button" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                <a href="/visualizar-grupos/{{$grupos->id}}" type="button" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-search" style="font-size: 1rem;color:#000;" data-bs-toggle="modal" data-bs-target="#pessoa"></i>
                                </a>
                                <a href="/deletar-grupos/{{$grupos->id}}" type="button" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
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







