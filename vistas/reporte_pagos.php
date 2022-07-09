<?php
session_start(); 
include "../php/funtions.php";

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}    

$_SESSION['menu'] = "Reporte de Pagos";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s"); 
$comentario = mb_convert_case("Ingreso al Modulo de Reporte de Pagos", MB_CASE_TITLE, "UTF-8");   

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
    <title>Reporte de Pagos :: <?php echo SERVEREMPRESA;?></title>
	<?php include("script_css.php"); ?>	
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->  
  <?php include("templates/modals.php"); ?>    

<!--INICIO MODAL-->
<div class="modal fade" id="mensaje_show">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Detalles de Pagos</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formCobros" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					   <span id="mensaje_mensaje_show"></span>
					</div>				
				</div>											
			  <div class="modal-footer">
				<button class="btn btn-primary ml-2" type="button" id="okay" data-dismiss="modal"><div class="sb-nav-link-icon" id="okay"></div><i class="fas fa-thumbs-up fa-lg"></i> Okay</button>
				<button class="btn btn-danger ml-2" type="button" id="bad" data-dismiss="modal"><div class="sb-nav-link-icon" id="bad"></div><i class="fas fa-times-circle fa-lg"></i> Okay</button>				
			  </div>	  
			</form>
        </div>
      </div>
    </div>
</div>	

<div class="modal fade" id="modal_editar_pagos">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tipo Pago</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formulario_reporte_pagos" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" required readonly id="pagos_id" name="pagos_id" />
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>	 
					</div>							
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="fecha_reporte_pago">Fecha</label>
					  <input type="date" id="fecha_reporte_pago" name="fecha_reporte_pago" step="0.01" class="form-control"/>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="paciente_reporte_pago">Paciente</label>
					  <input type="text" id="paciente_reporte_pago" name="paciente_reporte_pago" step="0.01"class="form-control"/>
					  </select>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="factura_reporte_pago">Factura</label>
					  <input type="text" id="factura_reporte_pago" name="factura_reporte_pago" step="0.01" class="form-control"/>
					</div>					
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="tipo_pago_reporte">Tipo Pago</label>
					  <select id="tipo_pago_reporte" name="tipo_pago_reporte" class="form-control" data-toggle="tooltip" data-placement="top" title="Tipo de Pago">		   
                      </select>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="paciente_reporte_efectivo">Efectivo</label>
					  <input type="text" id="paciente_reporte_efectivo" name="paciente_reporte_efectivo" step="0.01" class="form-control"/>
					  </select>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="factura_reporte_tarjeta">Tarjeta</label>
					  <input type="text" id="factura_reporte_tarjeta" name="factura_reporte_tarjeta" readonly step="0.01" class="form-control"/>
					</div>											
				</div>	
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="tipo_pago_importe">Importe</label>
					  <input type="text" id="tipo_pago_importe" name="tipo_pago_importe" step="0.01" class="form-control"/>	   
                      </select>
					</div>											
				</div>																  
			</form>
        </div>
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="reg_reporte_pagos" form="formulario_reporte_pagos"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>			
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
		<li class="breadcrumb-item"><a class="breadcrumb-link" href="inicio.php">Dashboard</a></li>
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Reporte de Pagos</li>
	</ol>
	
    <form class="form-inline" id="form_main">
	  <div class="form-group mr-1">
         <select id="profesional" name="profesional" class="form-control" data-toggle="tooltip" data-placement="top" title="Profesional">   				   		 
			<option value="">Profesional</option>
         </select>	   
      </div>	  
      <div class="form-group mr-1">
        <select id="estado" name="estado" class="form-control" data-toggle="tooltip" data-placement="top" title="Estado">   				   		 
			<option value="">Estado</option>
         </select>	
      </div>
	  <div class="form-group mr-1">
		<input type="date" required="required" id="fecha_b" name="fecha_b" data-toggle="tooltip" data-placement="top" title="Fecha Inicial" value="<?php 
						$fecha = date ("Y-m-d");
						
						$año = date("Y", strtotime($fecha));
						$mes = date("m", strtotime($fecha));
						$dia = date("d", mktime(0,0,0, $mes+1, 0, $año));

						$dia1 = date('d', mktime(0,0,0, $mes, 1, $año)); //PRIMER DIA DEL MES
						$dia2 = date('d', mktime(0,0,0, $mes, $dia, $año)); // ULTIMO DIA DEL MES

						$fecha_inicial = date("Y-m-d", strtotime($año."-".$mes."-".$dia1));
						$fecha_final = date("Y-m-d", strtotime($año."-".$mes."-".$dia2));						
						
						
						echo $fecha_inicial;
		?>" class="form-control"/>	   
      </div>	  
      <div class="form-group mr-1">
        <input type="date" required="required" id="fecha_f" name="fecha_f" value="<?php echo date ("Y-m-d");?>" data-toggle="tooltip" data-placement="top" title="Fecha Final" class="form-control"/>
      </div>
      <div class="form-group mr-1">
		<input type="text" placeholder="Buscar por: Expediente, Paciente, Identidad o Número de Factura" data-toggle="tooltip" data-placement="top" title="Buscar por: Expediente, Nombre, Apellido, Identidad o Número de Factura" id="bs_regis" autofocus class="form-control" size="45"/>
      </div>	
      <div class="form-group">
	    <button class="btn btn-success ml-1" type="submit" id="reporte" data-toggle="tooltip" data-placement="top" title="Exportar"><div class="sb-nav-link-icon"></div><i class="fas fa-download fa-lg"></i> Exportar</button>
      </div>		   
    </form>	
	<hr/>   
    <div class="form-group">
	  <div class="col-sm-12">
		<div class="registros overflow-auto" id="agrega-registros"></div>
	   </div>		   
	</div>
	<nav aria-label="Page navigation example">
		<ul class="pagination justify-content-center"" id="pagination"></ul>
	</nav>
    <?php include("templates/footer.php"); ?> 	
</div>

    <!-- add javascripts -->
	<?php 
		include "script.php"; 
		
		include "../js/main.php"; 
		include "../js/myjava_reporte_pagos.php";  		
		include "../js/select.php"; 	
		include "../js/functions.php"; 
		include "../js/myjava_cambiar_pass.php"; 		
	?>
  
</body>
</html>