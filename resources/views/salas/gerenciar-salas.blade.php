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
                        <th class="col">ID</th>
                        <th class="col">NOME</th>
                        <th class="col">NÚMERO</th>
                        <th class="col">NR_LUGARES</th>
                        <th class="col">LOCALIZAÇÃO</th>
                        <th class="col">PROJETOR</th>
                        <th class="col">PC</th>
                        <th class="col">QUADRO</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                        </a>
                    </td>
                </tr>
        </table>
      </div>
</div>
@endsection
