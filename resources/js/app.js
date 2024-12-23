import '@fortawesome/fontawesome-free/css/all.min.css';

import './bootstrap';

import '../sass/app.scss';

import './custom';

import 'select2/dist/css/select2.min.css';

import 'jquery/dist/jquery.js';

import 'bootstrap5-toggle/js/bootstrap5-toggle.ecmas.min.js';

import $ from 'jquery';



window.$ = $;

 window.jQuery = $;



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


