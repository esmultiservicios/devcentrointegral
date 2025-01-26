<script>
//INVOICES
function llenarTablaFactura(count){
	var htmlRows = '';
	htmlRows += '<tr>';
	htmlRows += '<td><input class="itemRow" type="checkbox"></td>';                    
	htmlRows += '<td><input type="hidden" name="isv[]" id="isv_'+count+'" class="form-control" placeholder="Producto ISV" autocomplete="off"><input type="hidden" name="valor_isv[]" id="valor_isv_'+count+'" class="form-control" placeholder="Valor ISV" autocomplete="off"><input type="hidden" name="facturas_detalle_id[]" id="facturas_detalle_id_'+count+'" class="form-control" placeholder="Código Producto" autocomplete="off"><input type="hidden" name="productoID[]" id="productoID_'+count+'" class="form-control" placeholder="Código Producto" autocomplete="off"><div class="input-group"><input type="text" name="productName[]" id="productName_'+count+'" class="form-control producto" placeholder="Producto o Servicio" autocomplete="off"><div id="suggestions_producto_'+count+'" class="suggestions"></div><div class="input-group-append" id="grupo_buscar_productos"><a data-toggle="modal" href="#" class="btn btn-outline-success" id="buscar_productos"><div class="sb-nav-link-icon"></div><i class="buscar_producto fas fa-search-plus fa-lg"></i></a></div></td>';						
	htmlRows += '<td><input type="number" name="quantity[]" id="quantity_'+count+'" placeholder="Cantidad" class="buscar_cantidad form-control" autocomplete="off"></td>';   		
	htmlRows += '<td><input type="number" name="price[]" id="price_'+count+'" placeholder="Precio" class="form-control" autocomplete="off"></td>';	
	htmlRows += '<td><div class="input-group mb-3"><input type="number" name="discount[]" id="discount_'+count+'" class="form-control" step="0.01" placeholder="Descuento" readonly autocomplete="off"><div id="suggestions_producto_0" class="suggestions"></div><div class="input-group-append" id="grupo_aplicar_descuento"><a data-toggle="modal" href="#" class="btn btn-outline-success"><div class="sb-nav-link-icon"></div><i class="aplicar_descuento fas fa-plus fa-lg"></i></a></div></div></td>';			
	htmlRows += '<td><input type="number" name="total[]" id="total_'+count+'" placeholder="Total" class="form-control total" readonly autocomplete="off"></td>';          
	htmlRows += '</tr>';
	$('#invoiceItem').append(htmlRows);	
	$("#formulario_facturacion .tableFixHead").scrollTop($(document).height());
	$("#formulario_facturacion #invoiceItem #productoID_"+count).focus();

	if(getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		$('#formulario_facturacion #invoiceItem #price_'+ count).attr('readonly', false);
	}else{
		$('#formulario_facturacion #invoiceItem #price_'+ count).attr('readonly', false);
	}		
}

function limpiarTabla(){
	$("#formulario_facturacion #invoiceItem > tbody").empty();//limpia solo los registros del body
	var count = 0;
	var htmlRows = '';
	htmlRows += '<tr>';
	htmlRows += '<td><input class="itemRow" type="checkbox"></td>';                    
	htmlRows += '<td><input type="hidden" name="isv[]" id="isv_'+count+'" class="form-control" placeholder="Producto ISV" autocomplete="off"><input type="hidden" name="valor_isv[]" id="valor_isv_'+count+'" class="form-control" placeholder="Valor ISV" autocomplete="off"><input type="hidden" name="facturas_detalle_id[]" id="facturas_detalle_id_'+count+'" class="form-control" placeholder="Código Producto" autocomplete="off"><input type="hidden" name="productoID[]" id="productoID_'+count+'" class="form-control" placeholder="Código Producto" autocomplete="off"><div class="input-group"><input type="text" name="productName[]" id="productName_'+count+'" class="form-control producto" placeholder="Producto o Servicio" autocomplete="off"><div id="suggestions_producto_'+count+'" class="suggestions"></div><div class="input-group-append" id="grupo_buscar_productos"><a data-toggle="modal" href="#" class="btn btn-outline-success" id="buscar_productos"><div class="sb-nav-link-icon"></div><i class="buscar_producto fas fa-search-plus fa-lg"></i></a></div></td>';			
	htmlRows += '<td><input type="number" name="quantity[]" id="quantity_'+count+'" placeholder="Cantidad" class="buscar_cantidad form-control" autocomplete="off"></td>';   		
	htmlRows += '<td><input type="number" name="price[]" id="price_'+count+'" placeholder="Precio" class="form-control" autocomplete="off"></td>';	
	htmlRows += '<td><div class="input-group mb-3"><input type="number" name="discount[]" id="discount_'+count+'" class="form-control" step="0.01" placeholder="Descuento" readonly autocomplete="off"><div id="suggestions_producto_0" class="suggestions"></div><div class="input-group-append" id="grupo_aplicar_descuento"><a data-toggle="modal" href="#" class="btn btn-outline-success"><div class="sb-nav-link-icon"></div><i class="aplicar_descuento fas fa-plus fa-lg"></i></a></div></div></td>';				
	htmlRows += '<td><input type="number" name="total[]" id="total_'+count+'" placeholder="Total" class="form-control total" readonly autocomplete="off"></td>';          
	htmlRows += '</tr>';
	$('#invoiceItem').append(htmlRows);	
	$("#formulario_facturacion .tableFixHead").scrollTop($(document).height());
	$("#formulario_facturacion #invoiceItem #productoID_"+count).focus();

	if(getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		$('#formulario_facturacion #invoiceItem #price_'+ count).attr('readonly', false);
	}else{
		$('#formulario_facturacion #invoiceItem #price_'+ count).attr('readonly', false);
	}		
}

function addRow(){
	var count = $(".itemRow").length;
	var htmlRows = '';
	htmlRows += '<tr>';
	htmlRows += '<td><input class="itemRow" type="checkbox"></td>';                    
	htmlRows += '<td><input type="hidden" name="isv[]" id="isv_'+count+'" class="form-control" placeholder="Producto ISV" autocomplete="off"><input type="hidden" name="valor_isv[]" id="valor_isv_'+count+'" class="form-control" placeholder="Valor ISV" autocomplete="off"><input type="hidden" name="facturas_detalle_id[]" id="facturas_detalle_id_'+count+'" class="form-control" placeholder="Código Producto" autocomplete="off"><input type="hidden" name="productoID[]" id="productoID_'+count+'" class="form-control" placeholder="Código Producto" autocomplete="off"><div class="input-group"><input type="text" name="productName[]" id="productName_'+count+'" class="form-control producto" placeholder="Producto o Servicio" autocomplete="off"><div id="suggestions_producto_'+count+'" class="suggestions"></div><div class="input-group-append" id="grupo_buscar_productos"><a data-toggle="modal" href="#" class="btn btn-outline-success" id="buscar_productos"><div class="sb-nav-link-icon"></div><i class="buscar_producto fas fa-search-plus fa-lg"></i></a></div></td>';			
	htmlRows += '<td><input type="number" name="quantity[]" id="quantity_'+count+'" placeholder="Cantidad" class="buscar_cantidad form-control" autocomplete="off"></td>';   		
	htmlRows += '<td><input type="number" name="price[]" id="price_'+count+'" placeholder="Precio" class="form-control" autocomplete="off"></td>';	
	htmlRows += '<td><div class="input-group mb-3"><input type="number" name="discount[]" id="discount_'+count+'" class="form-control" step="0.01" readonly placeholder="Descuento" autocomplete="off"><div id="suggestions_producto_0" class="suggestions"></div><div class="input-group-append" id="grupo_aplicar_descuento"><a data-toggle="modal" href="#" class="btn btn-outline-success"><div class="sb-nav-link-icon"></div><i class="aplicar_descuento fas fa-plus fa-lg"></i></a></div></div></td>';		
	htmlRows += '<td><input type="number" name="total[]" id="total_'+count+'" placeholder="Total" class="form-control total" readonly autocomplete="off"></td>';          
	htmlRows += '</tr>';
	$('#invoiceItem').append(htmlRows);	
	$("#formulario_facturacion #invoiceItem #productoID_"+count).focus();
	
	if(getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		$('#formulario_facturacion #invoiceItem #price_'+ count).attr('readonly', false);
	}else{
		$('#formulario_facturacion #invoiceItem #price_'+ count).attr('readonly', false);
	}		
	count++;
}

function deleteFacturasDetalles(facturas_detalle_id){
	var url = '<?php echo SERVERURL; ?>php/facturacion/deleteFacturasDetalles.php';

	$.ajax({
	   type:'POST',
	   url:url,
	   async: false,
	   data:'facturas_detalle_id='+facturas_detalle_id,
	   success:function(data){

	  }
	});
}

$(document).ready(function(){
	$(document).on('click', '#checkAll', function() {          	
		$(".itemRow").prop("checked", this.checked);
	});	
	$(document).on('click', '.itemRow', function() {  	
		if ($('.itemRow:checked').length == $('.itemRow').length) {
			$('#checkAll').prop('checked', true);
		} else {
			$('#checkAll').prop('checked', false);
		}
	});  
	var count = $(".itemRow").length;
	$(document).on('click', '#addRows', function() { 
		if($("#formulario_facturacion #paciente_factura").val() != ""){
			addRow();
		}else{
			swal({
				title: "Error", 
				text: "Lo sentimos no puede agregar más filas, debe seleccionar un usuario antes de poder continuar",
				icon: "error", 
				confirmButtonClass: "btn-danger"
			});				
		}
	}); 
	$(document).on('click', '#removeRows', function(){
		if ($('.itemRow ').is(':checked') ){
			$(".itemRow:checked").each(function() {
				var row_index = $(this).closest("tr").index();
		 		var col_index = $(this).closest("td").index();
				deleteFacturasDetalles($('#formulario_facturacion #invoiceItem #facturas_detalle_id_'+ row_index).val());
				$(this).closest('tr').remove();
				count--;
			});
			$('#checkAll').prop('checked', false);
			calculateTotal();						
		}else{
			swal({
				title: "Error", 
				text: "Lo sentimos debe seleccionar un fila antes de intentar eliminarla",
				icon: "error", 
				confirmButtonClass: "btn-danger"
			});				
		}
	});		
	$(document).on('blur', "[id^=quantity_]", function(){
		calculateTotal();
	});	
	$(document).on('keyup', "[id^=quantity_]", function(){
		calculateTotal();
	});	
	$(document).on('blur', "[id^=price_]", function(){
		calculateTotal();
	});	
	$(document).on('keyup', "[id^=price_]", function(){
		calculateTotal();
	});		
	$(document).on('blur', "[id^=discount_]", function(){
		calculateTotal();
	});	
	$(document).on('keyup', "[id^=discount_]", function(){
		calculateTotal();
	});		
	$(document).on('blur', "#taxRate", function(){		
		calculateTotal();
	});	
	$(document).on('blur', "#amountPaid", function(){
		var amountPaid = $(this).val();
		var totalAftertax = $('#totalAftertax').val();	
		if(amountPaid && totalAftertax) {
			totalAftertax = totalAftertax-amountPaid;			
			$('#amountDue').val(totalAftertax);
		} else {
			$('#amountDue').val(totalAftertax);
		}	
	});	
	$(document).on('click', '.deleteInvoice', function(){
		var id = $(this).attr("id");
		if(confirm("Are you sure you want to remove this?")){
			$.ajax({
				url:"action.php",
				method:"POST",
				dataType: "json",
				data:{id:id, action:'delete_invoice'},				
				success:function(response) {
					if(response.status == 1) {
						$('#'+id).closest("tr").remove();
					}
				}
			});
		} else {
			return false;
		}
	});
});

function calculateTotal(){
	var totalAmount = 0; 
	var totalDiscount = 0;
	var totalISV = 0;
	$("[id^='price_']").each(function() {
		var id = $(this).attr('id');
		id = id.replace("price_",'');
		var price = $('#price_'+id).val();
		var isv_calculo = $('#valor_isv_'+id).val();
		var discount = $('#discount_'+id).val();
		var quantity  = $('#quantity_'+id).val();
		if(!discount){
			discount = 0;
		}
		if(!quantity) {
			quantity = 1;
		}
		
		if(!isv_calculo){
			isv_calculo = 0;
		}
		
		var total = price*quantity;
		$('#total_'+id).val(parseFloat(total));
		totalAmount += total;
		totalISV += parseFloat(isv_calculo);
		totalDiscount += parseFloat(discount);
	});	
	$('#subTotal').val(parseFloat(totalAmount).toFixed(2));
	$('#subTotalFooter').val(parseFloat(totalAmount).toFixed(2));
	$('#taxDescuento').val(parseFloat(totalDiscount).toFixed(2));
	$('#taxDescuentoFooter').val(parseFloat(totalDiscount).toFixed(2));	
	var taxRate = $("#taxRate").val();
	var subTotal = $('#subTotal').val();	
	if(subTotal) {
		//var taxAmount = subTotal*taxRate/100;
		$('#taxAmount').val(parseFloat(totalISV).toFixed(2));
		$('#taxAmountFooter').val(parseFloat(totalISV).toFixed(2));
		subTotal = (parseFloat(subTotal)+parseFloat($('#taxAmount').val()))-parseFloat(totalDiscount);
		$('#totalAftertax').val(parseFloat(subTotal).toFixed(2));
		$('#totalAftertaxFooter').val(parseFloat(subTotal).toFixed(2));

		var amountPaid = $('#amountPaid').val();
		var totalAftertax = $('#totalAftertax').val();	
		if(amountPaid && totalAftertax) {
			totalAftertax = totalAftertax-amountPaid;			
			$('#amountDue').val(totalAftertax);
		} else {		
			$('#amountDue').val(subTotal);
		}
	}
}

function cleanFooterValueBill(){
	$('#subTotalFooter').val("");
	$('#taxAmountFooter').val("");
	$('#totalAftertaxFooter').val("");
}

//INICIO DESCUENTO PRODUCTO EN FACTURACION
$(document).ready(function(){
    $("#formulario_facturacion #invoiceItem").on('click', '.aplicar_descuento', function(e) {
		  e.preventDefault();
		  $('#formDescuentoFacturacion')[0].reset();

		  var row_index = $(this).closest("tr").index();
		  var col_index = $(this).closest("td").index();

		  if( $('#formulario_facturacion #pacientes_id').val() != "" &&  $("#formulario_facturacion #invoiceItem #productoID_" + row_index).val() != ""){			
			$('#formDescuentoFacturacion #row_index').val(row_index);
			$('#formDescuentoFacturacion #col_index').val(col_index);

			var productos_id = $("#formulario_facturacion #invoiceItem #productos_id_" + row_index).val();
			var producto = $("#formulario_facturacion #invoiceItem #productName_" + row_index).val();
			var precio = $("#formulario_facturacion #invoiceItem #price_" + row_index).val();
					
			$('#formDescuentoFacturacion #descuento_productos_id').val(productos_id);
			$('#formDescuentoFacturacion #producto_descuento_fact').val(producto);
			$('#formDescuentoFacturacion #precio_descuento_fact').val(precio);		  

			$('#formDescuentoFacturacion #pro_descuento_fact').val("Aplicar Descuento");

			$('#modalDescuentoFacturacion').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
			});
		  }else{
			swal({
				title: "Error",
				text: "Debe seleccionar un paciente y seleciconar un producto antes de continuar",
				icon: "error",
				confirmButtonClass: "btn-danger"
			});				
		  }
	});
});

$(document).ready(function() {
	$("#formDescuentoFacturacion #porcentaje_descuento_fact").on("keyup", function(){
		var precio;
		var porcentaje;
			
		if($("#formDescuentoFacturacion #porcentaje_descuento_fact").val()){
			precio = parseFloat($('#formDescuentoFacturacion #precio_descuento_fact').val());
			porcentaje = parseFloat($('#formDescuentoFacturacion #porcentaje_descuento_fact').val());
			
			$('#formDescuentoFacturacion #descuento_fact').val(parseFloat(precio * (porcentaje/100)).toFixed(2));
		}else{
			$('#formDescuentoFacturacion #descuento_fact').val(0);
		}
	});	
	
	$("#formDescuentoFacturacion #descuento_fact").on("keyup", function(){
		var precio;
		var descuento_fact;
			
		if($("#formDescuentoFacturacion #descuento_fact").val() != ""){
			precio = parseFloat($('#formDescuentoFacturacion #precio_descuento_fact').val());
			descuento_fact = parseFloat($('#formDescuentoFacturacion #descuento_fact').val());
			
			$('#formDescuentoFacturacion #porcentaje_descuento_fact').val(parseFloat((descuento_fact / precio) * 100).toFixed(2));
		}else{
			$('#formDescuentoFacturacion #porcentaje_descuento_fact').val(0);
		}
	});		
});		

$("#reg_DescuentoFacturacion").on("click", function(e){
	e.preventDefault();
	var row_index = $('#formDescuentoFacturacion #row_index').val();
	var col_index = $('#formDescuentoFacturacion #col_index').val();

	var descuento = parseFloat($('#formDescuentoFacturacion #descuento_fact').val()).toFixed(2);

	var precio = $("#formulario_facturacion #invoiceItem #price_" + row_index).val();
	var cantidad = $("#formulario_facturacion #invoiceItem #quantity_" + row_index).val();
	var impuesto_venta = $("#formulario_facturacion #invoiceItem #isv_" + row_index).val();
	$("#formulario_facturacion #invoiceItem #discount_" + row_index).val(descuento);


	var isv = 0;
	var isv_total = 0;
	var porcentaje_isv = 0;
	var porcentaje_calculo = 0;
	var isv_neto = 0;
	var total_ = (precio * cantidad) - descuento;

	if(total_ >= 0){
		if(impuesto_venta == 1){
			porcentaje_isv = parseFloat(getPorcentajeISV("Facturas") / 100);
			if($('#formulario_facturacion #taxAmount').val() == "" || $('#formulario_facturacion #taxAmount').val() == 0){
				porcentaje_calculo = (parseFloat(total_) * porcentaje_isv).toFixed(2);
				isv_neto = porcentaje_calculo;
				$('#formulario_facturacion #taxAmount').val(porcentaje_calculo);
				$('#formulario_facturacion #invoiceItem #valor_isv_'+ row_index).val(porcentaje_calculo);
			}else{
				isv_total = parseFloat($('#formulario_facturacion #taxAmount').val());
				porcentaje_calculo = (parseFloat(total_) * porcentaje_isv).toFixed(2);
				isv_neto = parseFloat(isv_total) + parseFloat(porcentaje_calculo);
				$('#formulario_facturacion #taxAmount').val(isv_neto);
				$('#formulario_facturacion #invoiceItem #valor_isv_'+ row_index).val(porcentaje_calculo);
			}
		}

		$('#modalDescuentoFacturacion').modal('hide');
		calculateTotal();
	}else{
		swal({
			title: "warning",
			text: "El valor del descuento es mayor al precio total del artículo, por favor corregir",
			icon: "warning",
			confirmButtonClass: "btn-warning"
		});		
	}
});
//FIN DESCUENTO PRODUCTO EN FACTURACION
</script>