@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                CADASTRAR GRUPOS
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="container-fluid">
                            <form class="form-horizontal mt-2" method="post" action="/incluir-grupos">
                                @csrf

                                <div class="row">

                                    <div class="col-6">
                                        Nome
                                        <input type="text" class="form-control" id="nome" name="nome" maxlength="30" required="required" oninput="validarCaracteresEspeciais(this)">

                                    </div>

                                    <script>
                                        function validarCaracteresEspeciais(input) {
                                            // Permite letras, espaços e caracteres especiais
                                            input.value = input.value.replace(/[^a-zA-Z\u00C0-\u00FF\s]/g, '');
                                        }
                                    </script>



                                    <!DOCTYPE html>
                                    <html lang="en">
                                    <head>
                                        <meta charset="UTF-8">
                                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                        <title>Exemplo</title>
                                        <!-- Inclua o CDN do jQuery -->
                                        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                                    </head>
                                    <body>
                                        <div class="col">
                                            Status
                                            <select class="form-select" aria-label=".form-select-lg example" name="status_grupo" id="status_grupo" required="required">
                                                <option value="1">Ativo</option>
                                                <option value="2">Inativo</option>
                                                <option value="3">Experimental</option>
                                            </select>
                                        </div>

                                        <br>

                                        <div class="col">
                                            Motivo
                                            <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_motivo" id="tipo_motivo" disabled>
                                                <option value=""> </option>
                                                @foreach ($tipo_motivo as $tipo_motivos)
                                                    <option value="{{ $tipo_motivos->id }}"> {{ $tipo_motivos->tipo }} </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </body>
                                    </html>

                                    <div class="row">
                                        <div class="col">
                                            <br>
                                            Tipo grupo
                                            <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_grupo" required="required">
                                                @foreach ($tipo_grupo as $item)
                                                    <option value="{{ $item->idg }}">{{ $item->nm_tipo_grupo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <br>
                                            Tipo de tratamento
                                            <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_tratamento" required="required">
                                                @foreach ($tipo_tratamento as $tipo)
                                                    <option value="{{ $tipo->id }}"> {{ $tipo->descricao }} </option>
                                                    @endforeach
                                                </select>
                                            </div>




                                    <br>

                                    <div class="row justify-content-center">
                                        <div class="d-grid gap-1 col-4 mx-auto">
                                            <br>
                                            <a class="btn btn-danger" href="/gerenciar-grupos" role="button">Cancelar</a>
                                        </div>
                                        <div class="d-grid gap-2 col-4 mx-auto">
                                            <br>
                                            <button class="btn btn-primary">Confirmar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            // Adiciona um ouvinte de evento para o campo "Status"
            $('#status_grupo').change(function () {
                // Obtém o valor selecionado no campo "Status"
                var selectedStatus = $(this).val();

                // Habilita ou desabilita o campo "Motivo" com base na seleção
                if (selectedStatus === '2') {
                    $('#tipo_motivo').prop('disabled', false);
                } else {
                    $('#tipo_motivo').prop('disabled', true);
                    $('#tipo_motivo').val(''); // Limpa a seleção quando desabilitado
                }
            });
        });
    </script>
<script>

        const status_grupo = document.getElementById('status_grupo');
        const id_tipo_motivo = document.getElementById('id_tipo_grupo');

        // Adiciona um ouvinte de eventos para o campo1
        status_grupo.addEventListener('input', function() {
            // Se status_grupo estiver preenchido, desabilita o id_tipo_motivo
            if (status_grupo.value.trim() !== '') {
                id_tipo_motivo.disabled = true;
            } else {
                id_tipo_motivo.disabled = false;
            }
        });

        // Adiciona um ouvinte de eventos para o campo2
        id_tipo_motivo.addEventListener('input', function() {
            // Se id_tipo_motivo estiver preenchido, desabilita o status_grupo
            if (id_tipo_motivo.value.trim() !== '') {
                status_grupo.disabled = true;
            } else {
                status_grupo.disabled = false;
            }
        });

 </script>



    @endsection

    @section('footerScript')

    <script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="caminho/para/jquery.min.js"></script>


    @endsection

