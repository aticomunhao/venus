@extends('layouts.app')

@section('content')


<div class="container">
  <br>
     <div class="row" style="text-align:center;">
        <h4 class="card-title col-10 " class="card-title"
          style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR MÉDIUNS
        </h4>
        <div class="col" style="text-align: right">

          <a href="/criar-mediuns" class="btn btn-success"id="name=" type="button">NOVO+</a>
        </div>
        <br>
        <br><br>

        <div class="table">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">NOME</th>
                        <th class="col">TIPO MEDIUNIDADE</th>


                        <th class="col">AÇÕES</th>

                     </tr>
                </thead>
                <tbody>
                  @foreach ($medium as $mediuns)
                  <tr>
                    <td> {{$mediuns->nome_completo}} </td>
                     <td> {{$mediuns->tipo}} </td>


                     {{-- <td  class="text-center">{{$medium->status ? 'Ativo' : 'Inativo' }}</td> --}}


                    <td> <a href="/editar-mediuns/{{$mediuns->id}}" type="button"
                      class="btn btn-outline-warning btn-sm"><i class="bi bi-pen"
                          style="font-size: 1rem; color:#000;"></i>
                          </a>

                          <a href="/visualizar-mediuns/{{$mediuns->id}} "type="button"
                            class="btn btn-outline-primary btn-sm"><i class="bi bi-search"
                            style="font-size: 1rem;color:#000;" data-bs-toggle="modal" data-bs-target="#pessoa"></i>


              </a>
              <a href="/deletar-mediuns/{{$mediuns->id}}" type="button"
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
