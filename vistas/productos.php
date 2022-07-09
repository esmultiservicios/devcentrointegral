<?php
session_start(); 
include "../php/funtions.php";

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}    

$_SESSION['menu'] = "Almacen";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s"); 
$comentario = mb_convert_case("Ingreso al Modulo de Almacen", MB_CASE_TITLE, "UTF-8");   

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
    <title>Productos :: <?php echo SERVEREMPRESA;?></title>
	<?php include("script_css.php"); ?>	
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->  
  <?php include("templates/modals.php"); ?>    

<!--INICIO MODAL-->
   <?php include("modals/modals.php");?>
<!--FIN MODAL-->  	

   <!--Fin Ventanas Modales-->
	<!--MENU-->	  
       <?php include("templates/menu.php"); ?>
    <!--FIN MENU--> 
	
<br><br><br>
<div class="container-fluid">
	<ol class="breadcrumb mt-2 mb-4">
		<li class="breadcrumb-item"><a class="breadcrumb-link" href="<?php echo SERVERURL; ?>vistas/inicio.php">Dashboard</a></li>
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Almacén</li>
	</ol>
	
    <form class="form-inline" id="form_main">
		<div class="form-group mx-sm-3 mb-2">
			<label>Estado</label>
			  <select id="estado_producto" name="estado_producto" class="custom-select ml-1" data-toggle="tooltip" data-placement="top" title="Estado">
					<option value=1>Activo</option>
					<option value=2>Inactivo</option>
			  </select>
		  </div>
		<div class="form-group mx-sm-3 mb-2">
			<label>Categoría</label>
			  <select id="categoria_id" name="categoria_id" class="custom-select ml-1" data-toggle="tooltip" data-placement="top" title="Categoría">
					<option value="">Seleccione</option>
			  </select>
		  </div>	
		  <button type="submit" class="btn btn-primary mb-2 mr-1" data-toggle="tooltip" data-placement="top" title="Actualizar" id="actualizar"><i class="fas fa-sync-alt fa-lg"></i> Actualizar</button>
	</form>
	<hr/> 	
	<div class="table-responsive">
		<form id="formPrincipal">
			<div class="col-md-12 mb-3">
				<table id="dataTableProductos" class="table table-striped table-condensed table-hover" style="width:100%">
					<thead>
						<tr>
							<th>Producto</th>
							<th>Cantidad</th>
							<th>Concentracion</th>
							<th>Medida</th>
							<th>Categoría</th>							
							<th>Almacen</th>							
							<th>Precio Compra</th>
							<th>Precio Venta</th>
							<th>ISV</th>							
							<th>Descripción</th>
							<th>Editar</th>
							<th>Eliminar</th>							
						</tr>
					</thead>
				</table> 
			</div>
		<form>
	</div>
    <?php include("templates/footer.php"); ?> 	
</div>

    <!-- add javascripts -->
	<?php 
		include "script.php"; 
		
		include "../js/main.php"; 
		include "../js/myjava_productos.php"; 	
		include "../js/select.php"; 	
		include "../js/functions.php"; 
		include "../js/myjava_cambiar_pass.php"; 		
	?>

</body>
</html>