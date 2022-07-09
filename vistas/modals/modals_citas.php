<!--INICIO MODAL AGREGAR CITAS-->
<div class="modal fade" id="ModalAdd">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Citas</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form id="form-addevent" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">	
				<input type="text" name="paciente_id" class="form-control" id="paciente_id" placeholder="Paciente" style="display: none;">	
	            <input type="text" name="medico" class="form-control" id="medico" placeholder="Médico" style="display: none;">
	            <input type="text" name="serv" class="form-control" id="serv" placeholder="Servicio" style="display: none;">
                <input type="text" name="unidad" class="form-control" id="unidad" placeholder="Puesto" style="display: none;">			
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="nombre_proveedores">Expediente</label>
						  <input type="number" name="expediente" class="form-control" id="expediente" placeholder="Expediente o Identidad" required="required">
					</div>
					<div class="col-md-4 mb-3">
					  <label for="apellido_proveedores">Profesional</label>
					  <input type="text" name="profesional_citas" readonly class="form-control" id="profesional_citas" required="required">
					</div>
					<div class="col-md-4 mb-3">
					  <label for="rtn_proveedores">Color</label>
						  <select name="color" class="form-control" id="color" readonly="readonly">
							  <option value="">Choose</option>
							  <option style="color:#0071c5;" value="#0071c5">&#9724; Azul Oscuro</option>
							  <option style="color:#008000;" value="#008000">&#9724; Verde</option>							  
						  </select>
					</div>					
				</div>
				<div class="form-row">			  
					<div class="col-md-12 mb-3">
					  <label for="telefono_proveedores">Nombre</label>
					  <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Nombre" readonly="readonly">
					</div>
				</div>
				
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="nombre_proveedores">Fecha Cita Incio</label>
						  <input type="text" name="fecha_cita" class="form-control" id="fecha_cita" readonly="readonly">
					</div>
					<div class="col-md-4 mb-3">
					  <label for="apellido_proveedores">Fecha Cita End</label>
					  <input type="text" name="fecha_cita_end" class="form-control" id="fecha_cita_end" readonly="readonly" data-toggle="tooltip" data-placement="top" data-toggle="tooltip" data-placement="top" data-toggle="tooltip" data-placement="top" title="Año-Mes-Dia Hora:Minutos:Segundos">
					</div>
					<div class="col-md-4 mb-3">
					  <label for="rtn_proveedores">Hora</label>
						  <input type="time" name="hora" class="form-control" id="hora" placeholder="Hora" readonly data-toggle="tooltip" data-placement="top" title="Año-Mes-Dia Hora:Minutos:Segundos">
					</div>					
				</div>	
				<div class="form-row">			  
					<div class="col-md-12 mb-3">
					  <label for="telefono_proveedores">Observación</label>
					  <input type="text" name="obs" class="form-control" id="obs" placeholder="Observación">
					</div>
				</div>									  
			</form>
        </div>	
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="ModalAdd_enviar" form="form-addevent"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>			
		</div>		
      </div>
    </div>
</div>
<!--FIN MODAL AGREGAR CITAS-->

<!--INICIO MODAL EDITAR CITAS-->
<div class="modal fade" id="ModalEdit">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Editar/Eliminar una Cita</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form id="form-editevent" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">	
				<input type="hidden" name="paciente_id" class="form-control" id="paciente_id" placeholder="Paciente">	
	            <input type="hidden" name="id" class="form-control" id="id" placeholder="Médico">
	            <input type="hidden" name="medico" class="form-control" id="medico" placeholder="Médico">
	            <input type="hidden" name="serv" class="form-control" id="serv" placeholder="Servicio">
                <input type="hidden" name="unidad" class="form-control" id="unidad" placeholder="Puesto">			
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="nombre_proveedores">Expediente</label>
					  <input type="text" name="expediente_edit" class="form-control" id="expediente_edit" placeholder="Expediente" readonly="readonly">
					</div>
					<div class="col-md-8 mb-3">
					  <label for="apellido_proveedores">Paciente</label>
					  <input type="text" name="paciente" class="form-control" id="paciente" placeholder="Paciente" readonly="readonly">
					  <input type="text" name="medico1" class="form-control" id="medico1" readonly="readonly" style="display: none;">
					</div>					
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="nombre_proveedores">Fecha</label>
					  <input type="date" name="fecha_citaedit" class="form-control" id="fecha_citaedit" data-toggle="tooltip" data-placement="top" title="Año-Mes-Dia   Hora:Minutos:Segundos">
					</div>
					<div class="col-md-4 mb-3">
					  <label for="apellido_proveedores">Hora</label>
					  <select id="hora_nueva" name="hora_nueva" class="form-control" required="required">
					  </select>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="rtn_proveedores">Fecha Cita Inicio</label>
						 <input type="text" name="fecha_citaedit1" class="form-control" id="fecha_citaedit1" data-toggle="tooltip" data-placement="top" title="Año-Mes-Dia Hora:Minutos:Segundos" readonly="readonly">
					</div>					
				</div>								
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="apellido_proveedores">Fecha Cita End</label>
					  <input type="text" name="fecha_citaeditend" class="form-control" id="fecha_citaeditend" data-toggle="tooltip" data-placement="top" title="Año-Mes-Dia Hora:Minutos:Segundos" readonly="readonly">
					</div>
					<div class="col-md-4 mb-3">
					  <label for="rtn_proveedores">Hora</label>
					  <input type="time" name="hora_citaeditend" class="form-control" id="hora_citaeditend" placeholder="Hora" readonly >
					</div>	
					<div class="col-md-4 mb-3">
					  <label for="nombre_proveedores">Color</label>
					  <select name="color" class="form-control" id="color" readonly="readonly">-
						  <option value="">Choose</option>
						  <option style="color:#0071c5;" value="#0071c5">&#9724; Azul Oscuro</option><!--Usuarios Subsiguientes-->
						  <option style="color:#008000;" value="#008000">&#9724; Verde</option><!--Usuarios Nuevos-->	
                          <option style="color:#DF0101;" value="#DF0101">&#9724; Rojo</option><!--Usuarios Precargados-->	
						  <option style="color:#824CC8;" value="#824CC8">&#9724; Morado</option><!--Usuarios Extemporaneos-->
                          <option style="color:#FF5733;" value="#FF5733">&#9724; Naranja</option><!--Usuarios Reprogramados-->
                          <option style="color:#B7950B;" value="#B7950B">&#9724; Amarillo</option><!--Usuarios con mas de 5 años-->						  
					  </select>
					</div>					
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="apellido_proveedores">Profesional</label>
					  <div class="input-group mb-3">
							  <select id="colaborador" name="colaborador" class="form-control" data-toggle="tooltip" data-placement="top" title="Profesional" required ></select>
							  <div class="input-group-append" id="buscar_profesional">				
								<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
							  </div>
						   </div>						  
					</div>
					<div class="col-md-8 mb-3">
					  <label for="rtn_proveedores">Observación</label>
					  <input type="text" name="coment1" class="form-control" id="coment1" placeholder="Observación" readonly="readonly">
					</div>					
				</div>
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label for="rtn_proveedores">Comentario</label>
					  <textarea rows="4" cols="50" name="coment_1" class="form-control" id="coment_1" placeholder="Comentario" readonly="readonly">
					  </textarea>
					</div>					
				</div>
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label for="rtn_proveedores">Comentario</label>
					  <input type="text" name="coment" class="form-control" id="coment" placeholder="Comentario" required="required">
					</div>					
				</div>				
			   <div class="form-group form-check-inline">
				  <div class="col-md-12 mb-3">
					 <input type="checkbox" name="checkeliminar" class="checkbox-inline" id="checkeliminar" placeholder="Comentario" value="1">
					 <label class="form-check-label" for="exampleCheck1">Eliminar</label>				
				  </div>						
			   </div>					 
			</form>
        </div>	
		<div class="modal-footer">
			<button class="btn btn-warning ml-2" type="submit" id="ModalEdit_enviar" form="form-editevent"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Modificar</button>	
			<button class="btn btn-danger ml-2" type="submit" id="ModalDelete_enviar" form="form-editevent"><div class="sb-nav-link-icon"></div><i class="fas fa-trash fa-lg"></i> Eliminar</button>	
			<button class="btn btn-dark ml-2" type="submit" id="ModalImprimir_enviar" form="form-editevent"><div class="sb-nav-link-icon"></div><i class="fas fa-print fa-lg"></i> Imprimir</button>			
		</div>			
      </div>
    </div>
</div>
<!--FIN MODAL EDITAR CITAS-->

<!--INICIO MODAL AUSENCIAS-->
<div class="modal fade" id="registrar_ausencias">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Ausencias</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formulario_ausencias" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">	
				<input type="hidden" id="clientes_id" name="clientes_id" class="form-control">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
						<div class="input-group mb-3">
						    <input type="hidden" id="clientes_id" name="clientes_id" class="form-control">
							<input type="text" id="pro_ausencias" name="pro_ausencias" class="form-control" readonly>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>	
					</div>				
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="nombre_proveedores">Profesional</label>
					   <select id="medico_ausencia" name="medico_ausencia" class="form-control" data-toggle="tooltip" data-placement="top" title="Seleccione">		   
					   </select>
					</div>
					<div class="col-md-8 mb-3">
					  <label for="apellido_proveedores">Comentario</label>
					 <input type="text" name="comentario_ausencias" id="comentario_ausencias" class="form-control" size="280">
					</div>					
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="nombre_proveedores">Fecha Inicio</label>
					  <input type="date" name="fecha_ausencia" id="fecha_ausencia" class="form-control" value="<?php echo date ("Y-m-d");?>">
					</div>
					<div class="col-md-4 mb-3">
					  <label for="apellido_proveedores">Fecha Fin</label>
					  <input type="date" name="fecha_ausenciaf" id="fecha_ausenciaf" class="form-control" value="<?php echo date ("Y-m-d");?>">
					</div>					
				</div>								
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  	<div class="registros" id="agrega-registros_ausencias"></div>
						</div>
					    <center>	
						   <ul class="pagination" id="pagination"></ul>
					    </center>							
				</div>									  
			</form>
        </div>	
		<div class="modal-footer">
			<button class="btn btn-success ml-1" type="submit" id="reg_ausencias" form="formulario_ausencias"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-info ml-1" type="submit" id="reg_buscarausencias" form="formulario_ausencias"><div class="sb-nav-link-icon"></div><i class="fas fa-sync-alt fa-lg"></i> Buscar</button>		
		</div>			
		
      </div>
    </div>
</div>
<!--FIN MODAL AUSENCIAS-->

<!--INICIO MODAL BUSCAR CITAS PENDIENTES-->
<div class="modal fade" id="buscarCita" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buscar Citas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="form-buscarcita">		
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <input type="text" placeholder="Buscar por: Expediente, Paciente, Medico/Psicólogo o Identidad" id="bs-regis" autofocus class="form-control"/>
					</div>					
				</div>
				<div class="form-row">
				  <div class="col-md-12 mb-3 overflow-auto">
					 <div class="registros" id="agrega-registros"></div>
				  </div>		   	
					<nav aria-label="Page navigation example">
						<ul style="align: center;" class="pagination justify-content-center" id="pagination"></ul>
					</nav>
				</div>																  
			</form>
      </div>	  
    </div>
  </div>
</div>
<!--FIN MODAL BUSCAR CITAS PENDIENTES-->

<!--INICIO MODAL BUSCAR HISTORIAL DE CITAS ATENDIDAS-->
<div class="modal fade" id="buscarHistorial" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buscar Historial de Citas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form-buscarhistorial">
			<div class="form-row">
				<div class="col-md-12 mb-3">
				  <input type="text" placeholder="Buscar por: Expediente, Paciente, Medico/Psicólogo o Identidad" id="bs-regis" autofocus class="form-control"/>
				</div>					
			</div>
			<div class="form-row">
			  <div class="col-md-12 mb-3 overflow-auto">
				 <div class="registros" id="agrega-registros"></div>
			  </div>		   	
				<nav aria-label="Page navigation example">
					<ul style="align: center;" class="pagination justify-content-center" id="pagination"></ul>
				</nav>
			</div>	
        </form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL BUSCAR HISTORIAL DE CITAS ATENDIDAS-->

<!--INICIO MODAL BUSCAR HISTORIAL DE REPROGRAMACION DE CITAS-->
<div class="modal fade" id="buscarHistorialReprogramaciones" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buscar Historial de Reprogramaciones</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<form id="form_buscarhistorial_reprogramaciones">			
			<div class="form-row">
				<div class="col-md-12 mb-3">
				  <input type="text" placeholder="Buscar por: Expediente, Paciente, Medico/Psicólogo o Identidad" id="bs-regis" autofocus class="form-control"/>
				</div>					
			</div>
			<div class="form-row">
			  <div class="col-md-12 mb-3 overflow-auto">
				 <div class="registros" id="agrega-registros"></div>
			  </div>		   	
				<nav aria-label="Page navigation example">
					<ul style="align: center;" class="pagination justify-content-center" id="pagination"></ul>
				</nav>
			</div>																	  
		</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL BUSCAR HISTORIAL DE REPROGRAMACION DE CITAS-->

<!--INICIO MODAL BUSCAR HISTORIAL DE AUSENCIA DE USUARIOS-->
<div class="modal fade" id="buscarHistorialNo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buscar Historial de Usuarios que no se presentaron a su cita</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="form-buscarhistorialno">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <input type="text" placeholder="Buscar por: Expediente, Paciente, Medico/Psicólogo o Identidad" id="bs-regis" autofocus class="form-control"/>
					</div>					
				</div>
				<div class="form-row">
				  <div class="col-md-12 mb-3 overflow-auto">
					 <div class="registros" id="agrega-registros"></div>
				  </div>		   	
					<nav aria-label="Page navigation example">
						<ul style="align: center;" class="pagination justify-content-center" id="pagination"></ul>
					</nav>
				</div>																	  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL BUSCAR HISTORIAL DE AUSENCIA DE USUARIOS-->