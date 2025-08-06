@extends('layouts.app')

@section('title')
    Visualizar Inscrição
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>VISUALIZAR INSCRIÇÃO</span>
                        <a href="/gerenciar-inscricao" class="btn-close" aria-label="Fechar"></a>
                    </div>
                    <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-lg-4 col-12">
                                    <label for="grupo" class="form-label">Grupo</label>
                                    <input class="form-control" type="text" value="{{ $inscricao[0]->nomeg }}" disabled>
                                </div>
                                <div class="col-lg-4 col-12">
                                    <label for="tratamento" class="form-label">Atividade</label>
                                    <input class="form-control" type="text" value="{{ $inscricao[0]->trnome }}" disabled>
                                </div>
                                <div class="col-lg-2 col-6">
                                    <label for="semestre" class="form-label">Semestre</label>
                                    <input class="form-control" type="text" value="{{ $inscricao[0]->sesigla }}" disabled>                                    
                                </div>
                                <div class="col-lg-2 col-6">
                                    <label for="h_fim" class="form-label">Observação</label>
                                    <input class="form-control" value="{{$inscricao[0]->observacao}}" disabled>
                                </div>
                            </div>
                            <div class="row mt-3">
                                 <div class="col-lg-6 col-6">
                                    <label for="max_atend" class="form-label">Modalidade</label>
                                    <input  type="text" class="form-control" value="{{ $inscricao[0]->nmodal }}" disabled>
                                </div>
                                <div class="col-lg-6 col-6">
                                    <label for="dia" class="form-label">Dia da semana</label>                                 
                                    <input class="form-control" type="text" value="{{ $inscricao[0]->nomed }}" disabled>
                                </div>
                                <div class="col-lg-6 col-6">
                                    <label for="h_inicio" class="form-label">Hora de início</label>
                                    <input class="form-control" type="time" value="{{ $inscricao[0]->h_inicio }}" disabled>
                                </div>
                                <div class="col-lg-6 col-6">
                                    <label for="h_fim" class="form-label">Hora de fim</label>
                                    <input class="form-control" type="time" value="{{ $inscricao[0]->h_fim }}" disabled>
                                </div>
                            </div>
                            <div class="row mt-3">                               
                                <div class="ccol-lg-6 col-6">
                                    <label class="form-label">Data Inicio</label>
                                    <input type="date" class="form-control"value="{{ $inscricao[0]->data_inicio }}" disabled>
                                </div>
                                <div class="col-lg-6 col-6">
                                    <label class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" value="{{ $inscricao[0]->data_fim }}" disabled>
                                </div>
                                
                            </div>
                            <br />
                    </div>
                </div>
                <br />
                <div class="card">
                    <div class="card-header">
                       <div class="row">
                            <div class="col">
                                SALA
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-6">
                                <label for="id_sala" class="form-label">Número</label>
                                <input  type="number" class="form-control" value="{{ $inscricao[0]->numero }}" disabled>
                            </div>
                            <div class="col-lg-6 col-6">
                                <label for="localizacao" class="form-label">Localização</label>
                                <input type="text" class="form-control"  value="{{ $inscricao[0]->sloc }}" disabled>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-inscricao" role="button">Fechar</a>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
