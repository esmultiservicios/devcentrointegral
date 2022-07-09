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

	<style>
		/*Profile Pic Start*/
		.picture-container{
			position: relative;
			cursor: pointer;
			text-align: center;
		}
		.picture{
			width: 90px;
			height: 88px;
			background-color: #999999;
			border: 4px solid #CCCCCC;
			color: #FFFFFF;
			border-radius: 50%;
			margin: 0px auto;
			overflow: hidden;
			transition: all 0.2s;
			-webkit-transition: all 0.2s;
		}
		.picture:hover{
			border-color: #2ca8ff;
		}
		.content.ct-wizard-green .picture:hover{
			border-color: #05ae0e;
		}
		.content.ct-wizard-blue .picture:hover{
			border-color: #3472f7;
		}
		.content.ct-wizard-orange .picture:hover{
			border-color: #ff9500;
		}
		.content.ct-wizard-red .picture:hover{
			border-color: #ff3b30;
		}
		.picture input[type="file"] {
			cursor: pointer;
			display: block;
			height: 100%;
			left: 0;
			opacity: 0 !important;
			position: absolute;
			top: 0;
			width: 100%;
		}

		.picture-src{
			width: 100%;
			
		}
		/*Profile Pic End*/	
	</style>
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->  
  <?php include("templates/modals.php"); ?>    

<!--INICIO MODAL-->
<div class="modal fade" id="modalEmpresa">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Empresa</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formularioEmpresa" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" id="empresa_id" name="empresa_id" class="form-control" required="required">
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
						<div class="picture-container">
							<div class="picture">
								<img src="../img/avatar.jpg" class="picture-src" id="wizardPicturePreview" title="">
								<input type="file" id="wizard-picture" class="">
							</div>
							 <h6 class="">Seleccionar Imagen</h6>

						</div>					  					  
					</div>	
					<div class="col-md-5 mb-3">
					  <label for="expedoente">Empresa</label>
					  <div class="input-group mb-3">
						  <input type="text" name="empresa" id="empresa" class="form-control"  placeholder="Empresa" required="required">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-building fa-lg"></i></span>
							</div>
					   </div>
					</div>	
					<div class="col-md-4 mb-3">
					  <label for="edad">RTN</label>
					  <div class="input-group mb-3">
						  <input type="number" name="rtn" id="rtn" class="form-control" placeholder="RTN" required="required">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-id-card-alt fa-lg"></i></span>
							</div>
					   </div>
					</div>						
				</div>				
				<div class="form-row">
					<div class="col-md-6 mb-3">
					  <label for="nombre">Otra Información</label>
					  <div class="input-group mb-3">
						  <input type="text" name="otra_info" id="otra_info" class="form-control" placeholder="Otra Información" maxlength="150" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-phone-alt fa-lg"></i></span>
							</div>
					   </div>
					</div>				
					<div class="col-md-6 mb-3">
					  <label for="nombre">Eslogan</label>
					  <div class="input-group mb-3">
						  <input type="text" name="eslogan" id="eslogan" class="form-control" placeholder="Eslogan" maxlength="150" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-file-alt fa-lg"></i></span>
							</div>
					   </div>
					</div>						
				</div>
				<div class="form-row">
					<div class="col-md-3 mb-3">
					  <label for="nombre">Teléfono</label>
					  <div class="input-group mb-3">
						  <input type="number" name="telefono" id="telefono" class="form-control" placeholder="Teléfono" maxlength="8" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-phone-alt fa-lg"></i></span>
							</div>
					   </div>
					</div>					
					<div class="col-md-3 mb-3">
					  <label for="nombre">WhatsApp</label>
					  <div class="input-group mb-3">
						  <input type="number" name="celular" id="celular" class="form-control" placeholder="Teléfono" maxlength="8" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fab fa-whatsapp fa-lg"></i></span>
							</div>
					   </div>
					</div>				
					<div class="col-md-6 mb-3">
					  <label for="nombre">Correo</label>
					  <div class="input-group mb-3">
						  <input type="text" name="correo" id="correo" class="form-control" placeholder="Correo">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-at fa-lg"></i></span>
							</div>
					   </div>
					</div>						
				</div>
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label for="nombre">Horarios de Atención</label>
					  <div class="input-group mb-3">
						  <input type="text" name="horario_atencion" id="horario_atencion" class="form-control" placeholder="Horarios de Atención">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="far fa-clock fa-lg"></i></span>
							</div>
					   </div>
					</div>							
				</div>
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label for="nombre">Facebook</label>
					  <div class="input-group mb-3">
						  <input type="text" name="facebook_empresa" id="facebook_empresa" class="form-control" placeholder="Facebook">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa-brands fa-facebook"></i></span>
							</div>
					   </div>
					</div>							
				</div>
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label for="nombre">Sitio WEB</label>
					  <div class="input-group mb-3">
						  <input type="text" name="sitioweb_empresa" id="sitioweb_empresa" class="form-control" placeholder="Sitio WEB">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa-solid fa-globe"></i></span>
							</div>
					   </div>
					</div>							
				</div>									
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label for="nombre">Dirección</label>
					  <div class="input-group mb-3">
						  <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Direccion">
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-address-card fa-lg"></i></span>
							</div>
					   </div>
					</div>							
				</div>				
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" form="formularioEmpresa" type="submit" id="reg"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-primary ml-2" form="formularioEmpresa" type="submit" id="edi"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>			
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
			<div class="input-group">				
				<div class="input-group-append">				
					<span class="input-group-text"><div class="sb-nav-link-icon"></div>Empresa</span>
				</div>
				 <select id="empresa" name="empresa" class="custom-select" data-toggle="tooltip" data-placement="top" title="Empresa" style="width:450px">  
				 </select>	   
			</div>		   
		</div>	
      <div class="form-group mr-1">
        <input type="text" placeholder="Buscar por: Empresa" title="Buscar por: Empresa" id="bs_regis" autofocus class="form-control" size="60"/>
      </div>		  
      <div class="form-group">
	    <button class="btn btn-primary ml-1" type="submit" id="nuevo_registro" data-toggle="tooltip" data-placement="top" title="Exportar"><div class="sb-nav-link-icon"></div><i class="fas fa-plus-circle fa-lg"></i> Crear</button>
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
		include "../js/myjava_empresa.php"; 		
		include "../js/select.php"; 	
		include "../js/functions.php"; 
		include "../js/myjava_cambiar_pass.php"; 		
	?>
		
</body>
</html>