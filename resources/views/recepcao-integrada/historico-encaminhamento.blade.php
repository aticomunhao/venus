@extends('layouts.app')

@section('title') Histórico Encaminhamento  @endsection

@section('content')


<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            HISTÓRICO DO ENCAMINHAMENTO
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <fieldset class="border rounded border-secondary p-4">
                    <div class="form-group row">
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Assistido:</label>
                            <input type="text" id="" value="{{$result[0]->nm_1}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-2">
                            <label for="disabledTextInput" class="form-label">Sexo:</label>
                            <input type="text" id="" value="{{$result[0]->tipo}}" style="text-align:center;" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-3">
                            <label for="disabledTextInput" class="form-label">Dt nascimento:</label>
                            <input type="date" class="form-control" id=""  name="date"  value="{{$result[0]->dt_nascimento}}"   class="form-control" placeholder="Disabled input" disabled>
                        </div>
                    </div>
                    </fieldset>
                    <br>
                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Dados do Atendimento Fraterno</legend>
                    @foreach($result as $results)
                    <table class="table table-sm table-bordered table-striped">
                        <thead style="text-align:center; background: #daffe0;">
                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                <td class="col">NR</td>
                                <td class="col-3">REPRESENTANTE</td>
                                <td class="col-1">PARENTESCO</td>
                                <td class="col-3">ATENDENTE</td>
                                <td class="col-1">DT/H INÍCIO</td>
                                <td class="col-1">DT/H FIM</td>
                                <td class="col-2">STATUS</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="text-align:center;font-size:13px">
                                <td>{{$results->ida}}</td>
                                <td>{{$results->nm_2}}</td>
                                <td>{{$results->nome}}</td>
                                <td>{{$results->nm_4}}</td>
                                <td>{{ date('d/m/Y G:i', strtotime($results->dh_inicio))}}</td>
                                <td>{{ date('d/m/Y G:i', strtotime($results->dh_fim))}}</td>
                                <td>{{$results->tst}}</td>
                            </tr>
                        </tbody>
                    </table>
                    @endforeach

                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Dados do Encaminhamento</legend>
                    @foreach($result as $results)
                    <table class="table table-sm table-bordered table-striped">
                        <thead style="text-align:center; background: #daffe0;">
                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                <td class="col">NR</td>
                                <td class="col">DATA</td>
                                <td class="col">TRATAMENTO</td>
                                <td class="col">GRUPO</td>
                                <td class="col">DIA</td>
                                <td class="col">HORÁRIO</td>
                                <td class="col">STATUS</td>
                                <td class="col">MOTIVO</td>
                            </tr>

                        </thead>
                        <tbody>
                            <tr style="text-align:center;font-size:13px">
                                <td>{{$results->ide}}</td>
                                <td>{{date ('d-m-Y', strtotime($results->dh_enc))}}</td>
                                <td>{{$results->desctrat}}</td>
                                <td>{{$results->nomeg}}</td>
                                <td>{{$results->nomedia}}</td>
                                <td>{{$results->rm_inicio}}</td>
                                <td>{{$results->tsenc}}</td>
                                <td>{{$results->tpmotivo}}</td>
                            </tr>
                        </tbody>
                    </table>
                    @endforeach

                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Dados de presenças</legend>
                    Nr de faltas: {{$faul}}
                    <table class="table table-sm table-bordered table-striped">
                        <thead style="text-align:center; background: #daffe0;">
                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                <td class="col">NR</td>
                                <td class="col">DATA</td>
                                <td class="col">PRESENÇA</td>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach($list as $lists)
                            <tr style="text-align:center;font-size:13px">
                                <td>{{$lists->idp}}</td>
                                <td>{{date ('d-m-Y', strtotime($lists->data))}}</td>

                                @if ($lists->presenca == true)
                                <td style="background-color:#90EE90;">Sim</td>

                                <td style="background-color:#FA8072;">Não</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br/>
              
                    <div class="row">
                        <div class="col">
                            <a class="btn btn-danger" href="/gerenciar-encaminhamentos" style="text-align:right;" role="button">Fechar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section('footerScript')


<script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>

@endsection
