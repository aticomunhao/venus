@extends('layouts.app')

@section('content') 
<div class="container-fluid";>
  <div class="col-12">
          <div class="row justify-content-center">
              <div>
<div class="container">      
      
  <div class="row">
<div class="col" style="text-align: right">
      <a ref="/novofato"> <button class="btn btn-success" id="name=" type="button">NOVO+</a>

 </div> 
  </div>    
  <table class="table">
    <thead>
      <tr>
       <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
        <th scope="col">ID</th>
        <th scope="col">DESCRIÇÃO</th>
        <th scope="col">AÇÕES</th>
    
  
      @foreach ($lista as $listas)

         <tr>
        <td>{{$listas->id}}</td>
        <td>{{$listas->descricao}}</td>
        
        <td> <a href="/editar-fatos/{{$lista[0]->id}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i></button></a>
        
        </tr>
      @endforeach
       
    </tbody>
  </table>
  </div>

@endsection


