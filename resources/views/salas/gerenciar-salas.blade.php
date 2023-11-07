@extends('layouts.app')

@section('content')
<div class="container">
  <br> 
     <div class="row" style="text-align:center;">
        <h4 class="card-title col-10 " class="card-title"
          style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR SALAS
        </h4>
        <div class="col" style="text-align: right">
          <a href="criar-salas" class="btn btn-success"id="name=" type="button">NOVO+</a>
        </div>
        <br>   
        <br><br>
        <div class="table">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">NOME</th>
                          <th class="col">NÚMERO</th>
                          <th class="col">LOCALIZAÇÃO</th>
                          <th class="col">M² SALA</th>
                         <th class="col">NÚMERO DE LUGARES</th>
                         <th class="col">STATUS</th>
                        <th class="col">AÇÕES</th>
                       
                     </tr>
                </thead>
                <tbody>
                  @foreach ($salas as $sala)
                  <tr>
                    <td> {{$sala->nome}} </td>
                    <td> {{$sala->numero}} </td>
                    <td> {{$sala->localizacao}} </td>
                    <td> {{$sala->tamanho_sala}} </td>
                    <td> {{$sala->nr_lugares}} </td>
                    
                    
                    
                    
                      @include('salas.popUp-incluir')

                      <td><input type="checkbox" checked data-toggle="toggle" data-onstyle="success"data-on="A" data-off="D"data-size="small">
                     
                                          
                      <td> <a href="/popUp-incluir/ {{$sala->id}}"type="button"
                        class="btn btn-outline-primary btn-sm"><i class="bi bi-search" 
                        style="font-size: 1rem;color:#000;" data-bs-toggle="modal" data-bs-target="#pessoa"></i>
                     </a>
                      <a href="/editar-salas/{{$sala->id}}" type="button"
                        class="btn btn-outline-warning btn-sm"><i class="bi bi-pen"
                            style="font-size: 1rem; color:#000;"></i>
                </a>
                <a href="/deletar-salas/{{$sala->id}}" type="button"
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
