@extends('layouts.app')
@section('title')
    Relatório Presença AFI
@endsection
@section('content')
<center>
  
        <canvas id="myChart"></canvas>
      
   </center>





<script>
  


const ctx = document.getElementById('myChart');


new Chart(ctx, {
    type: 'bar',
    data: {
                labels: [
                    'Mediunidade aflorada',
                    'Influenciação espiritual',
                    'Obsessão',
                    'Conjugal',
                    'Familiar',
                    'Social',
                    'Profissional',
                    'Saúde',
                    'Psiquiátrica diagnosticada',
                    'Sexualidade',
                    'Desânimo / Tristeza / Solidão',
                    'Ansiedade / Depressão',
                    'Dependência química',
                    'Estresse',
                    'Aborto',
                    'Suicídio',
                    'Interesse pela Doutrina',
                    'Sonhos',
                    'Medo de espíritos',
                    'Dificuldades profissionais',
                    'Desencarne de ente querido',
                ],
                datasets: [
    {
      label: 'Dataset 1',
      data: [10,2, 10, 12,1,0,5,10,6,7],
    
      stack: 'Stack 0',
    },
    {
      label: 'Dataset 2',
      data: [10,2, 10, 12,1,0,5,10,6,7],
     
      stack: 'Stack 0',
    },
    {
      label: 15,
      data: [10,2, 10, 12,1,0,5,10,6,7],
      
      stack: 'Stack 1',
    },]
            },
});
    </script>

   @endsection