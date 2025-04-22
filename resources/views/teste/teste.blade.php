@extends('layouts.app')

@section('title')
    Currículo de Membro
@endsection

@section('content')
    <?php $hello = 1; ?>


    <div class="container">
        <div class="justify-content-center">
            <br />
            <div class="progress mb-2" role="progressbar" aria-label="Animated striped 1px example" aria-valuenow="75"
                aria-valuemin="0" aria-valuemax="100" style="height: 10px">
                <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" id="prog"></div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="mb-0">DADOS PESSOAIS</span>

                </div>
                <form action="">
                    <div class="card-body" id="cardBody">
                    </div>
                </form>
            </div>
        </div>



        <div class="row justify-content-around">



            <div class=" col mt-5">
                <div class="card" style="width: 18rem;">
                    <div class="card-body ">
                        <h5 class="card-title">Progress Bar</h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary" id="valor"></h6>
                        <p class="card-text">Use os botões abaixo para adicionar ou diminuir a barra</p>
                        <div class="row">
                            <div class="col">
                                <button type="button" id="mais" class="btn btn-primary col-12">+</button>
                            </div>
                            <div class="col">
                                <button type="button" id="menos" class="btn btn-primary col-12">-</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class=" col mt-5">
                <div class="card" style="width: 18rem;">
                    <div class="card-body ">
                        <h5 class="card-title">Exemplo 1</h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Inputs e Selects Vazios</h6>
                        <p class="card-text">Carrega inputs e selects vazios no card sem recarregar a página</p>
                        <div class="row">
                            <div class="col">
                                <button type="button" id="carregamento1" class="btn btn-primary col-12">Carregar <i
                                        class="bi bi-cloud-arrow-up-fill"></i></button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div class="col mt-5">
                <div class="card" style="width: 18rem;">
                    <div class="card-body ">
                        <h5 class="card-title">Exemplo 2</h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Inputs e Selects com dados</h6>
                        <p class="card-text">Carrega inputs e selects com dados do banco</p>
                        <div class="row">
                            <div class="col">
                                <button type="button" id="carregamentoInput" class="btn btn-primary col-12">Carregar <i
                                        class="bi bi-cloud-arrow-up-fill"></i></button>
                            </div>
                            <div class="col">
                                <button type="button" id="salvar" class="btn btn-primary col-12">Salvar <i
                                        class="bi bi-cloud-arrow-down-fill"></i></button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col mt-5">
                <div class="card" style="width: 18rem;">
                    <div class="card-body ">
                        <h5 class="card-title">Exemplo 3</h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Inputs e Selects com dados</h6>
                        <p class="card-text">Carrega inputs e selects com mascaramento</p>
                        <div class="row">
                            <div class="col">
                                <button type="button" id="carregamentoInput2" class="btn btn-primary col-12">Carregar <i
                                        class="bi bi-cloud-arrow-up-fill"></i></button>
                            </div>
                            <div class="col">
                                <button type="button" id="salvar" class="btn btn-primary col-12">Salvar <i
                                        class="bi bi-cloud-arrow-down-fill"></i></button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div class="col mt-5">
                <div class="card" style="width: 18rem;">
                    <div class="card-body ">
                        <h5 class="card-title">Visualizar</h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Dados Salvos</h6>
                        <p class="card-text">Mostra todos os dados salvos durante o uso</p>
                        <div class="row">
                            <div class="col">
                                <button type="button" id="visualizar" class="btn btn-primary col-12">Visualizar <i
                                        class="bi bi-eye-fill"></i></button>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="visu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:rgb(39, 91, 189);color:white">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Informações Inseridas</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                            <div class="modal-body" id="visualizarModal"></div>


                    </div>
                </div>
            </div>



        </div>
    </div>


    <script>
        $(document).ready(function() {

            let valor = 0
            let dadosSelect = {}

            // "Pai" escuta o evento
            $('#salvar').on('mensagemEnviada', function(evento, mensagem) {
                console.log(mensagem);
            });

            function carregar1() {

                $('#cardBody').load('/teste-1');
            }

            function carregarInput() {

                $('#cardBody').load('/teste-input');
            }
            function carregarInput2() {

                $('#cardBody').load('/teste-input2');
            }


            function valorF() {
                $('#valor').html(valor);
                $('#prog').css('width', valor + '%');

                if (valor < 50) {
                    $('#prog').addClass('bg-danger');
                    $('#prog').removeClass('bg-warning');
                    $('#prog').removeClass('bg-success');
                } else if (valor > 40 && valor < 90) {
                    $('#prog').addClass('bg-warning');
                    $('#prog').removeClass('bg-danger');
                    $('#prog').removeClass('bg-success');
                } else if (valor > 80) {
                    $('#prog').addClass('bg-success');
                    $('#prog').removeClass('bg-danger');
                    $('#prog').removeClass('bg-warning');
                }
            }

            function preencheModal() {

                $('#visualizarModal').html('')
                let lixo = ['key', 'getItem', 'setItem', 'removeItem', 'clear', 'length']

                
                $.each(localStorage, function(key, value) {
                    
                    
                    let append = ''
                    if (!lixo.includes(key)) {
                        
                         append = '<div class="card"><div class="card-body"><h5 class="card-title">' + key + '</h5><p>';
                        

                        
                        let form = JSON.parse(value).form
                        let select = JSON.parse(value).select


                        $.each(form, function(k, v) {

                            if (v.name != '_token') {
                           

                                if (Object.keys(select).includes(v.name)) {
                                   append +=  v.name.charAt(0).toUpperCase() + v.name.slice(1) + ': ' + select[v.name] + '<br />'
                                } else {
                                    append +=  v.name.charAt(0).toUpperCase() + v.name.slice(1) + ': ' + v.value + '<br />'

                                }

                            }



                        })

                        $('#visualizarModal').append(append)
                    }
                })



            }

            function modalVazio() {

                $('#visualizarModal').html(
                    '<center><p class="mt-5 mb-5" style="content-align:center">Salve para poder visualizar! <i class="bi bi-cloud-arrow-down-fill"></p></center>'
                )

            }



            $('#mais').click(function() {
                if (valor < 100) {
                    valor += 10;
                }
                valorF();
            })
            $('#menos').click(function() {
                if (valor > 0) {
                    valor -= 10;
                }
                valorF();

            })

            $('#carregamento1').click(() => {
                carregar1()
            })
            $('#carregamentoInput').click(() => {
                carregarInput()
            })
            $('#carregamentoInput2').click(() => {
                carregarInput2()
            })
            $('#visualizar').click(() => {
                preencheModal();
                $('#visualizarModal').html() === "" ? modalVazio() : null
                $('#visu').modal('show')
            })


            $('#salvar').click(() => {

                localStorage.clear()
                $('#cardBody').trigger('teste123');


                $('#cardBody').on('teste1234', function(event, param1) {
                    dadosSelect = JSON.parse(param1)

                    localStorage.setItem("Exemplo 2", JSON.stringify({
                        'form': $('form').serializeArray(),
                        'select': dadosSelect
                    }));

                });




            })

            valorF();

        });
    </script>
@endsection
