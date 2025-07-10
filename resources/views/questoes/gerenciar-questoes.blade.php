@extends('layouts.app')
@section('title')
  Gerenciar Questões
@endsection
@section('content')


<div class="container">
    
    <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
        GERENCIAR QUESTÕES</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <form action="" class="form-horizontal mt-4" method="GET">
                    <div class="row">
                        <div class="col-4">Nome
                            <input class="form-control" type="text" maxlength="45"
                            oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                            id="1" name="nome" value=>
                        </div>
                
                        <div class="col"><br>
                            <input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                            value="Pesquisar">
                            <a href="/gerenciar-atendentes-apoio"><input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                value="Limpar"></a>
                            </form>
                            <a href="/criar-questoes"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                                type="button" value="Nova Questão +"></a>
            </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    
                    
                    <div id="tbl">
                        <table  class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                            <thead style="text-align: center;">
                                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                    <th class="col">NOME</th>
                                    <th class="col">STATUS</th>
                                    <th class="col">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px; color:#000000; text-align:center;">
                            </tbody>
                        </table>
                        @endsection