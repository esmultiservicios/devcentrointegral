<?php
session_start(); 
include "../php/funtions.php";

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}    

$_SESSION['menu'] = "Administrador de Secuencias";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s"); 
$comentario = mb_convert_case("Ingreso al Modulo de Administrador de Secuencias", MB_CASE_TITLE, "UTF-8");   

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
    <title>Administrador de Secuencias :: <?php echo SERVEREMPRESA;?></title>
	<?php include("script_css.php"); ?>   	
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->  
  <?php include("templates/modals.php"); ?>    

<!--INICIO MODAL-->
<div class="modal fade" id="modalAdministradorSecuencias">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Administrador Secuencias</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formularioAdministradorSecuencias" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" id="tabla" name="tabla" class="form-control">
					    <input type="hidden" id="secuencias_id" name="secuencias_id" class="form-control">	
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>	 
					</div>							
				</div>
				<div class="form-row">
					<div class="col-md-3 mb-3">
					  <label>Empresa</label>
					  <div class="input-group mb-3">
                        <select id="empresa" name="empresa" class="form-control" data-toggle="tooltip" data-placement="top" title="Empresa">   				   
                        </select>
						  <div class="input-group-append" id="buscar_empreas">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-building fa-lg"></i></a>
						  </div>
					   </div>
					</div>
					<div class="col-md-3 mb-3">
					  <label>Tabla</label>
					  <div class="input-group mb-3">
                        <select id="entidad" name="entidad" class="form-control" data-toggle="tooltip" data-placement="top" title="Tabla">   				   
							<option value="">Seleccione<option>
                        </select>
						  <div class="input-group-append" id="buscar_tabla_db">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-building fa-lg"></i></a>
						  </div>
					   </div>
					</div>	
					<div class="col-md-3 mb-3">
					  <label>Prefijo</label>
					  <div class="input-group mb-3">
						  <input type="text" name="prefijo" id="prefijo" class="form-control" placeholder="Prefijo" maxlength="24" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
						  <div class="input-group-append">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fab fa-autoprefixer fa-lg"></i></a>
						  </div>
					   </div>
					</div>	
					<div class="col-md-3 mb-3">
					  <label>Sufijo</label>
					  <div class="input-group mb-3">
						  <input type="text" name="sufijo" id="sufijo" class="form-control" placeholder="Sufijo" maxlength="24" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
						  <div class="input-group-append">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fab fa-autoprefixer fa-lg"></i></a>
						  </div>
					   </div>
					</div>						
				</div>				
				<div class="form-row">						
					<div class="col-md-3 mb-3">
					  <label>Relleno <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="number" name="relleno" id="relleno" class="form-control" placeholder="Relleno" required="required">
						  <div class="input-group-append">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-fill fa-lg"></i></a>
						  </div>
					   </div>					   
					</div>	
					<div class="col-md-3 mb-3">
					  <label>Incremento <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="number" name="incremento" id="incremento" class="form-control" data-toggle="tooltip" data-placement="top" title="Incremento" placeholder="Siguiente" required="required">
						  <div class="input-group-append">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-caret-right fa-lg"></i></a>
						  </div>
					   </div>
					</div>	
					<div class="col-md-3 mb-3">
					  <label>Siguiente <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="text" name="siguiente" id="siguiente" class="form-control" placeholder="Siguiente" required="required">
						  <div class="input-group-append">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-list-ol fa-lg"></i></a>
						  </div>
					   </div>
					</div>	
					<div class="col-md-3 mb-3">
						<label>Estado <span class="priority">*<span/></label>
						<div class="input-group mb-3">
						  <select id="estado" name="estado" class="form-control" data-toggle="tooltip" data-placement="top" title="Estado">   				   
						  </select>
						  <div class="input-group-append">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-toggle-on fa-lg"></i></a>
						  </div>
						</div>
					</div>						
				</div>
				<div class="form-row">						
					<div class="col-md-12 mb-3">
					  <label>Comentario</label>
					  <div class="input-group mb-3">
						  <input type="text" name="comentario" id="comentario" class="form-control" placeholder="Comentario" maxlength="150" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" data-toggle="tooltip" data-placement="top" title="Comentario el cual puede servir de guía o recordatorio de los datos almacenados en este formulario">
						  <div class="input-group-append">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-comments fa-lg"></i></a>
						  </div>
					   </div>					   
					</div>					
				</div>	
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label style="color: #077A2F;" align="center"><b>Leyendas para Prefijo y Sufijo</b></label>				   
					</div>				
				</div>
				<div class="form-row">
					<div class="col-md-3 mb-3">
					  <label data-toggle="tooltip" data-placement="top" title="Permite obtener el año actual. Esta leyenda se escribe tal cual esta en el prefijo o sufijo de ser necearias: ejemplo: @año_actual"><b>Año actual:</b> @año_actual</label>				   
					</div>	
					<div class="col-md-3 mb-3">
					  <label data-toggle="tooltip" data-placement="top" title="Permite obtener el mes actual. Esta leyenda se escribe tal cual esta en el prefijo o sufijo de ser necearias: ejemplo: @mes_actual"><b>Mes actual:</b> @mes_actual</label>				   
					</div>	
					<div class="col-md-3 mb-3">
					  <label data-toggle="tooltip" data-placement="top" title="Permite obtener el día actual. Esta leyenda se escribe tal cual esta en el prefijo o sufijo de ser necearias: ejemplo: @dia_actual"><b>Dia actual:</b> @dia_actual</label>				   
					</div>				
					<div class="col-md-3 mb-3">
					  <label data-toggle="tooltip" data-placement="top" title="Permite obtener el día de la semana actual. Esta leyenda se escribe tal cual esta en el prefijo o sufijo de ser necearias: ejemplo: @dia_semana"><b>Dia de la semana:</b> @dia_semana</label>			   
					</div>						
				</div>				
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" form="formularioAdministradorSecuencias" type="submit" id="reg"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-warning ml-2" form="formularioAdministradorSecuencias" type="submit" id="edi"><div class="sb-nav-link-icon"></div><i class="fas fa-edit fa-lg"></i> Modificar</button>
			<button class="btn btn-danger ml-2" form="formularioAdministradorSecuencias" type="submit" id="delete"><div class="sb-nav-link-icon"></div><i class="fas fa-trash fa-lg"></i> Eliminar</button>			
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
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Administrador de Secuencias</li>
	</ol>
	
    <form class="form-inline" id="form_main">
	    <div class="form-group mr-1">
			<div class="input-group">				
				<div class="input-group-append">				
					<span class="input-group-text"><div class="sb-nav-link-icon"></div>Empresa</span>
				</div>
				 <select id="empresa" name="empresa" class="custom-select" data-toggle="tooltip" data-placement="top" title="Empresa" style="width:250px">  
				 </select>	   
			</div>		   
		</div>	
	    <div class="form-group mr-1">
			<div class="input-group">				
				<div class="input-group-append">				
					<span class="input-group-text"><div class="sb-nav-link-icon"></div>Estado</span>
				</div>
				 <select id="estado" name="estado" class="form-control" data-toggle="tooltip" data-placement="top" title="Estado">   				   		 
				 </select>	   
			</div>		   
		</div>			
		<div class="form-group mr-1">
			<div class="input-group">				
				<div class="input-group-append">				
					<span class="input-group-text"><div class="sb-nav-link-icon"></div>Fecha Inicial</span>
				</div>
				<input type="date" required="required" id="fecha_b" name="fecha_b" style="width: 159px;" value="<?php echo date ("Y-m-d");?>" data-toggle="tooltip" data-placement="top" title="Fecha Inicial" class="form-control"/>  
			</div>		   
		</div>	
		<div class="form-group mr-1">
			<div class="input-group">				
				<div class="input-group-append">				
					<span class="input-group-text"><div class="sb-nav-link-icon"></div>Fecha Final</span>
				</div>
				<input type="date" required="required" id="fecha_f" name="fecha_f" style="width: 159px;" value="<?php echo date ("Y-m-d");?>" data-toggle="tooltip" data-placement="top" title="Fecha Inicial" class="form-control"/>  
			</div>		   
		</div>
      <div class="form-group mr-1">
		<input type="text" placeholder="Buscar por: Empresa" title="Buscar por: Empresa" id="bs_regis" autofocus class="form-control" size="30"/>
      </div>	  
      <div class="form-group">
	    <button class="btn btn-primary ml-1" type="submit" id="nuevo_registro" data-toggle="tooltip" data-placement="top" title="Nuevo Registro"><div class="sb-nav-link-icon"></div><i class="fas fa-plus-circle fa-lg"></i> Crear</button>
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
		include "../js/myjava_administrador_secuencias.php"; 
		include "../js/select.php"; 	
		include "../js/functions.php"; 
		include "../js/myjava_cambiar_pass.php"; 		
	?>
	
</body>
</html>