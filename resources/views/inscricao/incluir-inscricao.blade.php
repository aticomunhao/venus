@extends('layouts.app')

@section('title')
    Incluir inscrição
@endsection

@section('content')

   <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-12">
                <h4 class="card-title" style="font-size:20px; text-align:left; color:gray; font-family:calibri">
                INCLUIR INSCRIÇÃO
                </h4>
                <form autocomplete="on" class="form-horizontal mt-4" method="POST" action="/incluir-inscricao">
                    @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                FILTRAR PESSOA
                            </div>
                        </div>
                    </div>                    
                    <div class="card-body">                           
                        <div class="row justify-content-center">
                            <div class="col">                                          
                                <label for="id_associado" class="form-label">Nome:
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span></label>
                                <select class="form-select select2" aria-label=".form-select-lg example"
                                    name="id_pessoa">
                                    @foreach ($pessoa as $pessoas)
                                        <option value="{{ $pessoas->id }}">{{ $pessoas->nome_completo }} - {{ $pessoas->cpf}}</option>
                                    @endforeach
                                </select>
                            </div>                         
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                OPÇÕES DE CURSO
                            </div>
                        </div>
                    </div>                    
                    <div class="card-body">
                        <div class="row justify-content-center">
                      @foreach(collect($turma)->groupBy('nomem') as $modalidade => $gruposPorModalidade)
    {{-- Accordion: Modalidade --}}
    <div class="accordion mb-3" id="accordionModalidade{{ Str::slug($modalidade) }}">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingModalidade{{ Str::slug($modalidade) }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseModalidade{{ Str::slug($modalidade) }}"
                        aria-expanded="false" aria-controls="collapseModalidade{{ Str::slug($modalidade) }}">
                    {{ strtoupper($modalidade) }}
                </button>
            </h2>
            <div id="collapseModalidade{{ Str::slug($modalidade) }}" class="accordion-collapse collapse"
                aria-labelledby="headingModalidade{{ Str::slug($modalidade) }}"
                data-bs-parent="#accordionModalidade{{ Str::slug($modalidade) }}">
                <div class="accordion-body">

                    {{-- Accordion de Dias da Semana --}}
                    <div class="accordion accordion-flush" id="accordionDias{{ Str::slug($modalidade) }}">
                        @foreach($gruposPorModalidade->groupBy('dia_semana') as $dia => $gruposDoDia)
                            @php $diaSlug = Str::slug($modalidade.'-'.$dia); @endphp

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingDia{{ $diaSlug }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseDia{{ $diaSlug }}"
                                            aria-expanded="false" aria-controls="collapseDia{{ $diaSlug }}">
                                        {{ mb_convert_case($dia, MB_CASE_TITLE, 'UTF-8') }}
                                    </button>
                                </h2>
                                <div id="collapseDia{{ $diaSlug }}"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="headingDia{{ $diaSlug }}"
                                    data-bs-parent="#accordionDias{{ Str::slug($modalidade) }}">
                                    <div class="accordion-body">

                                        {{-- Agora dentro do DIA vem os tratamentos --}}
                                        <div class="accordion accordion-flush" id="accordionTratamentos{{ $diaSlug }}">
                                            @foreach($gruposDoDia->groupBy('id_tratamento') as $idTipoTratamento => $grupoTratamento)
                                                @php
                                                    $primeiraTurma = $grupoTratamento->first();
                                                    $nomeTratamento = $primeiraTurma->desct ?? 'N/A';
                                                    $semestre = $primeiraTurma->siglas ?? 'S/Sem';
                                                    $vagasTotais = array_sum(array_column($grupoTratamento->toArray(), 'vaga'));
                                                    $maxAtend = array_sum(array_column($grupoTratamento->toArray(), 'max_atend'));
                                                    $tratamentoIdSlug = Str::slug($diaSlug.'-'.$idTipoTratamento);
                                                @endphp

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingTratamento{{ $tratamentoIdSlug }}">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapseTratamento{{ $tratamentoIdSlug }}"
                                                                aria-expanded="false" aria-controls="collapseTratamento{{ $tratamentoIdSlug }}">
                                                            {{ $nomeTratamento }} - {{ $semestre }} - 
                                                            @if($maxAtend > 0)
                                                                <span style="color:green"> Vagas: {{ $vagasTotais }}</span>
                                                            @else
                                                                <span style="color:red"> Vagas: {{ $vagasTotais }}</span>
                                                            @endif
                                                        </button>
                                                    </h2>
                                                    <div id="collapseTratamento{{ $tratamentoIdSlug }}"
                                                        class="accordion-collapse collapse"
                                                        aria-labelledby="headingTratamento{{ $tratamentoIdSlug }}"
                                                        data-bs-parent="#accordionTratamentos{{ $diaSlug }}">
                                                        <div class="accordion-body">

                                                            {{-- Agora sim os grupos --}}
                                                            @foreach($grupoTratamento as $linha)
                                                                <table class="table table-sm table-bordered table-striped mb-2">
                                                                    <thead style="text-align:center; background: #daffe0;">
                                                                        <tr style="font-weight: bold; font-size:13px">
                                                                            <th class="d-none d-lg-table-cell">GRUPO</th>
                                                                            <th>SALA</th>
                                                                            <th class="d-none d-lg-table-cell">ATIVIDADE</th>
                                                                            <th>H INÍCIO</th>
                                                                            <th>H FIM</th>
                                                                            <th>VAGAS PREV</th>
                                                                            <th>VAGAS DISP</th>
                                                                            <th>MARCAR</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr style="text-align:center; font-size:14px">
                                                                            <td class="d-none d-lg-table-cell">{{ $linha->nomeg }}</td>
                                                                            <td>{{ $linha->sala }}</td>
                                                                            <td class="d-none d-lg-table-cell">{{ $linha->desct }} - {{ $linha->siglas }}</td>
                                                                            <td>{{ date('H:i', strtotime($linha->h_inicio)) }}</td>
                                                                            <td>{{ date('H:i', strtotime($linha->h_fim)) }}</td>
                                                                            <td>{{ $linha->max_atend }}</td>
                                                                            <td>{{ $linha->vaga }}</td>
                                                                            <td>
                                                                                <center>
                                                                                    <input type="radio" class="form-check" name="curso" value="{{ $linha->idt }}" required>
                                                                                </center>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            @endforeach

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        {{-- Fim accordion Tratamentos --}}

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Fim accordion Dias --}}
                    
                </div>
            </div>
        </div>
    </div>
@endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>      
        <div class="row mt-4 justify-content-center">
            <div class="col-lg-3 col-6 d-flex justify-content-center align-items-center">
                <a class="btn btn-danger w-100" href="/gerenciar-inscricao" class="btn btn-danger">Cancelar</a>
            </div>
            <div class="col-lg-3 col-6 d-flex justify-content-center align-items-center">
                <button type="submit" class="btn btn-primary w-100">Confirmar</button>
            </div>
        </form>
        </div>
    </div>
     
        
@endsection
