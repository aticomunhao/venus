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
                            <input type="date" id=""  name="date"  value="{{$result[0]->dt_nascimento}}"   class="form-control" placeholder="Disabled input" disabled>
                        </div>
                    </div>
                    </fieldset>
                    <br>
                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Histórico do Atendimento Fraterno</legend>
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
                            <tr style="text-align:center;font-size:11px">       
                                <td>{{$results->ida}}</td>
                                <td>{{$results->nm_2}}</td>
                                <td>{{$results->nome}}</td>
                                <td>{{$results->nm_4}}</td>
                                <td>{{$results->dh_inicio}}</td>
                                <td>{{$results->dh_fim}}</td>
                                <td>{{$results->tst}}</td>
                                
                            </tr>                                          
                        </tbody>
                    </table>
                    <br/>         
                    @endforeach

                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Histórico do Encaminhamento</legend>
                    @foreach($result as $results) 
                    <table class="table table-sm table-bordered table-striped">
                        <thead style="text-align:center; background: #daffe0;">
                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                <td class="col">NR</td>    
                                <td class="col-3">DATA</td>
                                <td class="col-1">TRATAMENTO</td>
                                <td class="col-1">GRUPO</td>
                                <td class="col-1">HORÁRIO</td>
                                <td class="col-2">STATUS DO ENC</td>                                
                            </tr>

                        </thead>
                        <tbody>
                            <tr style="text-align:center;font-size:11px">       
                                <td>{{$results->ide}}</td>
                                <td>{{$results->nm_2}}</td>
                                <td>{{$results->nome}}</td>
                                <td>{{$results->nm_4}}</td>
                                <td>{{$results->dh_inicio}}</td>
                                <td>{{$results->dh_fim}}</td>
                                <td>{{$results->tst}}</td>
                                
                            </tr>                                          
                        </tbody>
                    </table>
                    <br/>         
                    @endforeach
                    <br>
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