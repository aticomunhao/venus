@extends('layouts.app')

@section('title') Gerenciar Atendimentos @endsection

@section('content')



<div class="container-fluid";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ENCAMINHAMENTOS INTEGRAL</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="/gerenciar-encaminhamentos-pti" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class ="col">Data início
                        <input class="form-control" type="date" id="" name="dt_enc" value="{{$data_enc}}">
                    </div>
                    <div class="col-5">Assistido
                        <input class="form-control" type="text" id="3" name="assist" value="{{$assistido}}">
                    </div>
                    <div class="col">Status
                        <select class="form-select" id="4" name="status" type="number">
                            <option value="{{$situacao}}"></option>
                            @foreach ($stat as $status)
                            <option value="{{$status->id}}">{{$status->descricao}}</option>
                            @endforeach
                        </select>
                    </div>
                        <div class="col"><br/>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-encaminhamentos-integral"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    </form>
                        </div>
                </div>
                <br/>
            </div style="text-align:right;">
            <hr/>
            <div class="table">Total assistidos: {{$contar}}</div>
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col">HORÁRIO ENCAMINHAMENTO</th>
                            <th class="col">PRIORIDADE</th>
                            <th class="col">ASSISTIDO</th>
                            <th class="col">REPRESENTANTE</th>
                            <th class="col">TIPO TRATAMENTO</th>
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($lista as $listas)
                            <td>{{$listas->ide}}</td>
                            <td>{{date ('d/m/Y ', strtotime($listas->dh_enc))}}</td>
                            <td>{{$listas->prdesc}}</td>
                            <td>{{$listas->nm_1}}</td>
                            <td>{{$listas->nm_2}}</td>
                            <td>{{$listas->desctrat}}</td>
                            <td>{{$listas->tsenc}}</td>
                            <td>
                                @if ($listas->status_encaminhamento == 1)
                                <a href="/agendar-integral/{{$listas->ide}}/{{$listas->idtt}}"><button type="button" class="btn btn-outline-success btn-sm tooltips"><span class="tooltiptext">Agendar</span><i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i></button></a>
                                @elseif($listas->status_encaminhamento == 3)
                                {{-- botao de alterar grupo --}}
                                <a href="/alterar-grupo-tratamento-integral/{{ $listas->ide }}"type="button"
                                    class="btn btn-outline-success btn-sm tooltips"<i class="bi bi-arrow-left-right"
                                        style="font-size: 1rem; color:#000;"><span class="tooltiptext">Alterar Grupo</span></i></a>
                                @else
                                <button type="button"
                                    class="btn btn-outline-success btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Alterar Grupo" disabled><i class="bi bi-arrow-left-right"
                                        style="font-size: 1rem; color:#000;" ></i></button>
                                @endif

                                <a href="/visualizar-enc-integral/{{$listas->ide}}"><button type="button" class="btn btn-outline-primary btn-sm tooltips"><span class="tooltiptext">Histórico</span><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                                @if ($listas->status_encaminhamento < 2)
                                <button class="btn btn-outline-danger btn-sm tooltips" type="button" id="" data-bs-toggle="modal" data-bs-target="#inativar{{$listas->ide}}"><span class="tooltiptext">Inativar</span><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button>
                                @else
                                <button class="btn btn-outline-danger btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#inativar{{$listas->ide}}" data-tt="tooltip" data-placement="top" title="Inativar" disabled><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button>
                                @endif
                            </td>

                            <form action="/inativar-integral/{{$listas->ide}}">
                                <div class="modal fade" id="inativar{{$listas->ide}}" data-bs-keyboard="false"
                                    tabindex="-1" aria-labelledby="inativarLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color:#DC4C64;color:white">
                                                <h1 class="modal-title fs-5" id="inativarLabel">Inativação</h1>
                                                <button data-bs-dismiss="modal" type="button" class="btn-close"
                                                    aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <center>
                                                    <label for="recipient-name" class="col-form-label"
                                                        style="font-size:17px">Tem certeza que deseja inativar:<br /><span
                                                            style="color:#DC4C64; font-weight: bold;">{{ $listas->nm_1 }}</span>&#63;</label>
                                                </center>
                                                <br />

                                                <center>
                                                    <div class="mb-2 col-10">
                                                        <label class="col-form-label">Insira o motivo da
                                                            <span style="color:#DC4C64">inativação:</span></label>
                                                        <select class="form-select teste1" name="motivo" required>

                                                            @foreach ($motivo as $motivos)
                                                                <option value="{{ $motivos->id }}">
                                                                    {{ $motivos->tipo }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </center>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-bs-dismiss="modal"
                                                    class="btn btn-danger">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Confirmar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- fim modal de inativação --}}
                            </form>





                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div class="d-flex justify-content-center">
            {{$lista->links('pagination::bootstrap-5')}}
        </div>

    </div>
</div>





@endsection

@section('footerScript')


@endsection
