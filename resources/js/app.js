
import './bootstrap';

import $ from 'jquery';

import '../sass/app.scss';

import 'select2';

import './custom';

window.$ = window.jQuery = $;



import 'bootstrap5-toggle/js/bootstrap5-toggle.ecmas.min.js';
$(document).ready(function() {
    $('#assist').prop('selectedIndex', -1);
    $('#repres').prop('selectedIndex', -1);
    $('#parent').prop('selectedIndex', -1);

    function ajaxAssistido() {
        var nome = $('#cpfAssistido').val();
        $('#cpfAssistido').removeClass('is-invalid');
        $('#labelNumeroCpfAssistido').prop('hidden', true);
        $('#labelCpfAssistido').prop('hidden', true);
        $('#assist').html('');
        $('#assist').prop('selectedIndex', -1);
        
        if (nome.length < 1) {
            $('#cpfAssistido').addClass('is-invalid');
            $('#labelNumeroCpfAssistido').prop('hidden', false);
        } else {
            $.ajax({
                type: "GET",
                url: "/ajaxCRUD?nome=" + nome,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if(response.length === 0) {
                        $('#cpfAssistido').addClass('is-invalid');
                        $('#labelCpfAssistido').prop('hidden', false);
                    } else {
                        $.each(response, function() {
                            $('#assist').append('<option value="' + this.id + '">' + this.nome_completo + '</option>');
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    $('#cpfAssistido').addClass('is-invalid');
                    $('#labelCpfAssistido').prop('hidden', false);
                }
            });
        }
    }

    function ajaxResponsavel() {
        var nome = $('#cpfResponsavel').val();
        $('#cpfResponsavel').removeClass('is-invalid');
        $('#labelNumeroCpfResponsavel').prop('hidden', true);
        $('#labelCpfResponsavel').prop('hidden', true);
        $('#repres').html('');
        $('#repres').prop('selectedIndex', -1);
        $('#parent').prop('selectedIndex', -1);
        
        if (nome.length < 1) {
            $('#cpfResponsavel').addClass('is-invalid');
            $('#labelNumeroCpfResponsavel').prop('hidden', false);
        } else {
            $.ajax({
                type: "GET",
                url: "/ajaxCRUD?nome=" + nome,
                dataType: "json",
                success: function(response) {
                    if(response.length === 0) {
                        $('#cpfResponsavel').addClass('is-invalid');
                        $('#labelCpfResponsavel').prop('hidden', false);
                    } else {
                        $.each(response, function() {
                            $('#repres').append('<option value="' + this.id + '">' + this.nome_completo + '</option>');
                        });
                    }
                },
                error: function(xhr) {
                    $('#cpfResponsavel').addClass('is-invalid');
                    $('#labelCpfResponsavel').prop('hidden', false);
                }
            });
        }
    }

    $('#bCpfAssistido').click(function() {
        ajaxAssistido();
    });

    $('#bCpfResponsavel').click(function() {
        ajaxResponsavel();
    });

    $('.checkboxes').change(function() {
        if($('#representante').prop('checked')) {
            $('#represent').prop('hidden', false);
        } else {
            $('#represent').prop('hidden', true);
        }
        if($('#pEspecial').prop('checked')) {
            $('#pedidoEspecial').prop('hidden', false);
        } else {
            $('#pedidoEspecial').prop('hidden', true);
        }
    });

    $('.pedido').change(function() {
        if($('#tipo_afi').prop('selectedIndex') !== 0) {
            $('#afi_p').prop('disabled', true);
            $('#afi_p').prop('selectedIndex', 0);
        } else {
            $('#afi_p').prop('disabled', false);
        }

        if($('#afi_p').prop('selectedIndex') !== 0) {
            $('#tipo_afi').prop('disabled', true);
            $('#tipo_afi').prop('selectedIndex', 0);
        } else {
            $('#tipo_afi').prop('disabled', false);
        }
    });
});



//Cadastrar mediunidade

$(document).ready(function() {
    $('.data_manifestou')
        .hide()
        .find('input[type=date]')
        .prop('required', false);

    $('[name^=id_tp_mediunidade]').change(function() {
        $('.data_manifestou')
            .hide()
            .find('input[type=date]')
            .prop('required', false);

        $('[name^=id_tp_mediunidade]:checked').each(function() {
            var tipoId = $(this).val();
            $('#data_inicio_' + tipoId)
                .show()
                .find('input[type=date]')
                .prop('required', true);
        });
    });
})

//Editar mediunidade
$(document).ready(function() {
    $('.data_manifestou')
        .hide()
        .find('input[type=date]')
        .prop('required', false);

    $('[name^=id_tp_mediunidade]').change(function() {
        $('.data_manifestou')
            .hide()
            .find('input[type=date]')
            .prop('required', false);

        $('[name^=id_tp_mediunidade]:checked').each(function() {
            var tipoId = $(this).val();
            $('#data_inicio_' + tipoId)
                .show()
                .find('input[type=date]')
                .prop('required', true);
        });
    });
    $('[name^=id_tp_mediunidade]').change();
});



 


 
