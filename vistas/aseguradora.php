<?php
session_start(); 
include "../php/funtions.php";

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}    

$_SESSION['menu'] = "Empresa";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s"); 
$comentario = mb_convert_case("Ingreso al Modulo de Empresa", MB_CASE_TITLE, "UTF-8");   

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
	<title>Empresa :: <?php echo SERVEREMPRESA;?></title>
	<?php include("script_css.php"); ?>   
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->  
  <?php include("templates/modals.php"); ?>    

<!--INICIO MODAL-->
<div class="modal fade" id="modalAseguradora">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Empresa</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formularioAseguradora" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">	
				<input type="hidden" name="aseguradora_id" id="aseguradora_id" class="form-control">

				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label for="aseguradora">Aseguradora <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="text" name="aseguradora" id="aseguradora" class="form-control" placeholder="Nombre de la Aseguradora" maxlength="150" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required="required">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-building fa-lg"></i></span>
							</div>
					   </div>
					</div>										
				</div>

				<div class="form-row">				
					<div class="col-md-12 mb-3">
					  <label for="rtn_aseguradora">RTN <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="text" name="rtn_aseguradora" id="rtn_aseguradora" class="form-control" placeholder="RTN Aseguradora" maxlength="14" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required="required">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-id-card-alt fa-lg"></i></span>
							</div>
					   </div>
					</div>						
				</div>				
			
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" form="formularioAseguradora" type="submit" id="reg"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-primary ml-2" form="formularioAseguradora" type="submit" id="edi"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>			
		</div>			
      </div>
    </div>
</div>	
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
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Empresas</li>
	</ol>
	
    <form class="form-inline" id="form_main">	
      <div class="form-group mr-1">
        <input type="text" placeholder="Buscar por: Empresa" title="Buscar por: Aeguradora" id="bs_regis" autofocus class="form-control" size="60"/>
      </div>		  
      <div class="form-group">
	    <button class="btn btn-primary ml-1" type="submit" id="nuevo_registro" data-toggle="tooltip" data-placement="top" title="Crear"><div class="sb-nav-link-icon"></div><i class="fas fa-plus-circle fa-lg"></i> Crear</button>
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
    <?php include("templates/footer.php"); ?> 	
</div>

    <!-- add javascripts -->
	<?php 
		include "script.php"; 
		
		include "../js/main.php"; 
		include "../js/myjava_aseguradora.php"; 		
		include "../js/select.php"; 	
		include "../js/functions.php"; 
		include "../js/myjava_cambiar_pass.php"; 		
	?>
		
</body>
</html>