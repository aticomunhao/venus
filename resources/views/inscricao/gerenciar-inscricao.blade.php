@extends('layouts.app')

@section('title')
    Gerenciar Inscrições
@endsection

@section('content')


<div class="container-fluid";>
    <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR INSCRIÇÕES </h4>
    <div class="col-12">
        <form action="{{ route('index.insc') }}" class="form-horizontal mt-4" method="GET">
        <div class="row justify-content-center" style="text-align: midlle;">               
            <div class="col-md-1 col-6 mb-2">Dia
                <select class="form-select semana" id="4" name="semana">
                    <option value="" {{ request('semana') == '' ? 'selected' : '' }}>Todos
                    </option>
                    @foreach ($tpdia as $dias)
                        <option value="{{ $dias->idtd }}"
                            {{ request('semana') == $dias->idtd && request('semana') != '' ? 'selected' : '' }}>
                            {{ $dias->nomed }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 col-6 mb-2">CPF
                <input class="form-control" type="text" maxlength="11" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="2" name="cpf" value="{{$cpf}}" style="font-size: 15px">
            </div>
            <div class="col-md-2 col-12 mb-2">Nome
                <input class="form-control" type="text" maxlength="45" oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="1" name="pessoa" value="{{$pessoa}}">
            </div>                 
            <div class="col-md-2 col-6 mb-2">Atividade
                <select class="form-select select2" id="tipo_tratamento" name="tipo_tratamento">
                    <option value="">Todos</option>
                    @foreach ($tipo_tratamento as $tipot)
                        <option value="{{ $tipot->idt }}" {{ request('tipo_tratamento') == $tipot->idt ? 'selected' : '' }}>
                            {{ $tipot->descricao }}-{{ $tipot->tipo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 col-6 mb-2">Semestre
                <select class="form-select select2" id="semestre" name="semestre">
                    <option value="">Todos</option>
                    @foreach ($tipo_semestre as $tipos)
                        <option value="{{ $tipos->ids }}" {{ request('semestre') == $tipos->ids ? 'selected' : '' }}>
                            {{ $tipos->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>            
            <div class="col-lg-1 col-6 d-flex justify-content-center align-items-center">
                <input class="btn btn-light btn-sm w-100"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
            </div>
            <div class="col-lg-1 col-6 d-flex justify-content-center align-items-center">
                <a href="/gerenciar-inscricao" class="btn btn-light btn-sm w-100"
                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" >Limpar</a>
            </div>
            <div class="col-lg-1 col-6 d-flex justify-content-center align-items-center">                    
                <button type="button" class="btn btn-info btn-sm w-100" style="box-shadow: 1px 2px 5px #000000; margin:5px;" data-bs-toggle="modal" data-bs-target="#maisfiltros" style="margin:5px;">
                    Mais filtros
                </button>
            </div>
            <div class="col-lg-1 col-6 d-flex justify-content-center align-items-center">
                <a href="/formar-inscricao" class="btn btn-success btn-sm w-100"
                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" autofocus>Nova +
                </a>                
            </div>
            <!-- Modal -->
            <div class="modal fade" id="maisfiltros" tabindex="-1" aria-labelledby="filtrosPopupLabel" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="filtrosPopupLabel">Filtros Avançados</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">                
                            <div class="row px-3" style="margin-left: 10px; margin-right:10px;">
                                <!-- SETOR -->
                                <div class="col-12 mt3">Setor:
                                    <select class="form-select select2" id="meuSelect" name="setor" >
                                        <option value="">Selecione</option>
                                        Todos</option>
                                        @foreach ($setores as $setoress)
                                            <option value="{{ $setoress->id }}"
                                                {{ request('setor') == $setoress->id ? 'selected' : '' }}>
                                                {{ $setoress->sigla }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- GRUPO -->
                                <div class="col-12 mt-3">Grupo:
                                    <select class="form-select select2" id="" name="grupo">
                                        <option value="">Selecione</option>
                                        @foreach ($grupos as $gruposs)
                                            <option value="{{ $gruposs->idg }}"
                                                {{ request('grupo') == $gruposs->idg ? 'selected' : '' }}>
                                                {{ $gruposs->nomeg }} - {{ $gruposs->sigla }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- MODALIDADE -->
                                <div class="col-12 mt-3">Modalidade:
                                    <select class="form-select" name="modalidade">
                                        <option value="">Selecione</option>
                                        @foreach ($tmodalidade as $modal)
                                            <option value="{{ $modal->idm }}" {{ $modal->idm == request('modalidade') ? 'selected' : '' }}>
                                                {{ $modal->nomem }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- STATUS -->
                                <div class="col-12 mt-3">Status:
                                    <select class="form-select" name="status">
                                        <option value="">Selecione</option>
                                        @foreach ($situacao as $situ)
                                            <option value="{{ $situ->ids }}" {{ $situ->ids == request('status') ? 'selected' : '' }}>
                                                {{ $situ->descs }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>             
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fim Modal -->
            </form>
        </div>
        <hr>
        <div class="row">
            <div class="col">
                <span style="font-size: 18px;">Qtd Inscritos: {{ $contar }}</span>
            </div>
            <div class="col" style="text-align:right;">
                <span class="text-warning" style="font-size: 18px;">&#9632;</span>
                <span style="font-size: 15px;">Inscrição Pendente</span>
            </div>
        </div>            
        <div class="row">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="font-size:14px; color:#000000">
                        <th class="col d-none d-lg-table-cell">Nr</th>
                        <th class="col-2">NOME</th>
                        <th class="col">SETOR</th>
                        <th class="d-none d-lg-table-cell">MODALIDADE</th>
                        <th class="col">DIA</th>                                              
                        <th class="col-2 d-none d-lg-table-cell">TIPO DE ATIVIDADE</th>
                        <th class="col d-none d-lg-table-cell">SEMESTRE</th>                    
                        <th class="col d-none d-lg-table-cell">H INÍCIO</th>                        
                         <th class="col d-none d-lg-table-cell">DT INÍCIO</th>                        
                        <th class="col">STATUS</th>
                        <th class="col">DETALHES</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; text-align: center;">
                     @foreach ($inscricao as $insc)
                        <tr >                       
                            <td class="d-none d-lg-table-cell" style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ $insc->idi }}</td>                            
                            <td style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ $insc->nome_completo }}</td>
                            <td style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ $insc->stsigla }}</td>
                            <td class="d-none d-lg-table-cell" style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ $insc->nmodal }}</td>
                            <td style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ $insc->nomed }}</td>
                                                      
                            <td class="d-none d-lg-table-cell" style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ $insc->trsigla }}-{{ $insc->trnome }}</td>
                            <td class="d-none d-lg-table-cell" style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ $insc->sesigla }}</td>
                            <td class="d-none d-lg-table-cell" style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ date('H:i', strtotime($insc->h_inicio)) }}</td>                           
                            <td class="d-none d-lg-table-cell" style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ date('d-m-Y', strtotime($insc->data_inicio)) }}</td>                            
                            <td style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}">{{ $insc->tipo }}</td>
                            <td style="{{ $insc->statusid == 0 ? 'background-color:#FFFF61;' : '' }}"><i class="bi bi-info-circle-fill" style="color: #0d6efd; cursor: pointer; font-size: 1.4rem;" data-bs-toggle="tooltip" title=" CPF: {{ $insc->cpf }} | Grupo: {{ $insc->nomeg }} | Sala: {{ $insc->numero }} | Atividade: {{ $insc->trsigla }} - {{ $insc->trnome }} | Observação:{{ $insc->descricao}} | Sala: {{ $insc->numero }} | Hora fim:{{ date('H:i', strtotime($insc->h_fim)) }} | Data fim:{{ $insc->data_fim ? date('d-m-Y', strtotime($reuni->data_fim)) : '-' }}"></i> 
                            </td>
                            <td >
                                @if ($insc->statusid == 3)
                                <button type="button" class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Ativar</span>
                                    <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i></button>
                                <button type="button" class="btn btn-outline-warning btn-sm tooltips" disabled>
                                    <span class="tooltiptext">Trocar</span>
                                    <i class="bi bi-arrow-left-right" style="font-size: 1rem; color:#000;"></i></button>
                                 <button type="button" class="btn btn-outline-danger btn-sm tooltips" disabled>
                                    <span class="tooltiptext">Inativar</span>
                                    <i class="bi bi-ban" style="font-size: 1rem; color:#000;"></i></button>
                                <button type="button" class="btn btn-outline-danger btn-sm tooltips" disabled>
                                    <span class="tooltiptext">Excluir</span>
                                    <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i></button>
                                @else
                                <button type="button" class="btn btn-outline-warning btn-sm tooltips"  disabled>
                                    <span class="tooltiptext" >Ativar</span>
                                    <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i></button>
                                 <a href="/altera-turma/{{ $insc->idi }}/{{ $insc->idc }}"><button type="button" class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Trocar</span>
                                    <i class="bi bi-arrow-left-right" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="#" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#modali{{ $insc->idi }}"  data-tt="tooltip" data-placement="top"><span class="tooltiptext">Inativar</span>
                                <i class="bi bi-ban" style="font-size: 1rem; color:#000;"></i>
                                </a>
                               {{--inicio modal inativação --}}
                                <div class="modal fade" id="modali{{ $insc->idi }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color:#DC4C64">
                                                <h5 class="modal-title" id="exampleModalLabel" style="color:white">Inativar a inscrição</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>                                             
                                            <div class="modal-body" style="text-align: center; ">
                                                Tem certeza que deseja inativar a inscrição de<br /><span style="color:#DC4C64; font-weight: bold;">{{ $insc->nome_completo }}</span>&#63;
                                            </div>
                                            <center>
                                            <form action="/inativar-inscricao/{{ $insc->idi }}" method="POST">
                                                @csrf
                                                <div class="mb-2 col-10">
                                                    <label class="col-form-label">Insira o motivo da
                                                        <span style="color:#DC4C64">inativação:</span></label>
                                                    <select class="form-select teste1"
                                                        name="motivo_inat" required>
                                                        @foreach ($motivo as $motivos)
                                                            <option value="{{ $motivos->id }}">
                                                                {{ $motivos->motivo }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </center>
                                            <div class="modal-footer mt-3">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Confirmar</button>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--fim modal inativação --}}
                                
                                 <a href="#" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#modale{{ $insc->idi }}"  data-tt="tooltip" data-placement="top"><span class="tooltiptext">Excluir</span>
                                <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                 {{--inicio modal exclusão --}}
                                <div class="modal fade" id="modale{{ $insc->idi }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color:#DC4C64">
                                                <h5 class="modal-title" id="exampleModalLabel" style="color:white">Excluir a inscrição</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body" style="text-align: center; ">
                                                Tem certeza que deseja excluir a inscrição de<br /><span style="color:#DC4C64; font-weight: bold;">{{ $insc->nome_completo }}</span>&#63;
                                            </div>
                                            <div class="modal-footer mt-3">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                                <a type="button" class="btn btn-primary" href="/excluir-inscricao/{{ $insc->idi }}">Confirmar</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             {{--fim modal exclusão --}}
                                @endif                               
                                <a href="/visualizar-inscricao/{{ $insc->idi }}"><button type="button" class="btn btn-outline-info btn-sm tooltips">
                                    <span class="tooltiptext">Visualizar</span>
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>                                                          
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div div style="margin-right: 10px; margin-left: 10px">
                {{ $inscricao->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

    <script>
        $(document).ready(function() {
            if (typeof {{ $semana }} === 'undefined') { //Deixa o select status como padrao vazio
                $(".semana").prop("selectedIndex", -1);
            }

            if (typeof {{ $status }} === 'undefined') { //Deixa o select status como padrao vazio
                $(".status").prop("selectedIndex", -1);
            }
        })
    </script>

    <script>
        // Quando QUALQUER modal abrir
        $(document).on('shown.bs.modal', function (e) {
            let modal = $(e.target); // modal que foi aberto

            modal.find('select.select2').select2({
                theme: 'bootstrap-5',
                dropdownParent: modal, // garante que o dropdown fique dentro do modal aberto
                width: '100%'
            });
        });
    </script>




@endsection

