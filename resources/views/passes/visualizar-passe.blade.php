@extends('layouts.app')
@php use Carbon\Carbon; @endphp
@section('title', 'Visualizar Passes')
@section('content')
<button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
    <i class="bi bi-arrow-up"></i>
</button>
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                {{ $cronograma->nome }} -{{ $cronograma->setor }}- {{ $cronograma->dia }} - {{ $cronograma->h_inicio }} - {{ $cronograma->h_fim }}
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card-body">
                        <div class="accordion" id="accordionExample">
                            @foreach ($dias_cronograma as $index => $cronograma_dia)
                                @if ($cronograma_dia->nr_acompanhantes > 0) <!-- Filtrando datas com acompanhantes -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $index }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                                {{ Carbon::parse($cronograma_dia->data)->format('d/m/Y') }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <p>Número de Passes: {{ $cronograma_dia->nr_acompanhantes }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="row justify-content-center mt-3">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-passe" role="button">Fechar</a>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        #btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 10px;
            display: none;
            z-index: 100;
        }
    </style>
    <script>
        //Get the button
        let mybutton = document.getElementById("btn-back-to-top");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            if (
                document.body.scrollTop > 20 ||
                document.documentElement.scrollTop > 20
            ) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }
        // When the user clicks on the button, scroll to the top of the document
        mybutton.addEventListener("click", backToTop);

        function backToTop() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
@endsection

@section('footerScript')

@endsection
