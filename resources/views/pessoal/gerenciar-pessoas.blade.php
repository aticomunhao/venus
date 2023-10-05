@extends('layouts.app')

@section('title') Gerenciar Pessoas @endsection

@section('content')

<div class="container-fluid";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR PESSOAS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
                <form action="{{route('pesdex')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class="col-4">Nome
                        <input class="form-control" type="text" maxlength="45" oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="1" name="nome" value="">
                    </div>
                    <div class="col-2">CPF
                        <input class="form-control" type="text" maxlength="11" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="2" name="cpf" value="">
                    </div>
                    <div class="col-2">Status
                        <select class="form-select" id="3" name="status" type="numeric" required="required">
                            <option value="1">Ativo</option> 
                            <option value="">Todos</option>
                            <option value="2">Desencarnou</option> 
                            <option value="3">Mudou</option>                    
                        </select>
                    </div>
                    <div class="col"><br>
                        <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                        <a href="/gerenciar-pessoas"><input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    </form>                            
                        <button class="btn btn-success btn-sm" style="font-size: 0.9rem; color:#FFF;  box-shadow: 1px 2px 5px #000000;" type="button" id="" data-bs-toggle="modal" data-bs-target="#pessoa">Nova Pessoa  &plus;</button>
                    </div>
                        @include('pessoal.popUp-incluir')
                </div>
        </div>

            <hr>
            Quantidade filtrada: {{$soma}}
            <div class="table">
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:15px; color:#000000">
                            <th class="col">NOME</th>
                            <th class="col">CPF</th>
                            <th class="col">NASCIMENTO</th>
                            <th class="col">SEXO</th>
                            <th class="col">EMAIL</th>
                            <th class="col">DDD</th>
                            <th class="col">TELEFONE</th>
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align:center;">
                    @foreach($pessoa as $pessoas)
                        <tr>                        
                            <td scope="" >{{$pessoas->nome_completo}}</td>
                            <td scope="" >{{str_pad($pessoas->cpf, 11, "0", STR_PAD_LEFT)}}</td>
                            <td scope="" >{{date( 'd/m/Y' , strtotime($pessoas->dt_nascimento))}}</td>
                            <td scope="" >{{$pessoas->tipo}}</td>
                            <td scope="" >{{$pessoas->email}}</td>
                            <td scope="" >{{str_pad($pessoas->ddd, 2, "0", STR_PAD_LEFT)}}</td>
                            <td scope="" >{{$pessoas->celular}}</td>
                            <td scope="" >{{$pessoas->nmstatus}}</td>
                            <td scope="">                                
                                <a href="/editar-pessoa/{{$pessoas->idp}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/excluir-pessoa/{{$pessoas->idp}}"><button type="button" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i></button></a>
                            </td>                            
                        </tr>
                        @endforeach                        
                    </tbody>
                </table>
            </div class="d-flex justify-content-center">
            {{$pessoa->withQueryString()->links()}}
        </div>
    </div>
</div>




@endsection

@section('footerScript')  


@endsection
