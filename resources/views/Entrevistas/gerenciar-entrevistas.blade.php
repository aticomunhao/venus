@extends('layouts.app')

@section('title') Gerenciar Entrevista @endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<div class="container">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ENTREVISTA</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{ route('gerenciamento') }}" class="form-horizontal mt-4" method="GET">
                    <div class="row">
                        <div class="col">Nome
                            <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" value = {{$pesquisaNome}}>
                        </div>

                        <div class="col">Status
                            <select class="form-select" id="4" name="status" type="number">
                                <option ></option>
                                <option value=1 {{$pesquisaValue == 1 ? 'selected' : ''}} >Aguardando agendamento</option>
                                <option value=2 {{$pesquisaValue == 2 ? 'selected' : ''}}>Agendado</option>
                                <option value=3 {{$pesquisaValue == 3 ? 'selected' : ''}}>Entrevistado</option>
                            </select>
                        </div>
                        <div class="col"><br/>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-entrevistas"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                        </div>
                    </div>
                </form>
                <br/>
            </div>
            <hr/>
            <div class="table">Total assistidos:
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col">NOME</th>
                            <th class="col">DATA </th>
                            <th class="col">HORA </th>
                            <th class="col">ENTREVISTADOR</th>
                            <th class="col">SALA</th>
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        @foreach($informacoes as $informacao)
                        <tr>
                            <td>{{ $informacao->ide }}</td>
                            <td>{{ $informacao->nome_pessoa }}</td>
                            <td>{{ !is_null($informacao->data) ? date('d-m-Y', strtotime($informacao->data)) : '--' }}</td>
                            <td>{{ !is_null($informacao->hora) ? date('G:i', strtotime($informacao->hora)) : '--' }}</td>
                            <td>{{ $informacao->nome_entrevistador }}</td>
                            <td>{{ $informacao->local }}</td>
                            <td>
                                @if($informacao->status === 'Aguardando agendamento')
                                    Aguardando agendamento
                                @else
                                    {{ $informacao->status }}
                                @endif
                            </td>
                            <td>
                                @if($informacao->status == 'Aguardando agendamento'  )
                                    <a href="#"
                                       type="button"
                                       class="btn btn-outline-warning btn-sm disabled"
                                       data-tt="tooltip"
                                       data-placement="top"
                                       title="Editar" disabled>
                                       <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @else
                                    <a href="/editar-entrevista/{{$informacao->ide}}"
                                       type="button"
                                       class="btn btn-outline-warning btn-sm"
                                       data-tt="tooltip"
                                       data-placement="top"
                                       title="Editar">
                                       <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @endif

                                @if($informacao->status !== 'Aguardando agendamento')
                                    <a href="#"
                                       type="button"
                                       class="btn btn-outline-success btn-sm disabled"
                                       data-tt="tooltip"
                                       data-placement="top"
                                       title="Agendar" disabled>
                                       <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @else
                                    <a href="{{ route('criar-entrevista', ['id' => $informacao->ide]) }}"
                                       type="button"
                                       class="btn btn-outline-success btn-sm"
                                       data-tt="tooltip"
                                       data-placement="top"
                                       title="Agendar">
                                       <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @endif

                                @if($informacao->status == 'Aguardando agendamento')
                                    <a href="#"
                                       type="button"
                                       class="btn btn-outline-primary btn-sm disabled"
                                       data-tt="tooltip"
                                       data-placement="top"
                                       title="historico" disabled>
                                       <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @else
                                    <a href="/visualizar-entrevista/{{ $informacao->ide }}"
                                       type="button"
                                       class="btn btn-outline-primary btn-sm"
                                       data-tt="tooltip"
                                       data-placement="top"
                                       title="Histórico">
                                       <i class="bi bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @endif

                                @if($informacao->status !== 'Agendado')
                                <a href="#"
                                   type="button"
                                   class="btn btn-outline-success btn-sm disabled"
                                   data-tt="tooltip"
                                   data-placement="top"
                                   title="Finalizar" disabled>
                                   <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            @else
                                <a href="/finalizar-entrevista/{{ $informacao->ide }}"
                                   type="button"
                                   class="btn btn-outline-success btn-sm"
                                   data-tt="tooltip"
                                   data-placement="top"
                                   title="Finalizar">
                                   <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            @endif


                            @if($informacao->status == 'Entrevistado')
                            <a href="#"
                               type="button"
                               class="btn btn-outline-danger btn-sm disabled"
                               data-tt="tooltip"
                               data-placement="top"
                               title="Inativar" disabled>
                               <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                            </a>
                        @else
                            <a href="/inativar-entrevista/{{ $informacao->ide }}"
                               type="button"
                               class="btn btn-outline-danger btn-sm"
                               data-tt="tooltip"
                               data-placement="top"
                               title="Inativar">
                               <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                            </a>
                        @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endsection

@section('footerScript')
@endsection
