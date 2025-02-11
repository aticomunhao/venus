@extends('layouts.app')

@section('title')
    Currículo de Membro
@endsection

@section('content')
    <div class="container">
        <div class="justify-content-center">
            <br>
            <div class="card">
                <div class="card-header">
                    DADOS PESSOAIS
                </div>
                <div class="card-body">
                    <div class="row ">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="nome_completo" class="form-label">Nome do médium</label>
                                <input type="text" class="form-control" name="nome_completo"
                                    value="{{ $dadosP->nome_completo }}" disabled>
                            </div>
                        </div>
                        <div class="col-2">
                            <label for="dt_nascimento" class="form-label">Data de nascimento</label>
                            <input type="text" class="form-control" name="dt_nascimento"
                                value="{{ $dadosP->dt_nascimento ? date('d/m/Y', strtotime($dadosP->dt_nascimento)) : '' }}"
                                disabled>
                        </div>
                        <div class="col-2">
                            <label for="celular" class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="celular"
                                value="{{ $dadosP->descricao ? '(' . $dadosP->descricao . ')' : '' }} {{ $dadosP->celular }}"
                                disabled>
                        </div>
                        <div class="col-2">
                            <label for="nr_associado" class="form-label">Número de associado</label>
                            <input type="text" class="form-control" name="nr_associado"
                                value="{{ $dadosP->nr_associado }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
            @foreach($membros as $key => $membro)

            <br />
            <div class="card">
                <div class="card-header">
                    {{$key}}
                </div>
                <div class="card-body">
                    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                        <thead>
                            <tr style="background-color: #d6e3ff; font-size:12px; color:#000000; padding: 2px;">
                                <th style="padding: 4px;">FUNÇÃO</th>
                                <th style="padding: 4px;">INICIO</th>
                                <th style="padding: 4px;">FIM</th>
                                <th style="padding: 4px;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach ($membro as $dadoMembro)
                            <tr>
                                <td> {{ $dadoMembro->nome_funcao }} </td>
                                <td> {{ $dadoMembro->dt_inicio }} </td>
                                <td> {{ $dadoMembro->dt_fim }} </td>
                                <td> {{ $dadoMembro->status_membro }} </td>
                            </tr>
                    @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    </div>
@endsection
