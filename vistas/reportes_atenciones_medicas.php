<?php
session_start(); 
include "../php/funtions.php";

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}    

$_SESSION['menu'] = "Reporte de Atenciones Medicas";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s"); 
$comentario = mb_convert_case("Ingreso al Modulo de Atenciones Reporte de Medicas", MB_CASE_TITLE, "UTF-8");   

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
    <title>Reporte Atenciones Medicas :: <?php echo SERVEREMPRESA;?></title>
	<?php include("script_css.php"); ?>		
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->  
  <?php include("templates/modals.php"); ?>    

<!--INICIO MODAL-->

   <?php include("modals/modals.php");?>	

   <!--Fin Ventanas Modales-->
	<!--MENU-->	  
       <?php include("templates/menu.php"); ?>
    <!--FIN MENU--> 
	
<br><br><br>
<div class="container-fluid">
	<ol class="breadcrumb mt-2 mb-4">
		<li class="breadcrumb-item"><a class="breadcrumb-link" href="inicio.php">Dashboard</a></li>
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Reporte Atenciones Medicas</li>
	</ol>
	
	<div id="main_facturacion">
    <form class="form-inline" id="form_main">
	  <div class="form-group mr-1">
         <select id="colaborador" name="colaborador" class="form-control" data-toggle="tooltip" data-placement="top" title="Colaborador">   				   		 
			 <option value="">Seleccione</option>	         
		 </select>		   
      </div>	  
      <div class="form-group mr-1">
         <input type="date" required="required" id="fecha_i" name="fecha_i" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
      </div>
      <div class="form-group mr-1">
         <input type="date" required="required" id="fecha_f" name="fecha_f" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
      </div>
      <div class="form-group mr-1">
         <input type="text" placeholder="Buscar por: Expediente, Nombre, Apellido o Identidad" data-toggle="tooltip" data-placement="top" title="Buscar por: Expediente, Nombre, Apellido o Identidad" id="bs_regis" autofocus class="form-control" size="50"/>
      </div>	  
	  <div class="form-group">
		<div class="dropdown show" data-toggle="tooltip" data-placement="top" title="Exportar">
		  <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			 <i class="fas fa-download fa-lg"></i> Exportar
		  </a>
		  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
			<a class="dropdown-item" href="#" id="reporte_excel">Reporte</a>
			<a class="dropdown-item" href="#" id="reporte_diario">Reporte Diario Usuarios</a>		
		  </div>
		</div>		  
	  </div>	  
      <div class="form-group">
	    <button class="btn btn-danger ml-1" type="submit" id="limpiar"><div class="sb-nav-link-icon" data-toggle="tooltip" data-placement="top" title="Limpiar"></div><i class="fas fa-broom fa-lg"></i> limpiar</button>
      </div>	   
    </form>	
	<hr/>   
    <div class="form-group">
	  <div class="col-sm-12">
		<div class="registros overflow-auto" id="agrega-registros"></div>
	   </div>		   
	</div>
	<nav aria-label="Page navigation example">
		<ul class="pagination justify-content-center" id="pagination"></ul>
	</nav>
	</div>
	<?php include("templates/factura.php"); ?>
	<?php include("templates/footer.php"); ?>	
</div>

    <!-- add javascripts -->
	<?php 
		include "script.php"; 
		
		include "../js/main.php"; 
		include "../js/myjava_reportes_atenciones_medicas.php";  		
		include "../js/select.php"; 	
		include "../js/functions.php"; 
		include "../js/myjava_cambiar_pass.php"; 		
	?>
	   
</body>
</html>