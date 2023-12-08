@extends('layouts.app')

@section('content')


<div class="container-fluid">
     <div class="row" style="text-align:center;">
         <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR SALAS</h4>
        </h4>
        <div class="col" style="text-align: right">
            <br>
            <a href="/criar-salas" class="btn btn-success btn-sm" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">Nova Sala</a>
        </div>
        <br>
        <br><br>

        <div class="table">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">NOME</th>
                        <th class="col">FINALIDADE SALA</th>
                          <th class="col">NÚMERO</th>
                          <th class="col">LOCALIZAÇÃO</th>
                          <th class="col">M² DA SALA</th>
                         <th class="col">NÚMERO DE LUGARES</th>
                         <th class="col">STATUS</th>
                        <th class="col">AÇÕES</th>

                     </tr>
                </thead>
                <tbody>
                  @foreach ($sala as $salas)
                  <tr>
                    <td> {{$salas->nome1}} </td>
                     <td> {{$salas->descricao}} </td>
                    <td> {{$salas->numero}} </td>
                    <td> {{$salas->nome2}} </td>
                    <td> {{$salas->tamanho_sala}} </td>
                    <td> {{$salas->nr_lugares}} </td>




                      <td  class="text-center">{{$salas->status_sala ? 'Ativo' : 'Inativo' }}</td>


                      <td><a href="/editar-salas/{{$salas->ids}}" type="button"
                        class="btn btn-outline-warning btn-sm"><i class="bi bi-pen"
                            style="font-size: 1rem; color:#000;"></i>
                        </a>
                            <a href="/visualizar-salas/{{$salas->ids}} "type="button"
                                class="btn btn-outline-primary btn-sm"><i class="bi bi-search"
                                style="font-size: 1rem;color:#000;" data-bs-toggle="modal" data-bs-target="#pessoa"></i>
                </a>
                <a href="/deletar-salas/{{$salas->ids}}" type="button"
                        class="btn btn-outline-danger btn-sm"><i
                            class="bi bi-x-circle"
                            style="font-size: 1rem; color:#000;"></i></td>
                </a>
            </td>
                    @endforeach

                  </tr>
                </tbody>

        </table>
      </div>
</div>
@endsection
