<!--INICIO MODAL CIERRE DE CAJA-->
<div class="modal fade" id="modalCierreCaja">
	<div class="modal-dialog modal-sm modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Cierre de Caja</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formularioCierreCaja" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>	 
					</div>							
				</div>	
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label>Fecha <span class="priority">*<span/></label>
					  <input type="date" required id="fechaCierreCaja" name="fechaCierreCaja" value="<?php echo date ("Y-m-d");?>" class="form-control" />					  
					</div>									
				</div>					
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="generarCierreCaja" form="formularioCierreCaja"><div class="sb-nav-link-icon"></div><i class="fas fa-cash-register fa-lg"></i> Generar</button>		
		</div>			
      </div>
    </div>
</div>
<!--FIN MODAL CIERRE DE CAJA-->

<!--INICIO MODAL MOVIMIENTO DE PRODUCTOS-->
<div class="modal fade" id="modal_movimientos">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Movimiento de Productos</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">
			<form class="FormularioAjax" id="formularioMovimientos" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" id="movimientos_id " name="movimientos_id " class="form-control"/>
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
						<label for="categoria">Categoría<span class="priority">*<span/></label>
						<div class="input-group mb-3">
							<select class="selectpicker" id="movimiento_categoria" name="movimiento_categoria" required data-live-search="true" title="Categoría" data-size="10">
							</select>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="categoria">Productos<span class="priority">*<span/></label>
						<div class="input-group mb-3">
							<select class="selectpicker" id="movimiento_producto" name="movimiento_producto" required data-live-search="true" title="Productos" data-size="10">
							</select>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<label for="categoria">Tipo Operación<span class="priority">*<span/></label>
						<div class="input-group mb-3">
							<select class="selectpicker" id="movimiento_operacion" name="movimiento_operacion" required data-live-search="true" title="Tipo Operación" data-size="10">
							</select>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						  <label>Cantidad <span class="priority">*<span/></label>
						  <input type="number" required id="movimiento_cantidad" name="movimiento_cantidad" class="form-control" required>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-12 mb-3">
								<div class="card">
								<div class="card-header text-white bg-info mb-3" align="center">
									Comentario
								</div>
								<div class="card-body">
									<div class="form-row">
										<div class="col-md-12 mb-3">
											<div class="input-group">
											<textarea id="comentario" name="comentario" placeholder="Comentario" class="form-control" maxlength="1000" rows="7"></textarea>
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="btn btn-outline-success fas fa-microphone-alt" id="search_comentario_movimientos_start"></i>
													<i class="btn btn-outline-success fas fa-microphone-slash" id="search_comentario_movimientos_stop"></i>
												</span>
											</div>
											</div>
											<p id="charNum_comentario">1000 Caracteres</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="modal_movimientos" form="formularioMovimientos"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
		</div>
      </div>
    </div>
</div>
<!--FIN MODAL MOVIMIENTO DE PRODUCTOS-->

<!--INICIO MODAL PARA INGRESO DE USUARIOS-->
<div class="modal fade" id="registrar">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Registrar Usuarios</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formulario" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" id="id-registro" name="id-registro" class="form-control"/>
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>	 
					</div>							
				</div>
				<div class="form-row" id="grupo_expediente">
					<div class="col-md-8 mb-3">
					  <label>Colaborador <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						<select id="colaborador" name="colaborador" class="form-control" data-toggle="tooltip" data-placement="top" title="Consultorio" required></select>
						<div class="input-group-append" id="buscar_colaboradores">				
						  <a data-toggle="modal" href="#" class="btn btn-outline-success"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
					    </div>
					  </div>					  
					</div>
					<div class="col-md-4 mb-3">
					  <label for="edad">Estado <span class="priority">*<span/></label>
					  <select id="estatus" name="estatus" class="form-control" data-toggle="tooltip" data-placement="top" title="Estatus" required>		   
						 <option value="">Seleccione</option>
						 <option value="1">Activo</option>
						 <option value="2">Inactivo</option>						 
					  </select>
					</div>				
				</div>				
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="nombre">Nickname <span class="priority">*<span/></label>
					  <input type="text" required name="username" id="username" maxlength="100" class="form-control"/>
					</div>
					<div class="col-md-8 mb-3">
					  <label for="apellido">Email <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="email" required name="email" id="email" maxlength="100" class="form-control" required />
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-at fa-lg"></i></span>
							</div>
					   </div>					  
					</div>					
				</div>	
				<div class="form-row">
					<div class="col-md-6 mb-3">
					  <label for="fecha">Empresa <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						<select id="empresa" name="empresa" class="form-control" data-toggle="tooltip" data-placement="top" title="Empresa" required></select>
						<div class="input-group-append" id="buscar_empresa">				
						  <a data-toggle="modal" href="#" class="btn btn-outline-success"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
					    </div>
					  </div>					  
					</div>				
					<div class="col-md-6 mb-3">
					  <label for="sexo">Tipo <span class="priority">*<span/></label>
					  <select class="form-control" id="tipo" name="tipo" required>			  
					  </select>
					</div>					
				</div>				
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" form="formulario" type="submit" id="reg_usuarios"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>			
		</div>			
      </div>
    </div>
</div>

<div class="modal fade" id="registrar_editar">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Registrar Usuarios</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formulario_editar" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" id="id-registro1" name="id-registro1" class="form-control"/>	
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>	 
					</div>							
				</div>
				<div class="form-row" id="grupo_expediente">
					<div class="col-md-8 mb-3">
					  <label>Colaborador</label>
					  <div class="input-group mb-3">
						<select id="colaborador1" name="colaborador1" class="form-control" data-toggle="tooltip" data-placement="top" title="Consultorio"></select>
						<div class="input-group-append" id="buscar_colaborador_editar">				
						  <a data-toggle="modal" href="#" class="btn btn-outline-success" id="buscar_colaboradores_editar"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
					    </div>
					  </div>					  
					</div>
					<div class="col-md-4 mb-3">
					  <label for="edad">Estado <span class="priority">*<span/></label>
					  <select id="estatus1" name="estatus1" class="form-control" data-toggle="tooltip" data-placement="top" title="Estatus" required>		   
						 <option value="">Seleccione</option>
						 <option value="1">Activo</option>
						 <option value="2">Inactivo</option>						 
					  </select>
					</div>				
				</div>				
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label for="apellido">Email <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="email" required name="email1" id="email1" maxlength="100" class="form-control" required />
						  <div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-at fa-lg"></i></span>
							</div>
					   </div>
					</div>						
				</div>
				<div class="form-row">
					<div class="col-md-6 mb-3">
					  <label for="fecha">Empresa <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						<select id="empresa1" name="empresa1" class="form-control" data-toggle="tooltip" data-placement="top" title="Empresa" required></select>
						<div class="input-group-append" id="buscar_empresa_editar">				
						  <a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
					    </div>
					  </div>					  
					</div>	
					<div class="col-md-6 mb-3">
					  <label for="sexo">Tipo <span class="priority">*<span/></label>
					  <select class="form-control" id="tipo1" name="tipo1" required>			  
					  </select>
					</div>						
				</div>							
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" form="formulario_editar" type="submit" id="editar_usuarios"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>			
		</div>			
      </div>
    </div>
</div>

<!--INICIO MODAL PARA EL INGRESO DE PRECLINICA-->
<div class="modal fade" id="agregar_preclinica">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Confirmación</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">		
			<form class="FormularioAjax" id="formulario_agregar_preclinica" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">			
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" required="required" readonly id="id-registro" name="id-registro" readonly="readonly"/>
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
					  <label for="edad">Expediente <span class="priority">*<span/></label>
					  <input type="number" required id="expediente" placeholder="Expediente o Identidad" name="expediente" class="form-control"/>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="edad">Fecha <span class="priority">*<span/></label>
					  <input type="date" required readonly id="fecha" name="fecha" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
					</div>	
					<div class="col-md-4 mb-3">
					  <label for="edad">Identidad </label>
					  <input type="text" readonly id="identidad" name="identidad" class="form-control"/>
					</div>						
				</div>				
				<div class="form-row">
					<div class="col-md-6 mb-3">
					  <label for="nombre">Nombre</label>
					  <input type="text" required readonly id="nombre" name="nombre" class="form-control"/>
					</div>	
					<div class="col-md-6 mb-3">
					  <label for="nombre">Profesional</label>
					  <input type="text" readonly id="profesional_consulta" name="profesional_consulta" class="form-control"/>
					</div>						
				</div>						
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="edad">Presión Arterial (PA)</label>
					  <input type="text"  id="pa" name="pa" class="form-control" placeholder="Presión Arterial"/>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="edad">Frecuencia Respiratoria (FR)</label>
					  <input type="text" id="fr" name="fr" class="form-control" step="0.01" placeholder="Frecuencia Respiratoria"/>
					</div>	
					<div class="col-md-4 mb-3">
					  <label for="edad">Frecuencia Cardiaca </label>
					  <input type="text" id="fc" name="fc" class="form-control" placeholder="Frecuencia Cardiaca"/>
					</div>						
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="edad">Temperatura</label>
					  <input type="text" id="temperatura" name="temperatura" step="0.01" class="form-control" placeholder="Temperatura"/>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="edad">Peso</label>
					  <input type="text" id="peso" name="peso" class="form-control" placeholder="Peso"/>
					</div>	
					<div class="col-md-4 mb-3">
					  <label for="edad">Talla</label>
					  <input type="text" id="talla" name="talla" class="form-control" placeholder="Talla"/>
					</div>						
				</div>	
				<div class="form-row" id="grupo">
					<div class="col-md-6 mb-3">
						<label>Consultorio</label>
						<div class="input-group mb-3">
						  <select id="servicio" name="servicio" class="form-control" data-toggle="tooltip" data-placement="top" title="Consultorio"></select>
						  <div class="input-group-append" id="buscar_servicios_preclinica">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
						  </div>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<label>Profesional</label>
						<div class="input-group mb-3">
						  <select id="medico" name="medico" class="form-control" data-toggle="tooltip" data-placement="top" title="Profesional"></select>
						  <div class="input-group-append" id="buscar_profesionales_preclinica">				
							<a data-toggle="modal" href="#" class="btn btn-outline-success" id="servicio_boton"><div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i></a>
						  </div>
						</div>
					</div>								
				</div>	
				<div class="form-row">
					<div class="col-md-12 mb-3">
						<label>Observaciones</label>
						<input type="text" id="observaciones" name="observaciones" class="form-control" placeholder="Observaciones"/>
					</div>							
				</div>					
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" form="formulario_agregar_preclinica" type="submit" id="reg_preclinica"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>	
			<button class="btn btn-primary ml-2" form="formulario_agregar_preclinica" type="submit" id="edit_preclinica"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>				
		</div>			
      </div>
    </div>
</div>
<!--FIN MODAL PARA EL INGRESO DE PRECLINICA-->

<!--INICIO MODAL DEPARTAMENTOS-->
<div class="modal fade" id="modal_busqueda_departamentos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda Departamentos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_departamentos">		
				<div class="table-responsive">
					<table id="dataTableDepartamentos" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>Departamento</th>					
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL DEPARTAMENTOS-->

<!--INICIO MODAL PAIS-->
<div class="modal fade" id="modal_busqueda_pais" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda de Paises</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_pais">		
				<div class="table-responsive">
					<table id="dataTablePais" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>País</th>					
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL DEPARTAMENTOS-->

<!--INICIO MODAL MUNICIPIOS-->
<div class="modal fade" id="modal_busqueda_municipios" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda Municipios</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_municipios">		
				<div class="table-responsive">
					<table id="dataTableMunicipios" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>Departamento</th>
								<th>Municipio</th>								
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL MUNICIPIOS-->

<!--INICIO MODAL SERVICIOS-->
<div class="modal fade" id="modal_busqueda_servicios" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda de Consultorios</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_servicios">		
				<div class="table-responsive">
					<table id="dataTableServicios" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>Consultorio</th>					
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL SERVICIOS-->

<!--INICIO MODAL PROFESION-->
<div class="modal fade" id="modal_busqueda_profesion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda de Profesiones</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_profesion">		
				<div class="table-responsive">
					<table id="dataTableProfesiones" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>Profesión</th>					
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL PROFESION-->

<!--INICIO MODAL RELIGION-->
<div class="modal fade" id="modal_busqueda_religion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda de Religiones</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_religion">		
				<div class="table-responsive">
					<table id="dataTableReligion" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>Religión</th>					
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL RELIGION-->

<!--INICIO MODAL PACIENTES-->
<div class="modal fade" id="modal_busqueda_pacientes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda de Pacientes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_pacientes">		
				<div class="table-responsive">
					<table id="dataTablePacientes" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>Paciente</th>
								<th>Identidad</th>
								<th>Expediente</th>
								<th>Correo</th>						
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL PACIENTES-->

<!--INICIO MODAL PACIENTES-->
<div class="modal fade" id="modal_busqueda_colaboradores" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda de Colaboradores</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_coloboradores">		
				<div class="table-responsive">
					<table id="dataTableColaboradores" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>Colaborador</th>
								<th>Identidad</th>
								<th>Puesto</th>					
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL PACIENTES-->

<!--INICIO MODAL PACIENTES-->
<div class="modal fade" id="modal_busqueda_empresa" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda de Empresas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_empresa">		
				<div class="table-responsive">
					<table id="dataTableEmpresa" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>Empresa</th>
								<th>RTN</th>
								<th>Dirección</th>					
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL PACIENTES-->

<!--INICIO MODAL PRODUCTOS-->
<div class="modal fade" id="modal_busqueda_productos_facturas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Búsqueda de Productos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<form id="formulario_busqueda_productos_facturas">
			<input type="hidden" id="row" name="row" class="form-control"/>
			<input type="hidden" id="col" name="col" class="form-control"/>			
				<div class="table-responsive">
					<table id="dataTableProductosFacturas" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead align="center">
							<tr>
								<th>Seleecionar</th>
								<th>Producto</th>
								<th>Descripción</th>
								<th>Concentración</th>	
								<th>Medida</th>						
								<th>Cantidad</th>
								<th>Precio Venta</th>						
							</tr>
						</thead>
					</table>  
				</div>			
			  </div>															  
			</form>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL PRODUCTOS-->

<!--INICIO MODAL PARA EL INGRESO DE PRODUCTOS-->
<div class="modal fade" id="modal_productos">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Productos</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="form-horizontal FormularioAjax" id="formulario_productos" action="" method="POST" data-form="" enctype="multipart/form-data">				
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" required="required" id="productos_id" name="productos_id"/>
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
					  <label>Producto <span class="priority">*<span/></label>
					  <input type="text" required class="form-control" name="nombre" id="nombre" placeholder="Producto o Servicio" maxlength="150" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" />
					</div>
					<div class="col-md-3 mb-3">
					  <label>Tipo Producto <span class="priority">*<span/></label>
					  <select id="categoria" name="categoria" class="form-control" data-toggle="tooltip" data-placement="top" title="Tipo Producto" required>   				   
					  </select> 
					</div>	
					<div class="col-md-3 mb-3">
					  <label>Concentración <span class="priority">*<span/></label>
				      <input type="number" required class="form-control" name="concentracion" id="concentracion" placeholder="Concentracion" maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"/>
					</div>					
				</div>				
				<div class="form-row">				
					<div class="col-md-3 mb-3">
					  <label>Medida <span class="priority">*<span/></label>
					  <select id="medida" name="medida" class="form-control" data-toggle="tooltip" data-placement="top" title="Medida" required>   				   
					  </select>
					</div>
					<div class="col-md-3 mb-3">
						<label>Almacén <span class="priority">*<span/></label>
						<select id="almacen" name="almacen" class="form-control" data-toggle="tooltip" data-placement="top" title="Almacén" required>   				   
						</select> 
					</div>				
					<div class="col-md-3 mb-3">
					  <label>Cantidad <span class="priority">*<span/></label>
					  <input type="number" required id="cantidad" name="cantidad" placeholder="Cantidad" class="form-control"/>
					</div>
					<div class="col-md-3 mb-3">
					  <label>Precio de Compra <span class="priority">*<span/></label>
					  <input type="number" required id="precio_compra" name="precio_compra" step="0.01" placeholder="Precio Compra" class="form-control"/>
					</div>					
				</div>	
				<div class="form-row">
					<div class="col-md-3 mb-3">
					  <label>Precio de Venta <span class="priority">*<span/></label>
					  <input type="number" required id="precio_venta" name="precio_venta" step="0.01" placeholder="Precio Venta" class="form-control"/>
					</div>	
					<div class="col-md-3 mb-3">
					  <label>Cantidad Mínima</label>
					  <input type="number" id="cantidad_minima" name="cantidad_minima" placeholder="Cantidad Mínima" class="form-control"/>
					</div>	
					<div class="col-md-3 mb-3">
					  <label>Cantidad Máxima</label>
					  <input type="number" id="cantidad_maxima" name="cantidad_maxima" placeholder="Cantidad Máxima" class="form-control"/>
					</div>
					<div class="col-md-3 mb-3">
					  <label>Categoría <span class="priority">*<span/></label>
					  <select id="categoria_id" name="categoria_id" class="form-control" data-toggle="tooltip" data-placement="top" title="Categoría" required>   				   
					  </select> 
					</div>											
				</div>					
				<div class="form-row">			  
					<div class="col-md-12 mb-3">
					  <label>Descripción</label>
					  <textarea id="descripcion" name="descripcion" placeholder="Descripción" class="form-control" maxlength="100" rows="2"></textarea>	
				      <p id="charNum_descripcion">100 Caracteres</p>
					</div>
				</div>	
				
				<div class="form-group custom-control custom-checkbox custom-control-inline">	
				  <div class="col-md-5">			
						<label class="switch">
							<input type="checkbox" id="producto_activo" name="producto_activo" value="1" checked>
							<div class="slider round"></div>
						</label>
						<span class="question mb-2" id="label_producto_activo"></span>				
				  </div>				  	
				  <div class="col-md-8">		
						 <label class="form-check-label mr-1" for="defaultCheck1">¿ISV Venta?</label>
						<label class="switch">
							<input type="checkbox" id="producto_isv_factura" name="producto_isv_factura" value="1">
							<div class="slider round"></div>
						</label>
						<span class="question mb-2" id="label_producto_isv_factura"></span>				
				  </div>			  
				</div>										 				 
			</form>
        </div> 		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="reg_producto" form="formulario_productos"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-warning ml-2" type="submit" id="edi_producto" form="formulario_productos"><div class="sb-nav-link-icon"></div><i class="fas fa-edit fa-lg"></i> Editar</button>
			<button class="btn btn-danger ml-2" type="submit" id="delete_producto" form="formulario_productos"><div class="sb-nav-link-icon"></div><i class="fa fa-trash"></i> Eliminar</button>				
		</div>			
      </div>
    </div>
</div>
<!--FIN MODAL PARA EL INGRESO DE PRODUCTOS-->

<!--INICIO MODAL PARA EL INGRESO DE ALMACENES-->
<div class="modal fade" id="modal_almacen">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Almacén</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="form-horizontal FormularioAjax" id="formulario_almacen" action="" method="POST" data-form="" enctype="multipart/form-data">				
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" required="required" readonly id="almacen_id" name="almacen_id"/>
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
					  <label for="nombre_proveedores">Almacén <span class="priority">*<span/></label>
					  <input type="text" required class="form-control" name="almacen" id="almacen" placeholder="Almacén" maxlength="30" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"/>
					</div>
					<div class="col-md-6 mb-3">
					  <label for="apellido_proveedores">Ubicación <span class="priority">*<span/></label>
					  <select id="ubicacion" name="ubicacion" class="form-control" data-toggle="tooltip" data-placement="top" title="Ubicacion" required>   				   
					  </select>
					</div>					
				</div>																	  				
			</form>
        </div>	
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="reg_almacen" form="formulario_almacen"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-warning ml-2" type="submit" id="edi_almacen" form="formulario_almacen"><div class="sb-nav-link-icon"></div><i class="fas fa-edit fa-lg"></i> Editar</button>
			<button class="btn btn-danger ml-2" type="submit" id="delete_almacen" form="formulario_almacen"><div class="sb-nav-link-icon"></div><i class="fa fa-trash"></i> Eliminar</button>				
		</div>			
      </div>
    </div>
</div>
<!--FIN MODAL PARA EL INGRESO DE ALMACENES-->

<!--INICIO MODAL PARA EL INGRESO DE UBICACION-->
<div class="modal fade" id="modal_ubicacion">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Ubicación</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="form-horizontal FormularioAjax" id="formulario_ubicacion" action="" method="POST" data-form="" enctype="multipart/form-data">				
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" required="required" readonly id="ubicacion_id" name="ubicacion_id"/>
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
					  <label for="nombre_proveedores">Ubicación <span class="priority">*<span/></label>
					  <input type="text" required class="form-control" name="ubicacion" id="ubicacion" placeholder="Ubicación	" maxlength="30" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"/>
					</div>
					<div class="col-md-6 mb-3">
					  <label for="apellido_proveedores">Empresa <span class="priority">*<span/></label>
					  <select id="empresa" name="empresa" class="form-control" data-toggle="tooltip" data-placement="top" title="Empresa" required>   				   
					 </select>
					</div>					
				</div>				
			</form>
        </div>		
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="reg_ubicacion" form="formulario_ubicacion"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-warning ml-2" type="submit" id="edi_ubicacion" form="formulario_ubicacion"><div class="sb-nav-link-icon"></div><i class="fas fa-edit fa-lg"></i> Editar</button>
			<button class="btn btn-danger ml-2" type="submit" id="delete_ubicacion" form="formulario_ubicacion"><div class="sb-nav-link-icon"></div><i class="fa fa-trash"></i> Eliminar</button>				
		</div>			
      </div>
    </div>
</div>
<!--FIN MODAL PARA EL INGRESO DE UBICACION-->

<!--INICIO MODAL PARA EL INGRESO DE MEDIDAS-->
<div class="modal fade" id="modal_medidas">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Medidas</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="form-horizontal FormularioAjax" id="formulario_medidas" action="" method="POST" data-form="" enctype="multipart/form-data">				
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="text" required="required" readonly id="medida_id" name="medida_id"/>
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
					  <label>Medida <span class="priority">*<span/></label>
					  <input type="text" required id="medidas" name="medidas" placeholder="Medida" readonly class="form-control"  maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"/>
					</div>
					<div class="col-md-8 mb-3">
					  <label for="apellido_proveedores">Descripción <span class="priority">*<span/></label>
					  <input type="text" required id="descripcion_medidas" name="descripcion_medidas" placeholder="Descripción" readonly class="form-control"  maxlength="30" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"/>
					</div>					
				</div>																	  				
			</form>
        </div>	
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="reg_medidas" form="formulario_medidas"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-warning ml-2" type="submit" id="edi_medidas" form="formulario_medidas"><div class="sb-nav-link-icon"></div><i class="fas fa-edit fa-lg"></i> Editar</button>
			<button class="btn btn-danger ml-2" type="submit" id="delete_medidas" form="formulario_medidas"><div class="sb-nav-link-icon"></div><i class="fa fa-trash"></i> Eliminar</button>				
		</div>			
      </div>
    </div>
</div>
<!--FIN MODAL PARA EL INGRESO DE MEDIDAS-->

<!--INICIO MODAL PARA EL INGRESO DE CATEGORIAS-->
<div class="modal fade" id="modal_categoria">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">CAtegorías</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="form-horizontal FormularioAjax" id="formularioCategoria" action="" method="POST" data-form="" enctype="multipart/form-data">				
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" required="required" readonly id="categoria_id" name="categoria_id"/>
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>	 
					</div>							
				</div>
				<div class="form-row">
					<div class="col-md-12 mb-3">
					  <label>Categoria <span class="priority">*<span/></label>
					  <input type="text" required id="categoria" name="categoria" placeholder="Categoría" readonly class="form-control"  maxlength="30" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"/>
					</div>					
				</div>

				<div class="form-group custom-control custom-checkbox custom-control-inline">	
				  <div class="col-md-12">			
						<label class="switch">
							<input type="checkbox" id="categoria_activo" name="categoria_activo" value="1" checked>
							<div class="slider round"></div>
						</label>
						<span class="question mb-2" id="label_categoria_activo"></span>				
				  </div>			  
				</div>	

			</form>
        </div>	
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" type="submit" id="reg_categoria" form="formularioCategoria"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-warning ml-2" type="submit" id="edi_categoria" form="formularioCategoria"><div class="sb-nav-link-icon"></div><i class="fas fa-edit fa-lg"></i> Editar</button>
			<button class="btn btn-danger ml-2" type="submit" id="delete_categoria" form="formularioCategoria"><div class="sb-nav-link-icon"></div><i class="fa fa-trash"></i> Eliminar</button>				
		</div>			
      </div>
    </div>
</div>
<!--FIN MODAL PARA EL INGRESO DE CATEGORIAS-->

<!--INICIO MODAL PAGOS FACTURACION---->
<div class="modal fade" id="modal_pagos">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">	
			<div class="row justify-content-center">
				<div class="col-lg-12 col-12">
					<div class="card card0">
						<div class="d-flex" id="wrapper">
							<!-- Sidebar -->
							<div class="bg-light border-right" id="sidebar-wrapper">
								<div class="sidebar-heading pt-5 pb-4"><strong>Método de pago</strong></div>
								<div class="list-group list-group-flush"> 

									<a data-toggle="tab" href="#menu1" id="tab1" class="tabs list-group-item bg-light active1">
										<div class="list-div my-2">
											<div class="fas fa-money-bill-alt fa-lg"></div> &nbsp;&nbsp; Efectivo
										</div>
									</a> 
									<a data-toggle="tab" href="#menu2" id="tab2" class="tabs list-group-item">
										<div class="list-div my-2">
											<div class="far fa-credit-card fa-lg"></div> &nbsp;&nbsp; Tarjeta
										</div>
									</a> 		
									<a data-toggle="tab" href="#menu5" id="tab5" class="tabs list-group-item">
										<div class="list-div my-2">
											<div class="fa fa-pause fa-lg"></div> &nbsp;&nbsp; Mixto
										</div>
									</a> 																	
									<a data-toggle="tab" href="#menu3" id="tab3" class="tabs list-group-item bg-light">
										<div class="list-div my-2">
											<div class="fas fa-exchange-alt fa-lg"></div> &nbsp;&nbsp; Transferencia
										</div>
									</a> 
									<a data-toggle="tab" href="#menu4" id="tab4" class="tabs list-group-item bg-light">
										<div class="list-div my-2">
											<div class="fas fa-money-check fa-lg"></div> &nbsp;&nbsp; Cheque
										</div>
									</a>									
								</div>
							</div> <!-- Page Content -->
							<div id="page-content-wrapper">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<div class="row pt-3" id="border-btm">
									<div class="col-2">
										<i id="menu-toggle1" class="fas fa-angle-double-left fa-2x menu-toggle1"></i>
										<i id="menu-toggle2" class="fas fa-angle-double-right fa-2x menu-toggle2"></i>
									</div>
									<div class="col-10">
										<div class="row justify-content-right">
											<div class="col-12">
												<p class="mb-0 mr-4 mt-4 text-right" id="customer-name-bill"></p>
												<input type="hidden" name="customer_bill_pay" id="customer_bill_pay" placeholder="0.00">
											</div>
										</div>
										<div class="row justify-content-right">
											<div class="col-12">
												<p class="mb-0 mr-4 text-right color-text-white"><b>Pagar</b> <span class="top-highlight" id="bill-pay"></span> </p>
											</div>
										</div>
									</div>
								</div>
								<div class="row justify-content-center">
									<div class="text-center" id="test"></div>
								</div>
								<div class="tab-content">
									<div id="menu1" class="tab-pane in active">
										<div class="row justify-content-center">
											<div class="col-11">
												<div class="form-card">
													<h3 class="mt-0 mb-4 text-center">Ingrese detalles del Pago</h3>
													<form class="FormularioAjax" id="formEfectivoBill" action="<?php echo SERVERURL;?>php/facturacion/addPagoEfectivo.php" method="POST" data-form="save" autocomplete="off" enctype="multipart/form-data">
														<div class="row">
															<div class="col-11">
																<div class="input-group"> 	
																	<label for="monto_efectivo">Efectivo</label>
																	<input type="hidden" name="factura_id_efectivo" id="factura_id_efectivo"> 
																	<input type="hidden" name="monto_efectivo" id="monto_efectivo" placeholder="0.00"> 
																	<input type="number" name="efectivo_bill" id="efectivo_bill" class="inputfield" placeholder="0.00" step="0.01">																						
																</div>
															</div>
															<div class="col-11">
																<div class="input-group">
																	<label for="cambio_efectivo">Cambio</label>
																	<input type="number" readonly name="cambio_efectivo" id="cambio_efectivo" class="inputfield" step="0.01" placeholder="0.00">																
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12"> 
																<input type="submit" value="Efectuar Pago" id="pago_efectivo" class="pay btn btn-info placeicon" form="formEfectivoBill">
															</div>
														</div>
														<div class="RespuestaAjax"></div>
													</form>
												</div>
											</div>
										</div>
									</div>
									<div id="menu2" class="tab-pane">
										<div class="row justify-content-center">
											<div class="col-11">
												<div class="form-card">
													<h3 class="mt-0 mb-4 text-center">Ingrese detalles de la Tarjeta</h3>
													<form class="FormularioAjax" id="formTarjetaBill" method="POST" data-form="save" action="<?php echo SERVERURL;?>php/facturacion/addPagoTarjeta.php" autocomplete="off" enctype="multipart/form-data">
														<div class="row">
															<div class="col-12">
																<div class="input-group"> 
																<label>Número de Tarjeta</label> 
																<input type="hidden" name="factura_id_tarjeta" id="factura_id_tarjeta">
																<input type="text" id="cr_bill" name="cr_bill" class="inputfield"  placeholder="XXXX">
																<input type="hidden" name="monto_efectivo" id="monto_efectivo" placeholder="0.00">
																																
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-6">
																<div class="input-group"> 
																	<label> Fecha de Expiración</label>
																	<input type="text" name="exp" id="exp" class="mask inputfield" placeholder="MM/YY">
																</div>
															</div>
															<div class="col-6">
																<div class="input-group"> 
																	<label>Número Aprobación</label>
																	<input type="text" name="cvcpwd" id="cvcpwd" class="placeicon inputfield"> 																	 
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12"> 
																<input type="submit" value="Efectuar Pago" id="pago_tarjeta" class="pay btn btn-info placeicon" form="formTarjetaBill">
															</div>
														</div>
														<div class="RespuestaAjax"></div>
													</form>
												</div>
											</div>
										</div>
									</div>
									<div id="menu5" class="tab-pane">
										<div class="row justify-content-center">
											<div class="col-11">
												<div class="form-card">
													<h6 class="mt-0 mb-4 text-center">Ingrese Pago Mixto</h6>
													<form class="FormularioAjax" id="formMixtoBill" action="<?php echo SERVERURL;?>php/facturacion/addPagoMixto.php" method="POST" data-form="save" autocomplete="off" enctype="multipart/form-data">
														<div class="row">
															<div class="col-12 col-md-6">
																<div class="input-group"> 	
																	<label for="monto_efectivo">Efectivo</label>
																	<input type="hidden" name="factura_id_mixto" id="factura_id_mixto"> 
																	<input type="hidden" name="monto_efectivo" id="monto_efectivo_mixto" placeholder="0.00"> 
																	<input type="number" name="efectivo_bill" id="efectivo_bill_mixto" class="inputfield" placeholder="0.00" step="0.01">																						
																	<input type="hidden" readonly name="cambio_efectivo" id="cambio_efectivo_mixto" class="inputfield" step="0.01" placeholder="0.00">																
																</div>
															</div>
															
															<div class="col-12 col-md-6">
																<div class="input-group">
																	<label for="monto_tarjeta">Tarjeta</label>
																	<input type="number" readonly name="monto_tarjeta" id="monto_tarjeta" class="inputfield" step="0.01" placeholder="0.00">																
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12">
																<div class="input-group"> 
																<label>Número de Tarjeta</label> 
																<input type="text" id="cr_bill_mixto" name="cr_bill" class="inputfield"  placeholder="XXXX">
																																																
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-6">
																<div class="input-group"> 
																	<label> Fecha de Expiración</label>
																	<input type="text" name="exp" id="exp_mixto" class="mask inputfield" placeholder="MM/YY">
																</div>
															</div>
															<div class="col-6">
																<div class="input-group"> 
																	<label>Número Aprobación</label>
																	<input type="text" name="cvcpwd" id="cvcpwd_mixto" class="placeicon inputfield"> 																	 
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12"> 
																<input type="submit" value="Efectuar Pago" id="pago_efectivo_mixto" class="pay btn btn-info placeicon" form="formMixtoBill">
															</div>
														</div>
														<div class="RespuestaAjax"></div>
													</form>
												</div>
											</div>
										</div>
									</div>									
									<div id="menu3" class="tab-pane">
										<div class="row justify-content-center">
											<div class="col-11">
												<div class="form-card">
													<h3 class="mt-0 mb-4 text-center">Ingrese detalles de la Transferencia</h3>
													<form class="FormularioAjax" id="formTransferenciaBill" method="POST" data-form="save" action="<?php echo SERVERURL;?>php/facturacion/addPagoTransferencia.php" autocomplete="off" enctype="multipart/form-data">
														<div class="row">
															<div class="col-12">
															    <label>Banco</label> 
																<div class="input-group"> 																	
																	<input type="hidden" name="factura_id_transferencia" id="factura_id_transferencia">
																	<select required name="bk_nm" id="bk_nm" class="custom-select inputfield" data-toggle="tooltip" data-placement="top" title="Banco">
																		<option value="">Seleccione un Banco</option>
																	</select> 																	
																	<input type="hidden" name="monto_efectivo" id="monto_efectivo" placeholder="0.00">								
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12">
																<div class="input-group"> 	
																	<label>Número de Autorización</label> 
																	<input type="text" name="ben_nm" id="ben_nm" class="inputfield" placeholder="Número de Autorización">							
																</div>
															</div>
															<div class="col-12" style="display: none;">
																<div class="input-group"> 																	
																	<input type="text" name="scode" placeholder="ABCDAB1S" class="placeicon" minlength="8" maxlength="11"> 
																	<label>SWIFT CODE</label> 
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12"> 
																<input type="submit" value="Efectuar Pago" id="pago_transferencia" class="pay btn btn-info placeicon" form="formTransferenciaBill"> 
															</div>
														</div>
														<div class="RespuestaAjax"></div>
													</form>
												</div>
											</div>
										</div>
									</div>		
									
									<div id="menu4" class="tab-pane">
										<div class="row justify-content-center">
											<div class="col-11">
												<div class="form-card">
													<h3 class="mt-0 mb-4 text-center">Ingrese detalles del Cheque</h3>
													<form class="FormularioAjax" id="formChequeBill" method="POST" data-form="save" action="<?php echo SERVERURL;?>php/facturacion/addPagoCheque.php" autocomplete="off" enctype="multipart/form-data">
														<div class="row">
															<div class="col-12">
															    <label>Banco</label> 
																<div class="input-group"> 																	
																	<input type="hidden" name="factura_id_cheque" id="factura_id_cheque">
																	<select required name="bk_nm_chk" id="bk_nm_chk" class="custom-select inputfield" data-toggle="tooltip" data-placement="top" title="Banco">
																		<option value="">Seleccione un Banco</option>
																	</select> 																	
																	<input type="hidden" name="monto_efectivo" id="monto_efectivo" placeholder="0.00">								
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12">
																<div class="input-group"> 	
																	<label>Número de Cheque</label> 
																	<input type="text" name="check_num" id="check_num" class="inputfield" placeholder="Número de Cheque">							
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12"> 
																<input type="submit" value="Efectuar Pago" id="pago_transferencia" class="pay btn btn-info placeicon" form="formChequeBill"> 
															</div>
														</div>
														<div class="RespuestaAjax"></div>
													</form>
												</div>
											</div>
										</div>
									</div>
																		
									<div id="menu4" class="tab-pane">
										<div class="row justify-content-center">
											<div class="col-11">
												<h3 class="mt-0 mb-4 text-center">Scan the QR code to pay</h3>
												<div class="row justify-content-center">
													<div id="qr"> <img src="" width="200px" height="200px"> </div>
												</div>
											</div>
										</div>
									</div>
									<div id="menu4" class="tab-pane">
										<div class="row justify-content-center">
											<div class="col-11">
												<h3 class="mt-0 mb-4 text-center">Otra forma de pago</h3>
												<div class="row justify-content-center">
													<div id="qr"> <img src="" width="200px" height="200px"> </div>
												</div>
											</div>
										</div>
									</div>									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>				
      </div>
    </div>
</div>
<!--FIN MODAL PAGOS FACTURACION-->

<!--INICIO MODAL PARA FORMULARIO DESCENTOS EN FACTURACION-->
<div class="modal fade" id="modalDescuentoFacturacion">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Descuento</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">		
			<form class="form-horizontal" id="formDescuentoFacturacion" action="" method="POST" data-form="" enctype="multipart/form-data">				
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" required="required" readonly id="descuento_productos_id" name="descuento_productos_id"/>
						<input type="hidden" required="required" readonly id="row_index" name="row_index" class="form-control"/>
						<input type="hidden" required="required" readonly id="col_index" name="col_index" class="form-control"/>
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro_descuento_fact" name="pro_descuento_fact" class="form-control"/>
							<div class="input-group-append">				
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square fa-lg"></i></span>
							</div>
						</div>	 
					</div>							
				</div>
				<div class="form-row">
					<div class="col-md-8 mb-3">
					  <label for="producto_descuento_fact">Producto <span class="priority">*<span/></label>
					  <input type="text" readonly required id="producto_descuento_fact" name="producto_descuento_fact" placeholder="Producto" class="form-control" maxlength="11" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"/>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="precio_descuento_fact">Precio <span class="priority">*<span/></label>
					  <input type="text" readonly required id="precio_descuento_fact" name="precio_descuento_fact" placeholder="Precio" class="form-control"  maxlength="30" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" step="0.01"/>
					</div>					
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="porcentaje_descuento_fact">% Descuento <span class="priority">*<span/></label>
					  <input type="text" required id="porcentaje_descuento_fact" name="porcentaje_descuento_fact" placeholder="Porcentaje de Descuento" class="form-control" maxlength="11" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"/>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="descuento_fact">Valor Descuento <span class="priority">*<span/></label>
					  <input type="text" required id="descuento_fact" name="descuento_fact" placeholder="Descuento" class="form-control"  maxlength="30" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" step="0.01"/>
					</div>				
				</div>					
				<div class="RespuestaAjax"></div> 
			</form>
        </div>	
		<div class="modal-footer">
			<button class="guardar btn btn-primary ml-2" type="submit" id="reg_DescuentoFacturacion" form="formDescuentoFacturacion"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>				
		</div>			
      </div>
    </div>
</div>
<!--FIN MODAL PARA FORMULARIO DESCENTOS EN FACTURACION-->