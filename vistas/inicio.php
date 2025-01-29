<?php
session_start(); 
include('../php/funtions.php');

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}    

/*
if($_SESSION['type'] !=1 || $_SESSION['type'] !=2 ){
	header('Location: '.SERVERURL.'vistas/pacientes.php');
}*/

$_SESSION['menu'] = "Dashboard";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = getRealIP();		
$fecha = date("Y-m-d H:i:s"); 
$comentario = mb_convert_case("Ingreso al Modulo de Dashboard", MB_CASE_TITLE, "UTF-8");   

if($colaborador_id != "" || $colaborador_id != null){
   historial_acceso($comentario, $nombre_host, $colaborador_id);  
}      
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="author" content="Script Tutorials" />
    <meta name="description" content="Responsive Websites Orden Hospitalaria de San Juan de Dios">
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard :: <?php echo SERVEREMPRESA;?></title>
	<?php include("script_css.php"); ?>  
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->  
  <?php include("templates/modals.php"); ?>

<!--FIN VENTANAS MODALES-->	

<?php include("templates/menu.php"); ?> 

<br><br><br>
  <div class="container-fluid">
  	<ol class="breadcrumb mt-2 mb-4">
		<li class="breadcrumb-item"><a class="breadcrumb-link" href="inicio.php">Dashboard</a></li>
	</ol>

	<!--INICIO CARDS-->	
	<div class="row">
		<div class="col-md-3 col-xl-3">
			<a href="pacientes.php" data-toggle="tooltip" data-placement="top" title="Los usuarios temporales son todos aquellos que no se han presentado a su cita presencial en el Hospital">
				<div class="stati card bg-c-blue order-card">
					<div class="card-block">
						<h6 class="m-b-20">Total Usuarios</h6>
						<h2 class="text-right"><i class="fas fa-users f-left"></i><span id="main_temporales"></span></h2>
						<p class="m-b-0">Temporales <span class="f-right"></span></p>
					</div>
				</div>
			</a>
		</div>
		
		<div class="col-md-3 col-xl-3">
			<a href="pacientes.php" data-toggle="tooltip" data-placement="top" title="Muestra los usuarios activos del Hospital">
				<div class="stati card bg-c-green order-card">
					<div class="card-block">
						<h6 class="m-b-20">Total Usuarios</h6>
						<h2 class="text-right"><i class="fas fa-users f-left"></i><span id="main_activos"></span></h2>
						<p class="m-b-0">Activos<span class="f-right"></span></p>
					</div>
				</div>
			</a>
		</div>
		
		<div class="col-md-3 col-xl-3">
			<a href="reportes_atenciones_medicas.php" data-toggle="tooltip" data-placement="top" title="Muestra el total de Atenciones">
				<div class="stati card bg-c-yellow order-card">
					<div class="card-block">
						<h6 class="m-b-20">Total Atenciones</h6>
						<h2 class="text-right"><i class="fas fa-users f-left"></i><span id="main_atenciones"></span></h2>
						<p class="m-b-0"><?php echo nombremes(date("m")).", ".date("Y"); ?> <span class="f-right"></span></p>
					</div>
				</div>
			</a>
		</div>
        
        <div class="col-md-3 col-xl-3">
			<a href="reporte_ausencias.php" data-toggle="tooltip" data-placement="top" title="Muestra el total de Asencias">
				<div class="stati card bg-c-pink order-card">
					<div class="card-block">
						<h6 class="m-b-20">Total Usuarios</h6>
						<h2 class="text-right"><i class="fas fa-users f-left"></i><span id="main_ausencias"></span></h2>
						<p class="m-b-0">Ausencias<span class="f-right"></span></p>
					</div>
				</div>
			</a>
        </div>
	</div>
	
	<div class="row">
		<div class="col-md-3 col-xl-3">
			<a href="facturacion.php" data-toggle="tooltip" data-placement="top" title="Muestra el total de facturas de pendientes por facturar">
				<div class="stati card bg-c-yellow order-card">
					<div class="card-block">
						<h6 class="m-b-20">Facturas Pendientes</h6>
						<h2 class="text-right"><i class="fas fa-users f-left"></i><span id="main_facturas_pendientes"></span></h2>
						<p class="m-b-0"><?php echo nombremes(date("m")).", ".date("Y"); ?> <span class="f-right"></span></p>
					</div>
				</div>
			</a>
		</div>
		
		<div class="col-md-3 col-xl-3">
			<a href="atencion_medica.php" data-toggle="tooltip" data-placement="top" title="Muestra el total de atenciones pendientes de todos los servicios en el mes actual, esto es para las atenciones pendientes de los profesionales">
				<div class="stati card bg-c-pink order-card">
					<div class="card-block">
						<h6 class="m-b-20">Atenciones Pendientes</h6>
						<h2 class="text-right"><i class="fas fa-users f-left"></i><span id="main_prendiente_atenciones"></span></h2>
						<p class="m-b-0"><?php 
							echo nombremes(date("m")).", ".date("Y"); 
						?> <span class="f-right"></span></p>
					</div>
				</div>
			</a>
		</div>
		
		<div class="col-md-3 col-xl-3">
			<a href="preclinica.php" data-toggle="tooltip" data-placement="top" title="Muestra el total de atenciones pendientes de todos los servicios en el mes actual, esto es para las atenciones pendientes en el área de preclínica">
				<div class="stati card bg-c-blue order-card">
					<div class="card-block">
						<h6 class="m-b-20">Pendientes Preclínica</h6>
						<h2 class="text-right"><i class="fas fa-users f-left"></i><span id="main_pendiente_preclinica"></span></h2>
						<p class="m-b-0"><?php echo nombremes(date("m")).", ".date("Y"); ?><span class="f-right"></span></p>
					</div>
				</div>
			</a>
		</div>
		
		<div class="col-md-3 col-xl-3">
			<a href="productos.php" data-toggle="tooltip" data-placement="top" title="Muestra el total de productos y/o servicios">
				<div class="stati card bg-c-green order-card">
					<div class="card-block">
						<h6 class="m-b-20">Total Productos</h6>
						<h2 class="text-right"><i class="fas fa-users f-left"></i><span id="main_productos"></span></h2>
						<p class="m-b-0"><?php echo nombremes(date("m")).", ".date("Y"); ?> <span class="f-right"></span></p>
					</div>
				</div>
			</a>
		</div>	
	</div>	
	<!--FIN CARDS-->

	<!--INICIO GRAFICOS-->
	<div class="row">
		<div class="col-xl-6">
			<div class="stati card mb-3" data-toggle="tooltip" data-placement="top" title="Grafica de atenciones correspondientes al año anterior">
				<div class="card-header">
					<i class="fas fa-chart-bar mr-1"></i>
					Reporte de Atenciones Año: <?php echo date("Y",strtotime(date('Y-m-d')."- 1 year")); ?>
				</div>
				<canvas id="graphBarAtencionesAnoAnterior" width="100%"></canvas>
			</div>
		</div>
			
		<div class="col-xl-6">
			<div class="stati card mb-4" data-toggle="tooltip" data-placement="top" title="Grafica de atenciones correspondientes al año actual">
				<div class="card-header">
					<i class="fas fa-chart-bar mr-1"></i>
					Reporte de Atenciones Año: <?php echo date("Y"); ?>
				</div>
				<div class="card-body"><canvas id="graphBarAtencionesAnoActual" width="100%"></canvas></div>
			</div>
		</div>
	</div>	
	

	<!--FIN GRAFICOS-->	
	
	<div class="row">
		<div class="col-xl-12">
			<a href="<?php echo SERVERURL; ?>vistas/secuencia_facturacion.php" style="color: #3366BB;">
				<div class="card-header">
					<i class="fas fa-sliders-h mr-1"></i>
					Documentos Fiscales
				</div>
				<div class="card-body"> 
					<div class="table-responsive">
						<table id="dataTableSecuenciaDashboard" class="table table-striped table-condensed table-hover" style="width:100%">
							<thead>
								<tr>
									<th>Empresa</th>
									<th>Documento</th>
									<th>Rango Inicio</th>
									<th>Rango Fin</th>	
									<th>Actual</th>										
									<th>Fecha Expiración</th>					
								</tr>
							</thead>
						</table>  
					</div>                   
				</div>				
			</a>
		</div>
	</div>

	<?php include("templates/footer.php"); ?> 
 </div>

    <!-- add javascripts -->
	<?php include("script.php"); ?>

	<script src="<?php echo SERVERURL; ?>js/charts/Chart.min.js"></script>
	<script src="<?php echo SERVERURL; ?>js/charts/chartjs-plugin-datalabels@2.0.0.js"></script>
	
	<?php 			
		include "../js/functions.php"; 
		include "../js/main.php"; 
		include "../js/myjava_cambiar_pass.php";
		include "../js/charts/graphs.php"; 		
	?>	
</body>
</html>