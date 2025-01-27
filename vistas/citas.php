<?php 
session_start(); 
include "../php/funtions.php"; 

//CONEXION A DB
$mysqli = connect_mysqli();

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}     

$_SESSION['menu'] = "Citas";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];    

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s");
$comentario = mb_convert_case("Ingreso al Menu Citas", MB_CASE_TITLE, "UTF-8");   

if($colaborador_id != "" || $colaborador_id != null){
   historial_acceso($comentario, $nombre_host, $colaborador_id);     
} 

$fecha = date("Y-m-d");
$mes=nombremes(date("m", strtotime($fecha)));
$año=date("Y", strtotime($fecha));  
?>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Citas :: <?php echo SERVEREMPRESA;?></title>
	<link href="<?php echo SERVERURL; ?>/css/estilo-paginacion.css" rel="stylesheet">	
    <link href="<?php echo SERVERURL; ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>
	<link href="<?php echo SERVERURL; ?>bootstrap/css/bootstrap-select.min.css" rel="stylesheet" crossorigin="anonymous"/>
    <link href="<?php echo SERVERURL; ?>bootstrap/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous"/>
    <link href="<?php echo SERVERURL; ?>fontawesome/css/all.css" rel="stylesheet">	
    <link href="<?php echo SERVERURL; ?>css/style.css" rel="stylesheet"/>   
    <link rel="shortcut icon" href="<?php echo SERVERURL; ?>img/cami.png">
    <link href="<?php echo SERVERURL; ?>sweetalert/sweetalert.css" rel="stylesheet" crossorigin="anonymous"/>

	<!-- FullCalendar -->
	<link href='<?php echo SERVERURL; ?>css/fullcalendar.css' rel='stylesheet' />		
</head>
<body>
<?php include("templates/menu.php"); ?>
<?php include("templates/modals.php"); ?> 
<?php include("modals/modals_citas.php");?>
<?php include("modals/modals.php");?> 
<!-- Page Content -->
<div class="container-fluid">
<br><br><br>
		<ol class="breadcrumb mt-2 mb-4">
			<li class="breadcrumb-item"><a class="breadcrumb-link" href="inicio.php">Dashboard</a></li>
			<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Citas</li>
		</ol>	
		<form class="form-inline" id="botones_citas">
			<div class="form-group mx-sm-3 mb-1">
				<div class="input-group">				
					<div class="input-group-append">				
						<span class="input-group-text"><div class="sb-nav-link-icon"></div>Consultorio</span>
					</div>
					<select id="servicio" name="servicio" class="selectpicker" title="Consultorio" data-live-search="true" data-size="7">
					</select>
				</div>
			</div>	
			<div class="form-group mx-sm-3 mb-1">
				<div class="input-group">				
					<div class="input-group-append">				
						<span class="input-group-text"><div class="sb-nav-link-icon"></div>Profesional</span>
					</div>
					<select id="medico_general" name="medico_general" class="selectpicker" title="Profesional" data-live-search="true" data-size="7">
					</select>
				</div>
			</div>		  
		  <div class="form-group mr-1">	
			<button class="btn btn-primary" type="submit" id="refresh"><div class="sb-nav-link-icon"></div><i class="fas fa-sync-alt"></i> Actualizar</button>	  
		  </div>  
		  <div class="form-group mr-1">
			<div class="dropdown show">
			  <a class="btn btn-info dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				 Ausencias
			  </a>
			  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
				<a class="dropdown-item" id="ausencias" name="ausencias" href="#">Ausencias</a>
			  </div>
			</div>		  
		  </div> 
		  <div class="form-group mr-1">
			<button class="btn btn-success" type="submit" id="search"><div class="sb-nav-link-icon"></div><i class="fas fa-search"></i> Buscar</button>
		  </div> 		  
		  <div class="form-group mr-1">
			<div class="dropdown show">
			  <a class="btn btn-danger dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				 <i class="fas fa-search"></i> Historial
			  </a>
			  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
				<a class="dropdown-item" id="historial" name="historial" href="#">Historial de Citas</a>
				<a class="dropdown-item" id="historial_nopresento" name="historial_nopresento" href="#">Historial Ausencias</a>
				<a class="dropdown-item" id="historial_reprogramaciones" name="historial_reprogramaciones" href="#">Reprogramaciones</a>			
			  </div>
			</div>		  
		  </div> 	   
		</form>
		
		<div class="col-sm-12">
			<div id="calendar" class="col-centered" style="position: relative; z-index:0;"></div>
		</div>
		<div class="col-sm-12">
			<br/><br/><br/>
		</div>		
		
		<?php include("templates/footer.php"); ?>
</div>
<!-- /.container -->

<!--Librerias Java Script-->
<script src="<?php echo SERVERURL; ?>js/query/jquery.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/popper.min.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/bootstrap-select.min.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>js/main.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>sweetalert/sweetalert.min.js" crossorigin="anonymous"></script>

<script src="<?php echo SERVERURL; ?>bootstrap/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/dataTables.buttons.min.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/jszip.min.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/pdfmake.min.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/vfs_fonts.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/buttons.html5.min.js" crossorigin="anonymous"></script>
<script src="<?php echo SERVERURL; ?>bootstrap/js/buttons.print.min.js" crossorigin="anonymous"></script>
	
<script src='<?php echo SERVERURL; ?>js/query/moment.min.js'></script>	
<script src='<?php echo SERVERURL; ?>/js/query/fullcalendar.min.js'></script>
<script src="<?php echo SERVERURL; ?>js/query/menu-despelgable.js"></script>
<script src="<?php echo SERVERURL; ?>js/query/arriba.js"></script>   

<?php 	
	include "../js/main.php"; 
	include "../js/myjava_citas.php"; 	
	include "../js/select.php"; 	
	include "../js/functions.php"; 
	include "../js/myjava_cambiar_pass.php"; 		
?>

<span class="ir-arriba" data-toggle="tooltip" data-placement="top" title="Ir Arriba"><i class="fas fa-chevron-up fa-xs"></i></span> 

</body>
</html>