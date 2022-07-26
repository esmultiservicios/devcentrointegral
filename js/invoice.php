<script>
//INVOICES
function llenarTablaFactura(count){
	var htmlRows = '';
	htmlRows += '<tr>';
	htmlRows += '<td><input class="itemRow" type="checkbox"></td>';                    
	htmlRows += '<td><input type="hidden" name="isv[]" id="isv_'+count+'" class="form-control" placeholder="Producto ISV" autocomplete="off"><input type="hidden" name="valor_isv[]" id="valor_isv_'+count+'" class="form-control" placeholder="Valor ISV" autocomplete="off"><input type="hidden" name="facturas_detalle_id[]" id="facturas_detalle_id_'+count+'" class="form-control" placeholder="Código Producto" autocomplete="off"><input type="hidden" name="productoID[]" id="productoID_'+count+'" class="form-control" placeholder="Código Producto" autocomplete="off"><div class="input-group"><input type="text" name="productName[]" id="productName_'+count+'" class="form-control producto" placeholder="Producto o Servicio" autocomplete="off"><div id="suggestions_producto_'+count+'" class="suggestions"></div><div class="input-group-append" id="grupo_buscar_productos"><a data-toggle="modal" href="#" class="btn btn-outline-success" id="buscar_productos"><div class="sb-nav-link-icon"></div><i class="buscar_producto fas fa-search-plus fa-lg"></i></a></div></td>';			
	htmlRows += '<td><input type="number" name="quantity[]" id="quantity_'+count+'" placeholder="Cantidad" class="buscar_cantidad form-control" autocomplete="off"></td>';   		
	htmlRows += '<td><input type="number" name="price[]" id="price_'+count+'" placeholder="Precio" class="form-control" autocomplete="off"></td>';	
	htmlRows += '<td><input type="number" name="discount[]" id="discount_'+count+'" placeholder="Descuento" step="0.01" class="form-control" autocomplete="off"></td>';			
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
	htmlRows += '<td><input type="number" name="discount[]" id="discount_'+count+'" placeholder="Descuento" step="0.01" class="form-control" autocomplete="off"></td>';			
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
	htmlRows += '<td><input type="number" name="discount[]" id="discount_'+count+'" placeholder="Descuento" step="0.01" class="form-control" autocomplete="off"></td>';			
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
				type: "error", 
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
				type: "error", 
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
</script>