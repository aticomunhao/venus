@extends('layouts.app')

@section('title') Alterar usuário @endsection

@section('content')

<br>
<div class="container">
    <div class="row align-items-start">
        <div class="col">       
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="fs-5 fw-bold">USUÁRIO   <i class="bi bi-people-fill"></i></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">                                       
                    <p>Nome:<strong> {{$result[0]->nome_completo}}</strong></p>
                    <p>Cpf: <strong> {{$result[0]->cpf}}</strong> </p>
                    <p>Identidade:<strong>  {{$result[0]->idt}}</strong> </p>
                    <p>Data de Nascimento:<strong>  {{$result[0]->dt_nascimento}}</strong> </p>
                    <p>Email: <strong> {{$result[0]->email}}</strong> </p>            
                    <form class="form-horizontal mt-4" method="POST" action="/usuario-atualizar/{{$resultUsuario[0]->id}}">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="idPessoa" value="{{$result[0]->id}}">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <tr>
                            <td>
                                Ativo
                            </td>
                            <td>
                                <input type="checkbox" id="ativo" name="ativo" switch="bool" {{$resultUsuario[0]->ativo ? 'checked' : ''}} />
                                <label for="ativo" data-on-label="Sim" data-off-label="Não"></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Bloqueado
                            </td>
                            <td>
                                <input type="checkbox" id="bloqueado" name="bloqueado" switch="bool" {{$resultUsuario[0]->bloqueado ? 'checked' : ''}}/>
                                <label for="bloqueado" data-on-label="Sim" data-off-label="Não"></label>
                            </td>
                        </tr>
                    </table>
                </div>
                </br>
                <div class="card">
                    <div class="card-header">
                        <div class="col">
                            <div class="row">                            
                                <h4 class="fs-5 fw-bold">PERFIS  <i class="bi bi-key-fill" ></i></h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                @foreach($resultPerfil as $resultPerfils)
                                <tr>
                                    <td>
                                        {{$resultPerfils->nome}}
                                    </td>
                                    <td>

                                        <input type="checkbox" id="{{$resultPerfils->nome}}" name="{{$resultPerfils->nome}}" value="{{$resultPerfils->id}}"  {{in_array($resultPerfils->id,$resultPerfisUsuarioArray) ? 'checked' : ''}}/>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                </br>
                <div class="card">
                    <div class="card-header">
                        <div class="col">
                            <div class="row">                            
                                <h4 class="fs-5 fw-bold">DEPÓSITOS  <i class="bi bi-safe-fill" ></i></h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                @foreach($resultDeposito as $resultDepositos)
                                <tr>
                                    <td>
                                        {{$resultDepositos->nome}}
                                    </td>
                                    <td>

                                        <input type="checkbox" id="{{$resultDepositos->nome}}" name="{{$resultDepositos->nome}}" value="{{$resultDepositos->id}}"  {{in_array($resultDepositos->id,$resultDepositoUsuarioArray) ? 'checked' : ''}}/>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">         
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="fs-5 fw-bold">SETORES  <i class="bi bi-intersect"></i></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            @foreach($resultSetor as $resultSetor)
                            <tr>
                                <td>
                                    {{$resultSetor->nome}}
                                </td>
                                <td>
                                    <input type="checkbox" id="{{$resultSetor->nome}}" name="{{$resultSetor->nome}}" value="{{$resultSetor->id}}" {{in_array($resultSetor->id,$resultSetorUsuarioArray) ? 'checked' : ''}}>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</br>
    <div class="row">
        <div class="col">
            <a href="/gerenciar-usuario"><input class="btn btn-danger btn-block" type="button" value="Cancelar">
            </a>
        </div>
        <div class="col">
            <button type="submit" class="btn btn-primary btn-block">Confirmar</button>
        </div>
    </div>
    </form>
</div>
</br>




@endsection

@section('footerScript')
            <!-- Required datatable js -->
           <script src="{{ URL::asset('/libs/datatables/datatables.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/jszip/jszip.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/pdfmake/pdfmake.min.js')}}"></script>

            <!-- Datatable init js -->
            <script src="{{ URL::asset('/js/pages/datatables.init.js')}}"></script>
            <script src="{{ URL::asset('/libs/select2/select2.min.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/form-advanced.init.js')}}"></script>

@endsection
