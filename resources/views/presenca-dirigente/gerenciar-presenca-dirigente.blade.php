@extends('layouts.app')

@section('title')
    Gerenciar Presença Dirigente
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR PRESENÇA
            DIRIGENTE
        </h4>


        <form action="" class="form-horizontal mt-4" method="GET">
            <div class="row justify-content-center" style="display: flex; align-items:flex-end">

                <div class="col-12">
                    Grupo
                    <select class="form-select select2" name="grupo">
                        @foreach ($reunioes as  $reuniao)
                            <option value="{{$reuniao->id}}" {{$reuniao->id == $reunioesDirigentes[0] ? 'selected' : ''}}>{{$reuniao->nome . ' - ' .$reuniao->dia}}</option>
                            @endforeach
                        </select>
                       
                  
                </div>
                <div class="col-12">
                    Nome
                    <select class="form-select select2" name="nome_setor">

                    </select>
                  
                </div>

                

                <div class="col"><br />
                    <input class="btn btn-light btn-sm me-md-2 col-6 col-12"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                        value="Pesquisar">
                    
                </div>
                <div class="col"><br />
                    <a href="/gerenciar-presenca-dirigente"><input class="btn btn-light btn-sm me-md-2 col-12"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                        value="Limpar"></a>
                    
                </div>
            </div>
        </form>
        <hr />
        <hr>

            <div class="col">
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col ">NOME</th>
                            <th class="col">FUNÇÃO</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align:center;">
                        
                        @foreach ($membros as $membro)
                            
                        <tr>
                            <td> {{$membro->nome_completo}} </td>
                            <td> {{$membro->nome}} </td>
                            <td>  

                            </td>
                        </tr>


                        @endforeach


                    </tbody>
                </table>


                

    </div>

    @endsection