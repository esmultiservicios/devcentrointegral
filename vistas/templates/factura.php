<div class="table-responsive" id="facturacion" style="display: none;">	
	<form class="invoice-form FormularioAjax" id="formulario_facturacion" action="" method="POST" data-form="" enctype="multipart/form-data">
	  <div class="form-group row">
	  	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	  		<button class="btn btn-primary" type="submit" id="validar" data-toggle="tooltip" data-placement="top" title="Cobrar la Factura"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Cobrar</button>
		    <button class="btn btn-secondary" type="submit" id="guardar" data-toggle="tooltip" data-placement="top" title="Guardar la Factura"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Guardar</button>
		    <button class="btn btn-secondary" type="submit" id="guardar1" data-toggle="tooltip" data-placement="top" title="Guardar la Factura"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Guardar</button>														
		</div>		
	  </div>				  
	  <div class="form-group row">
		<label for="inputCliente" class="col-sm-1 col-form-label-md">Paciente <span class="priority">*<span/></label>
		<div class="col-sm-5">
			<div class="input-group mb-3">
				<select class="selectpicker" id="pacientes_id" name="pacientes_id"
					data-live-search="true" title="Paciente" data-width="100%" data-size="7">
				</select>				
			  <input type="hidden" class="form-control" id="facturas_id" name="facturas_id" readonly required>
			</div>	
		</div>
		<label for="inputFecha" class="col-sm-1 col-form-label-md">Fecha <span class="priority">*<span/></label>
		<div class="col-sm-3">
		  <input type="date" class="form-control" value="<?php echo date('Y-m-d');?>" id="fecha" name="fecha" readonly>
		</div>					
	  </div>
	  <div class="form-group row">
		<label for="inputCliente" class="col-sm-1 col-form-label-md">Profesional <span class="priority">*<span/></label>
		<div class="col-sm-5">
			<div class="input-group mb-3">
				<select class="selectpicker" id="colaborador_id" name="colaborador_id"
					data-live-search="true" title="Profesional" data-width="100%" data-size="7">
				</select>	
			</div>
		</div>
		<label for="inputFecha" class="col-sm-1 col-form-label-md">Servicio <span class="priority">*<span/></label>
		<div class="col-sm-3">
			<div class="input-group mb-3">
				<select class="selectpicker" id="servicio_id" name="servicio_id"
					data-live-search="true" title="Servicio" data-width="100%" data-size="7">
				</select>
			</div>
		</div>	
		<label class="switch mb-2" data-toggle="tooltip" data-placement="top" title="Tipo de Factura, Contado o Crédito">
			<input type="checkbox" id="facturas_activo" name="facturas_activo" value="1" checked>
			<div class="slider round"></div>
		</label>
		<span class="question mb-2" id="label_facturas_activo"></span>			
	  </div>				  
	  <div class="form-group row table-responsive-xl tableFixHead table table-hover">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table class="table table-bordered table-hover" id="invoiceItem">
				<thead align="center" class="table-success">
					<tr>
						<th width="2%"><input id="checkAll" class="formcontrol" type="checkbox"></th>
						<th width="38%">Nombre Producto</th>
						<th width="15%">Cantidad</th>
						<th width="15%">Precio</th>	
						<th width="15%">Descuento</th>									
						<th width="15%">Total</th>
					</tr>	
				</thead>
				<tbody>
					<tr>
						<td><input class="itemRow" type="checkbox"></td>
						<td>
							<input type="hidden" name="isv[]" id="isv_0" class="form-control" placeholder="Producto ISV" autocomplete="off">
							<input type="hidden" name="valor_isv[]" id="valor_isv_0" class="form-control" placeholder="Valor ISV" autocomplete="off">
							<input type="hidden" name="facturas_detalle_id[]" id="facturas_detalle_id_0" class="form-control" placeholder="Código Producto" autocomplete="off">
							<input type="hidden" name="productoID[]" id="productoID_0" class="form-control" placeholder="Código Producto" autocomplete="off">						
							<div class="input-group mb-3">
								<input type="text" name="productName[]" id="productName_0" class="form-control producto" placeholder="Producto o Servicio" autocomplete="off">
								<div id="suggestions_producto_0" class="suggestions"></div>
								<div class="input-group-append" id="grupo_buscar_productos">								
									<a data-toggle="modal" href="#" class="btn btn-outline-success buscar_productos"><div class="sb-nav-link-icon"></div><i class="buscar_producto fas fa-search-plus fa-lg"></i></a>
								</div>
							</div>								
						</td>			
						<td><input type="number" name="quantity[]" id="quantity_0" class="buscar_cantidad form-control" placeholder="Cantidad" autocomplete="off"></td>
						<td><input type="number" name="price[]" id="price_0" class="form-control price" placeholder="Precio" autocomplete="off"></td>
						<td>							
							<div class="input-group mb-3">
								<input type="number" name="discount[]" id="discount_0" class="form-control" step="0.01" placeholder="Descuento" readonly autocomplete="off">
								<div id="suggestions_producto_0" class="suggestions"></div>
								<div class="input-group-append" id="grupo_buscar_productos">								
									<a data-toggle="modal" href="#" class="btn btn-outline-success buscar_productos"><div class="sb-nav-link-icon"></div><i class="aplicar_descuento fas fa-plus fa-lg"></i></a>
								</div>
							</div>
						</td>
						<td><input type="number" name="total[]" id="total_0" class="form-control total" placeholder="Total" readonly autocomplete="off"></td>
					</tr>
				</tbody>
			</table>
		</div>	
	  </div>	
	  <hr class="line_table" />
	 <div class="form-group row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button class="btn btn-success ml-3" id="addRows" type="button" data-toggle="tooltip" data-placement="top" title="Agregar filas en la factura"><div class="sb-nav-link-icon"></div><i class="fas fa-plus fa-lg"></i> Agregar</button>
			<button class="btn btn-danger delete" id="removeRows" type="button" data-toggle="tooltip" data-placement="top" title="Remover filas en la factura"><div class="sb-nav-link-icon"></div><i class="fas fa-minus fa-lg"></i> Remover</button>					
		</div>
	 </div>				
	  
	  <div class="form-group row">
		  <div class="form-row col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="col-sm-12 col-md-12">
				<h3>Notas: </h3>
				<div class="form-group">
					<textarea class="form-control txt" rows="4" name="notes" id="notes" placeholder="Notas" maxlength="255"></textarea>
					<p id="charNum_notas">255 Caracteres</p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4" style="display: none;">
			  <div class="row">					  
				<div class="col-sm-3 form-inline">
				  <label>Subtotal:</label>
				</div>
				<div class="col-sm-9">	
					<div class="input-group">
						<div class="input-group-append mb-1">				
							<span class="input-group-text"><div class="sb-nav-link-icon"></div>L</i></span>
						</div>												
						<input value="" type="number" class="form-control" name="subTotal" step="0.01" id="subTotal" readonly placeholder="Subtotal">
					</div>
				</div>
			  </div>
			  <div class="row" style="display: none">
				<div class="col-sm-3 form-inline">
					<label>Porcentaje:</label>
				</div>
				<div class="col-sm-9">
					<div class="input-group mb-1">															
						<input value="0" type="number" class="form-control" name="taxRate" id="taxRate" step="0.01" readonly placeholder="Tasa de Impuestos">
						<div class="input-group-append">				
							<span class="input-group-text"><div class="sb-nav-link-icon"></div>%</i></span>
						</div>
					</div>
				</div>
			  </div>
			  <div class="row">
				<div class="col-sm-3 form-inline">
				  <label>ISV:</label>
				</div>
				<div class="col-sm-9">
					<div class="input-group mb-1">											
						<div class="input-group-append">				
							<span class="input-group-text"><div class="sb-nav-link-icon"></div>L</i></span>
						</div>	
						<input value="" type="number" class="form-control" name="taxAmount" id="taxAmount" step="0.01" readonly value="0.00" placeholder="Monto del Impuesto">
					</div>
				</div>
			  </div>
			  <div class="row">
				<div class="col-sm-3 form-inline">
				  <label>Descuento:</label>
				</div>
				<div class="col-sm-9">
					<div class="input-group mb-1">											
						<div class="input-group-append">				
							<span class="input-group-text"><div class="sb-nav-link-icon"></div>L</i></span>
						</div>	
						<input value="" type="number" class="form-control" name="taxDescuento" id="taxDescuento" step="0.01" readonly value="0.00" placeholder="Descuento Otorgado">
					</div>
				</div>
			  </div>						  
			  <div class="row">
				<div class="col-sm-3 form-inline">
					<label>Total:</label>
				</div>
				<div class="col-sm-9">
					<div class="input-group mb-1">
						<div class="input-group-append">				
							<span class="input-group-text"><div class="sb-nav-link-icon"></div>L</i></span>
						</div>	
						<input value="" type="number" class="form-control" name="totalAftertax" id="totalAftertax" step="0.01" value="0.00" readonly placeholder="Total">
					</div>
				</div>
			  </div>	
			  <div class="row" style="display: none;">
				<div class="col-sm-3 form-inline">
					<label>Cantidad pagada:</label>
				</div>
				<div class="col-sm-9">
					<div class="input-group mb-1">
						<div class="input-group-append">				
							<span class="input-group-text"><div class="sb-nav-link-icon"></div>L</i></span>
						</div>	
						<input value="" type="number" class="form-control" name="amountPaid" id="amountPaid" readonly step="0.01" placeholder="Cantidad pagada">
					</div>
				</div>
			  </div>	
			  <div class="row" style="display: none;">
				<div class="col-sm-3 form-inline">
					<label>Cantidad debida:</label>
				</div>
				<div class="col-sm-9">
					<div class="input-group mb-1">
						<div class="input-group-append">				
							<span class="input-group-text"><div class="sb-nav-link-icon"></div>L</i></span>
						</div>	
						<input value="" type="number" class="form-control" name="amountDue" id="amountDue" readonly step="0.01" placeholder="Cantidad debida">
					</div>
				</div>
			  </div>								  		
			</div>
		  </div>
	  </div>
	  
	  <div class="form-group row">
		<label for="inputCliente" class="col-sm-1 col-form-label-md">Empresas </label>
		<div class="col-sm-5">
			<select class="selectpicker" id="fact_empresas_id" name="fact_empresas_id"
				data-live-search="true" title="Empresas" data-width="100%" data-size="7">
			</select>
		</div>		
	  </div>

	  <div class="form-group row">
		<label for="inputCliente" class="col-sm-1 col-form-label-md">Aseguradora </label>
		<div class="col-sm-5">
			<select class="selectpicker" id="aseguradora_id" name="aseguradora_id"
				data-live-search="true" title="Aseguradora" data-width="100%" data-size="7">
			</select>
		</div>		
	  </div>	  
	  
	</form>
</div> 