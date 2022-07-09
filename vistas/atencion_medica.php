<?php
session_start(); 
include "../php/funtions.php";

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}    

$_SESSION['menu'] = "Atenciones Medicas";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s"); 
$comentario = mb_convert_case("Ingreso al Modulo de Atenciones Medicas", MB_CASE_TITLE, "UTF-8");   

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
    <title>Atenciones Medicas :: <?php echo SERVEREMPRESA;?></title>
	<?php include("script_css.php"); ?>	
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->  
  <?php include("templates/modals.php"); ?>    

<!--INICIO MODAL-->
<div class="modal fade" id="modal_registro_atenciones">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Atenciones</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formulario_atenciones" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item waves-effect waves-light">
                  <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">Datos Generales</a>
                </li>
                <li class="nav-item waves-effect waves-light">
                  <a class="nav-link" id="profile-tab" data-toggle="tab" href="#historia_clinica_tab" role="tab" aria-controls="historia_clinica" aria-selected="false">Historia Clínica</a>
                </li>
                <li class="nav-item waves-effect waves-light">
                  <a class="nav-link" id="seguimiento-tab" data-toggle="tab" href="#seguimiento_tab" role="tab" aria-controls="seguimiento_tab" aria-selected="true">Tratamiento</a>
                </li>
              </ul>
			  
              <br>
              <div class="tab-content" id="myTabContent"><!-- INICIO TAB CONTENT-->
                <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab"><!-- INICIO TAB HOME-->
					<div class="form-row">
						<div class="col-md-12 mb-3">
						  <input type="hidden" id="agenda_id" name="agenda_id" class="form-control">
						  <input type="hidden" id="pacientes_id" name="pacientes_id" class="form-control">	
						  <div class="input-group mb-3">
							  <input type="text" required readonly id="pro" name="pro" class="form-control"/>
							  <div class="input-group-append">				
								<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-plus-square fa-lg"></i></a>
							  </div>
						   </div>	
						</div>				
					</div>	
					<div class="form-row">
						<div class="col-md-6 mb-3">
						  <label for="expedoente">Paciente <span class="priority">*<span/></label>
						  <div class="input-group mb-3">
							  <select id="paciente_consulta" name="paciente_consulta" class="form-control" data-toggle="tooltip" data-placement="top" title="Pacientes"></select>
							  <div class="input-group-append" id="buscar_pacientes_atenciones">				
								<a data-toggle="modal" href="#" class="btn btn-outline-success"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
							  </div>
						   </div>						  
						</div>
						<div class="col-md-3 mb-3">
						  <label for="edad">Fecha de Registro</label>
						  <input type="date" id="fecha" name="fecha" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
						</div>				
						<div class="col-md-3 mb-3">
						  <label for="edad">Fecha de Nacimiento</label>
						  <input type="date" id="fecha_nac" name="fecha_nac" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
						</div>							
					</div>
					<div class="form-row">					
						<div class="col-md-3 mb-3">
						  <label for="edad">Edad</label>
						  <input type="text" id="edad" name="edad" readonly class="form-control"/>
						</div>
						<div class="col-md-9 mb-3">
						  <label for="edad">Preclínica</label>
						  <input type="text" id="preclinica" name="preclinica" readonly class="form-control"/>
						</div>						
					</div>	
					<div class="form-row">					
						<div class="col-md-4 mb-3">
						  <label for="expedoente">Consultorio</label>
						  <div class="input-group mb-3">
							  <select id="servicio_id" name="servicio_id" class="form-control" data-toggle="tooltip" data-placement="top" title="Pacientes">
									<option value="">Seleccione</option>							  
							  </select>
							  <div class="input-group-append" id="buscar_servicios_atenciones">				
								<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
							  </div>
						   </div>
						</div>							
					</div>						
				</div><!-- FIN TAB HOME-->
                <div class="tab-pane fade" id="historia_clinica_tab" role="tabpanel" aria-labelledby="historia_clinica-tab"><!-- INICIO TAB HISTORIA CLINICA-->
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<div class="card">
							  <div class="card-header text-white bg-info mb-3" align="center">
								Antecedentes
							  </div>
							  <div class="card-body">
								<div class="input-group">
							      <textarea id="antecedentes" name="antecedentes" placeholder="Antecedentes" class="form-control" maxlength="3200" rows="8"></textarea>	
								  <div class="input-group-prepend">						  
									<span class="input-group-text">
										<i class="btn btn-outline-success fas fa-microphone-alt" id="search_antecedentes_start"></i>
										<i class="btn btn-outline-success fas fa-microphone-slash" id="search_antecedentes_stop"></i>
									</span>
								  </div>								  
								</div>	
								<p id="charNum_antecedentes">3200 Caracteres</p>
							  </div>
							</div>
						</div>
						<div class="col-md-12 mb-3">						
							<div class="card">
							  <div class="card-header text-white bg-info mb-3" align="center">
								Historia Clínica
							  </div>
							  <div class="card-body">
								<div class="input-group">
							      <textarea id="historia_clinica" name="historia_clinica" placeholder="Historia Clinica" class="form-control" maxlength="3200" rows="8"></textarea>	
								  <div class="input-group-prepend">						  
									<span class="input-group-text">
										<i class="btn btn-outline-success fas fa-microphone-alt" id="search_historia_clinica_start"></i>
										<i class="btn btn-outline-success fas fa-microphone-slash" id="search_historia_clinica_stop"></i>
								  </div>								  
								</div>	
								<p id="charNum_historia">3200 Caracteres</p>
							  </div>							  
							</div>
						</div>
						<div class="col-md-12 mb-3">
							<div class="card">
							  <div class="card-header text-white bg-info mb-3" align="center">
								Examen Físico
							  </div>
							  <div class="card-body">
								<div class="input-group">
							      <textarea id="exame_fisico" name="exame_fisico" placeholder="Examen Físico" class="form-control" maxlength="3200" rows="8"></textarea>	
								  <div class="input-group-prepend">						  
									<span class="input-group-text">
										<i class="btn btn-outline-success fas fa-microphone-alt" id="search_exame_fisico_start"></i>
										<i class="btn btn-outline-success fas fa-microphone-slash" id="search_exame_fisico_stop"></i>
								  </div>								  
								</div>	
								<p id="charNum_examen">3200 Caracteres</p>
							  </div>
							</div>
						</div>
						<div class="col-md-12 mb-3">
							<div class="card">
							  <div class="card-header text-white bg-info mb-3" align="center">
								Diagnostico
							  </div>
							  <div class="card-body">
								<div class="input-group">
							      <textarea id="diagnostico" name="diagnostico" placeholder="Diagnostico" class="form-control" maxlength="3200" rows="8"></textarea>	
								  <div class="input-group-prepend">						  
									<span class="input-group-text">
										<i class="btn btn-outline-success fas fa-microphone-alt" id="search_diagnostico_start"></i>
										<i class="btn btn-outline-success fas fa-microphone-slash" id="search_diagnostico_stop"></i>
								  </div>								  
								</div>	
								<p id="charNum_diagnostico">3200 Caracteres</p>
							  </div>							  
							</div>
						</div>						
					</div>	     
				</div><!-- FIN TAB HISTORIA CLINICA-->
                <div class="tab-pane fade" id="seguimiento_tab" role="tabpanel" aria-labelledby="seguimiento-tab"><!-- INICIO TAB SEGUIMIENTO-->
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<div class="card">
							  <div class="card-header text-white bg-info mb-3" align="center">
								Seguimiento (Tratamiento)
							  </div>
							  <div class="card-body">
								<div class="input-group">
							      <textarea id="seguimiento" name="seguimiento" placeholder="Tratamiento" class="form-control" maxlength="3200" rows="8"></textarea>	
								  <div class="input-group-prepend">						  
									<span class="input-group-text">
										<i class="btn btn-outline-success fas fa-microphone-alt" id="search_seguimiento_start"></i>
										<i class="btn btn-outline-success fas fa-microphone-slash" id="search_seguimiento_stop"></i>
								  </div>								  
								</div>						
								<p id="charNum_seguimiento">3200 Caracteres</p>
							  </div>
							</div>
						</div>
						<div class="col-md-12 mb-3">
							<div class="card">
							  <div class="card-header text-white bg-info mb-3" align="center">
								Historia Seguimiento (Tratamiento)
							  </div>
							  <div class="card-body">
								<textarea id="seguimiento_read" name="seguimiento_read" readonly placeholder="Tratamiento" class="form-control" maxlength="500" rows="11"></textarea>	
							  </div>
							</div>
						</div>								
					</div>	
				</div><!-- FIN TAB SEGUIMIENTO-->
              </div><!-- FIN TAB CONTENT-->	
			</form>
        </div>
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="reg_atencion" form="formulario_atenciones"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-primary ml-2" type="submit" id="edi_atencion" form="formulario_atenciones"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>			
		</div>		
      </div>
    </div>
</div>	

<!--MODAL BUSCAR ATENCIONES-->
<div class="modal fade" id="buscar_atencion">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Búsqueda de Atenciones</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formulario_buscarAtencion" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <input type="hidden" id="atencion_id" name="atencion_id" class="form-control" required="required">
					  <input type="hidden" id="pacientes_id" name="pacientes_id" class="form-control" required="required">
					</div>				
				</div>
				<div class="form-row" id="grupo_expediente">
					<div class="col-md-12 mb-3">
					  <input type="text" name="busqueda" id="busqueda" placeholder="Buscar por: Nombre, Apellido o Identidad" data-toggle="tooltip" data-placement="top" title="Búsqueda de Atenciones por: Nombre, Apellido o Identidad" class="form-control">
					</div>				
				</div>				
				<div class="form-row">
					<div class="col-md-12 mb-3">
					   <div class="registros overflow-auto" id="agrega_registros_busqueda"></div>
					</div>					
				</div>	
				<div class="form-row">
					<div class="col-md-12 mb-3">
						<nav aria-label="Page navigation example">
							<ul class="pagination justify-content-center" id="pagination_busqueda"></ul>
						</nav>	
					</div>					
				</div>
				<div class="form-row">
					<div class="col-md-12 mb-3">
					   <div class="registros overflow-auto" id="agrega_registros_busqueda_"></div>
					</div>					
				</div>	
				<div class="form-row">
					<div class="col-md-12 mb-3">
						<nav aria-label="Page navigation example">
							<ul class="pagination justify-content-center" id="pagination_busqueda_"></ul>
						</nav>	
					</div>					
				</div>				
			</form>
        </div>		
		<div class="modal-footer">
			
		</div>			
      </div>
    </div>
</div>	
<!-- FIN MODAL BUSCAR ATENCIONES

<!--INICIO MODAL TRANSITO-->
<div class="modal fade" id="registro_transito_eviada">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Transito Enviada</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formulario_transito_enviada" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" id="pacientes_id" name="pacientes_id" class="form-control" required="required">
					    <input type="hidden" id="colaborador_id" name="colaborador_id" class="form-control" required="required">
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>	 
					</div>							
				</div>
				<div class="form-row">
					<div class="col-md-6 mb-3">
						<label for="expedoente">Paciente <span class="priority">*<span/></label>
						<div class="input-group mb-3">
						  <select id="paciente_te" name="paciente_te" class="form-control" data-toggle="tooltip" data-placement="top" title="Paciente" required >
								<option value="">Seleccione</option>						  
						  </select>
						  <div class="input-group-append" id="buscar_pacientes_te">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
						  </div>
						</div>
					</div>
					<div class="col-md-6 mb-3">
					  <label for="edad">Fecha <span class="priority">*<span/></label>
					  <input type="date" required id="fecha" name="fecha" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
					</div>				
				</div>				
				<div class="form-row">
					<div class="col-md-6 mb-3">
					  <label for="nombre">Identidad</label>
					  <input type="text" name="identidad" id="identidad" placeholder="Identidad" readonly class="form-control"/>
					</div>
					<div class="col-md-6 mb-3">
					  <label for="apellido">Enviada a <span class="priority">*<span/></label>
						<div class="input-group mb-3">
						  <select id="enviada" name="enviada" class="form-control" data-toggle="tooltip" data-placement="top" title="Enviada a" required >
							  <option value="">Seleccione</option>								
						  </select>
						  <div class="input-group-append" id="buscar_colaboradores_te">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
						  </div>
						</div>					  
					</div>				
				</div>						
				<div class="form-row">			  
					<div class="col-md-12 mb-3">
					  <label for="direccion">Motivo <span class="priority">*<span/></label>
					  <textarea id="motivo" name="motivo" required placeholder="Motivo de la Referencia" class="form-control" maxlength="255" rows="3"></textarea>	
					  <p id="charNumMotivoTE">255 Caracteres</p>
					</div>
				</div>	

					
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" form="formulario_transito_enviada" type="submit" id="reg_transitoe"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>			
		</div>			
      </div>
    </div>
</div>

<div class="modal fade" id="registro_transito_recibida">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Transito Recibida</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formulario_transito_recibida" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" id="pacientes_id" name="pacientes_id" class="form-control" required="required">
					    <input type="hidden" id="colaborador_id" name="colaborador_id" class="form-control" required="required">
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>	 
					</div>							
				</div>
				<div class="form-row">
					<div class="col-md-6 mb-3">
						<label for="expedoente">Paciente <span class="priority">*<span/></label>
						<div class="input-group mb-3">
						  <select id="paciente_tr" name="paciente_tr" class="form-control" data-toggle="tooltip" data-placement="top" title="Paciente" required >
								<option value="">Seleccione</option>							  
						  </select>
						  <div class="input-group-append" id="buscar_pacientes_tr">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
						  </div>
						</div>
					</div>
					<div class="col-md-6 mb-3">
					  <label for="edad">Fecha <span class="priority">*<span/></label>
					  <input type="date" required id="fecha" name="fecha" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
					</div>				
				</div>				
				<div class="form-row">
					<div class="col-md-6 mb-3">
					  <label for="nombre">Identidad</label>
					  <input type="text" name="identidad" id="identidad" placeholder="Identidad" readonly class="form-control"/>
					</div>
					<div class="col-md-6 mb-3">
					  <label for="apellido">Recibida de <span class="priority">*<span/></label>
						<div class="input-group mb-3">
						  <select id="recibida" name="recibida" class="form-control" data-toggle="tooltip" data-placement="top" title="Recibida de" required >
								<option value="">Seleccione</option>							  
						  </select>
						  <div class="input-group-append" id="buscar_colaboradores_tr">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
						  </div>
						</div>					  
					</div>				
				</div>						
				<div class="form-row">			  
					<div class="col-md-12 mb-3">
					  <label for="direccion">Motivo <span class="priority">*<span/></label>
					  <textarea id="motivo" name="motivo" required placeholder="Motivo de la Referencia" class="form-control" maxlength="255" rows="3"></textarea>	
					  <p id="charNumMotivoTE">255 Caracteres</p>
					</div>
				</div>	

					
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" form="formulario_transito_recibida" type="submit" id="reg_transitor"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>			
		</div>			
      </div>
    </div>
</div>
   <?php include("modals/modals.php");?>	

   <!--Fin Ventanas Modales-->
	<!--MENU-->	  
       <?php include("templates/menu.php"); ?>
    <!--FIN MENU--> 
	
<br><br><br>
<div class="container-fluid">
	<ol class="breadcrumb mt-2 mb-4">
		<li class="breadcrumb-item" id="acciones_atras"><a id="ancla_volver" class="breadcrumb-link" href="#">Atenciones Medicas</a></li>
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span></li>
	</ol>
	
	<div id="main_facturacion">
    <form class="form-inline" id="form_main">
	  <div class="form-group mr-1">
         <select id="estado" name="estado" class="form-control" data-toggle="tooltip" data-placement="top" title="Atención">   				   		 
			 <option value="">Seleccione</option>	         
		 </select>		   
      </div>	  
      <div class="form-group mr-1">
         <input type="date" required="required" id="fecha_b" name="fecha_b" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
      </div>
      <div class="form-group mr-1">
         <input type="date" required="required" id="fecha_f" name="fecha_f" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
      </div>
      <div class="form-group mr-1">
         <input type="text" placeholder="Buscar por: Expediente, Nombre, Apellido o Identidad" data-toggle="tooltip" data-placement="top" title="Buscar por: Expediente, Nombre, Apellido o Identidad" id="bs_regis" autofocus class="form-control" size="50"/>
      </div>
      <div class="form-group">
	    <button class="btn btn-primary ml-1" type="submit" id="nuevo_registro"><div class="sb-nav-link-icon"></div><i class="fas fa-plus-circle fa-lg"></i> Crear</button>
      </div>
      <div class="form-group mr-1">
	    <button class="btn btn-primary ml-1" type="submit" id="nueva_factura"><div class="sb-nav-link-icon"></div><i class="fas fa-file-invoice fa-lg"></i> Pre Factura</button>
      </div>	  
	  <div class="form-group">
		<div class="dropdown show">
		  <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			 <i class="fas fa-user-plus fa-lg"></i> Transito
		  </a>
		  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
			<a class="dropdown-item" href="#" id="transito_enviada">Transito Enviada</a>
			<a class="dropdown-item" href="#" id="transito_recibida">Transito Recibida</a>		
		  </div>
		</div>		  
	  </div> 	  
      <div class="form-group">
	    <button class="btn btn-success ml-1" type="submit" id="historial" data-toggle="tooltip" data-placement="top" title="Historial de Atenciones"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i> Historial</button>
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
	<?php include("templates/footer_facturas.php"); ?>	
</div>

    <!-- add javascripts -->
	<?php 
		include "script.php"; 
		
		include "../js/main.php"; 
		include "../js/invoice.php"; 
		include "../js/myjava_atencion_medica.php"; 		
		include "../js/select.php"; 	
		include "../js/functions.php"; 
		include "../js/myjava_cambiar_pass.php"; 		
	?>	
	  
</body>
</html>