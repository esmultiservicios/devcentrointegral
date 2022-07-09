<script>
$(document).ready(function () {
	showGraphAnteiconesAnoAnterior();
	showGraphAntecionesAnoActual();
	
	/*showGraphCEAnterior();
	showGraphCEActual();
	showGraphUNAAnterior();
	showGraphUNAActual();
	
	setInterval('showGraphCEActual()',10000);
	setInterval('showGraphUNAActual()',10000);*/
});

	
function showGraphAnteiconesAnoAnterior(){
	{		
		$.post("<?php echo SERVERURL; ?>php/main/totalAtencionesUltimosAno.php",
		function(data){
			var datos = eval(data);
			var mes = [];
			var total = [];
			
			for(var fila=0; fila < datos.length; fila++){
				mes.push(datos[fila]["mes"]);
				total.push(datos[fila]["total"]);
			}

			var ctx = document.getElementById('graphBarAtencionesAnoAnterior').getContext('2d');		
			var chart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'bar',

				// The data for our dataset
				data: {
					labels: mes,
					datasets: [{
						label: 'Reporte de Atenciones Año <?php echo date("Y",strtotime(date("Y-m-d")."- 1 year")); ?>',
						backgroundColor: '#4099ff',
						borderColor: '#4099ff',
						hoverBackgroundColor: '#73b4ff',
						hoverBorderColor: '#FAFAFA',
						borderWidth: 1,
						data: total,
						datalabels: {
							color: '#4099ff',
							anchor: 'end',
							align: 'top',
							labels: {
								title: {
									font: {
										weight: 'bold'
									}
								}
							}							
						}
					}]
				},

				// Configuration options go here
				plugins: [ChartDataLabels],
				options: {
					scales: {
						y: {
							beginAtZero: true
						}
					},
					plugins: {
						legend: {
							labels: {
								// This more specific font property overrides the global property
								font: {
									size: 12,
									weight: 'bold'
								}
							}
						}
					}
				}		
			});	
		});
	}
}

function showGraphAntecionesAnoActual(){
	{
		$.post("<?php echo SERVERURL; ?>php/main/totalAtencionesAnoActual.php",
		function(data){
			var datos = eval(data);
			var mes = [];
			var total = [];
			
			for(var fila=0; fila < datos.length; fila++){
				mes.push(datos[fila]["mes"]);
				total.push(datos[fila]["total"]);
			}

			var ctx = document.getElementById('graphBarAtencionesAnoActual').getContext('2d');		
			var chart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'bar',

				// The data for our dataset
				data: {
					labels: mes,
					datasets: [{
						label: 'Reporte de Atenciones Año <?php echo date("Y"); ?>',
						backgroundColor: '#2ed8b6',
						borderColor: '#2ed8b6',
						hoverBackgroundColor: '#59e0c5',
						hoverBorderColor: '#FAFAFA',
						borderWidth: 1,
						data: total,
						datalabels: {
							color: '#2ed8b6',
							anchor: 'end',
							align: 'top',
							labels: {
								title: {
									font: {
										weight: 'bold'
									}
								}
							}							
						}
					}]
				},

				// Configuration options go here
				plugins: [ChartDataLabels],
				options: {
					scales: {
						y: {
							beginAtZero: true
						}
					},
					plugins: {
						legend: {
							labels: {
								// This more specific font property overrides the global property
								font: {
									size: 12,
									weight: 'bold'
								}
							}
						}
					}	
				}		
			});				
		});
	}
}
</script>