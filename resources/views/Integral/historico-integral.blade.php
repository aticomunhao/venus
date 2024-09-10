@extends('layouts.app')

@section('title') Histórico  @endsection

@section('content')


<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            HISTÓRICO DO TRATAMENTO
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <fieldset class="border rounded border-secondary p-4">
                    <div class="form-group row">
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Assistido:</label>
                            <input type="text" id="" value="{{$result->nm_1}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-2">
                            <label for="disabledTextInput" class="form-label">Sexo:</label>
                            <input type="text" id="" value="{{$result->tipo}}" style="text-align:center;" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-3">
                            <label for="disabledTextInput" class="form-label">Dt nascimento:</label>
                            <input type="date" class="form-control" id=""  name="date"  value="{{$result->dt_nascimento}}"   class="form-control" placeholder="Disabled input" disabled>
                        </div>
                    </div>
                    </fieldset>
                    <br>
                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Dados do Atendimento Fraterno</legend>
                
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
                                <td>{{$result->ida}}</td>
                                <td>{{$result->nm_2}}</td>
                                <td>{{$result->nome}}</td>
                                <td>{{$result->nm_4}}</td>
                                <td>{{$result->dh_inicio}}</td>
                                <td>{{$result->dh_fim}}</td>
                                <td>{{$result->statat}}</td>
                            </tr>
                        </tbody>
                    </table>
      
                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Dados do Tratamento</legend>
     
                    <table class="table table-sm table-bordered table-striped">
                        <thead style="text-align:center; background: #daffe0;">
                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                <td class="col">NR</td>
                                <td class="col">DATA</td>
                                <td class="col">TRATAMENTO</td>
                                <td class="col">GRUPO</td>
                                <td class="col">HORÁRIO</td>
                                <td class="col">SALA</td>
                                <td class="col">STATUS</td>
                                <td class="col">MOTIVO</td>
                            </tr>

                        </thead>
                        <tbody>
                            <tr style="text-align:center;font-size:13px">
                                <td>{{$result->ide}}</td>
                                <td>{{date ('d-m-Y', strtotime($result->dh_enc))}}</td>
                                <td>{{$result->desctrat}}</td>
                                <td>{{$result->nomeg}}</td>
                                <td>{{$result->rm_inicio}}</td>
                                <td>{{$result->sala}}</td>
                                <td>{{$result->tsenc}}</td>
                                <td>{{$result->tpmotivo}}</td>
                            </tr>
                        </tbody>
                    </table>
      
                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Dados de Presenças Integral</legend>
                    Nr de faltas: {{$faul}}
                    <table class="table table-sm table-bordered table-striped">
                        <thead style="text-align:center; background: #daffe0;">
                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                <td class="col">NR</td>
                                <td class="col">DATA</td>
                                <td class="col">GRUPO</td>
                                <td class="col">PRESENÇA</td>
                            </tr>
                            
                        </thead>
                        <tbody>
                            @foreach($list as $lists)
                            <tr style="text-align:center;font-size:13px">
                                <td>{{$lists->idp}}</td>
                                 <td>{{$lists->data}}</td>
                                 <td>{{$lists->nome}}</td>
                                @if ($lists->presenca == 1)
                                <td style="background-color:#90EE90;">Sim</td>
                                @elseif ($lists->presenca == 0)
                                <td style="background-color:#FA8072;">Não</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Dados de Presenças PTD</legend>
                    Nr de faltas: {{$faul2}}
                    <table class="table table-sm table-bordered table-striped">
                        <thead style="text-align:center; background: #daffe0;">
                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                <td class="col">NR</td>
                                <td class="col">DATA</td>
                                <td class="col">GRUPO</td>
                                <td class="col">PRESENÇA</td>
                            </tr>
                            
                        </thead>
                        <tbody>
                            @foreach($list2 as $lists1)
                            <tr style="text-align:center;font-size:13px">
                                <td>{{$lists1->idp}}</td>
                                 <td>{{$lists1->data}}</td>
                                 <td>{{$lists1->nome}}</td>
                                @if ($lists1->presenca == 1)
                                <td style="background-color:#90EE90;">Sim</td>
                                @elseif ($lists1->presenca == 0)
                                <td style="background-color:#FA8072;">Não</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
              
                    <br/>
                    
                    <div class="row">
                        <div class="col">
                            <a class="btn btn-danger" href="/gerenciar-integral" style="text-align:right;" role="button">Fechar</a>
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
