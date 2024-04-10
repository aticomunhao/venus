@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    VISUALIZAR MEDIUNIDADE
                </div>
            </div>
        </div>

        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="/atualizar-mediunidade/{{ $mediunidade->id_pessoa}}/">
                @csrf

                <div class="row mt-3">
                    <div class="col-5">
                        <label for="id_pessoa" class="form-label">Nome</label>
                        <select name="id_pessoa" class="form-control" disabled>
                            <option value="{{ $mediunidade->idm }}"> {{ $mediunidade->nome_completo }} </option>
                            @foreach ($pessoas as $pessoa)
                            <option value="{{ $pessoa->id }}"> {{ $pessoa->nome_completo }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="tipo_status_pessoa" class="form-label ">Status</label>
                        <select class="form-control status" aria-label=".form-select-lg example" name="tipo_status_pessoa" disabled>
                            @foreach ($tipo_status_pessoa as $tipo)
                            <option value="{{ $tipo->id }}" {{ $mediunidade->tipo == $tipo->tipos ? 'selected' : '' }}>{{ $tipo->tipos }}</option>
                    
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="motivo_status" class="form-label">Motivo status</label>
                        <select class="form-control motivo" aria-label=".form-select-lg example" name="motivo_status" id="motivo_status" required="required" disabled>
                            <option value=""></option> 
                            @foreach ($tipo_motivo_status_pessoa as $motivo)
                                <option value="{{ $motivo->id }}" {{  $motivo->id == $mediunidade->motivo_status ? 'selected': '' }}>{{ $motivo->motivo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <label for="id_mediunidade" class="form-label"></label>
                    </div>
                    <div class="col">
                        <label for="data_inicio" class="form-label"></label>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <label for="id_mediunidade" class="form-label"></label>
                        <div class="table-responsive">
                            <div class="table">
                                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                    <thead>
                                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                          
                                            <th scope="col">Tipo de Mediunidade</th>
                                            <th scope="col">Data que manifestou</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tipo_mediunidade as $tipo)
                                            <tr>
                                               
                                                <td class="text-center">
                                                    {{ $tipo->tipo }}
                                                </td>
                                                <td>
                                                    <div class="form-group data_manifestou" name="id_mediunidade"
                                                        id="data_inicio_{{ $tipo->id }}">

                                                        <?php $i = 0; ?>
                                                    @foreach($mediunidadesIds as $dhi)
                                                    
                                                        @if ($dhi->id_mediunidade == $tipo->id)
                                                        <?php $i = 1; ?>
                                                        {{ $dhi->data_inicio ? date('d/m/Y', strtotime($dhi->data_inicio)) : '--' }}
                                                          @endif
                                                          @endforeach
                                                                    @if($i == 0)
                                                                    --
                                                        @endif
                                                       
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                

                <div class="row mt-1 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-primary" href="/gerenciar-mediunidades" role="button">Retornar</a>
                    </div>
                    
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
