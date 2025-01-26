<script>
	/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#modal_productos").on('shown.bs.modal', function(){
        $(this).find('#formulario_productos #nombre').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$(document).ready(function() {
	$('#formulario_productos #categoria').on('change',function(){
		evaluarCategoria();
	});	
	
	funciones();
	listar_productos();	
});	

function evaluarCategoria(){
	if($('#formulario_productos #categoria').find('option:selected').text() == "Servicio"){
		$('#formulario_productos #concentracion').attr('readonly', true);
		$('#formulario_productos #cantidad').attr('readonly', true);
		$('#formulario_productos #precio_compra').attr('readonly', true);
		$('#formulario_productos #precio_venta').attr('readonly', false);		
		$('#formulario_productos #cantidad_minima').attr('readonly', true);
		$('#formulario_productos #cantidad_maxima').attr('readonly', true);
		$('#formulario_productos #producto_isv_factura').prop('checked', false);
		$('#formulario_productos #label_producto_isv_factura').html("No");
		$('#formulario_productos #concentracion').val(0);
		$('#formulario_productos #cantidad').val(1);
		$('#formulario_productos #precio_compra').val(0);
	}else if($('#formulario_productos #categoria').find('option:selected').text() == "Insumos"){
		$('#formulario_productos #concentracion').attr('readonly', false);
		$('#formulario_productos #cantidad').attr('readonly', false);
		$('#formulario_productos #precio_compra').attr('readonly', false);
		$('#formulario_productos #precio_venta').attr('readonly', true);
		$('#formulario_productos #cantidad_minima').attr('readonly', false);
		$('#formulario_productos #cantidad_maxima').attr('readonly', false);					
		$('#formulario_productos #concentracion').val("");
		$('#formulario_productos #cantidad').val(1);
		$('#formulario_productos #precio_venta').val(0);
		$('#formulario_productos #producto_isv_factura').prop('checked', false);
		$('#formulario_productos #label_producto_isv_factura').html("No");		
	}else{
		$('#formulario_productos #concentracion').attr('readonly', false);
		$('#formulario_productos #cantidad').attr('readonly', false);
		$('#formulario_productos #precio_compra').attr('readonly', false);
		$('#formulario_productos #precio_venta').attr('readonly', false);		
		$('#formulario_productos #cantidad_minima').attr('readonly', false);
		$('#formulario_productos #cantidad_maxima').attr('readonly', false);
		$('#formulario_productos #producto_isv_factura').prop('checked', true);
		$('#formulario_productos #label_producto_isv_factura').html("Sí");		
		$('#formulario_productos #concentracion').val('');			
		$('#formulario_productos #cantidad').val('');
		$('#formulario_productos #precio_compra').val('');			
	}	
}

function evaluarCategoriaDetalle(categoria){
	if(categoria == "Servicio"){
		$('#formulario_productos #concentracion').attr('readonly', true);
		$('#formulario_productos #cantidad').attr('readonly', true);
		$('#formulario_productos #precio_compra').attr('readonly', true);
		$('#formulario_productos #precio_venta').attr('readonly', false);		
		$('#formulario_productos #cantidad_minima').attr('readonly', true);
		$('#formulario_productos #cantidad_maxima').attr('readonly', true);
		$('#formulario_productos #producto_isv_factura').prop('checked', false);
		$('#formulario_productos #label_producto_isv_factura').html("No");
		$('#formulario_productos #concentracion').val(0);
		$('#formulario_productos #cantidad').val(1);
		$('#formulario_productos #precio_compra').val(0);
	}else if(categoria == "Insumos"){
		$('#formulario_productos #concentracion').attr('readonly', false);
		$('#formulario_productos #cantidad').attr('readonly', false);
		$('#formulario_productos #precio_compra').attr('readonly', false);
		$('#formulario_productos #precio_venta').attr('readonly', true);
		$('#formulario_productos #cantidad_minima').attr('readonly', false);
		$('#formulario_productos #cantidad_maxima').attr('readonly', false);
		$('#formulario_productos #label_producto_isv_factura').html("No");
		$('#formulario_productos #concentracion').val("");
		$('#formulario_productos #cantidad').val(1);
		$('#formulario_productos #precio_venta').val(0);
		$('#formulario_productos #producto_isv_factura').prop('checked', true);	
	}else{
		$('#formulario_productos #concentracion').attr('readonly', false);
		$('#formulario_productos #cantidad').attr('readonly', false);
		$('#formulario_productos #precio_compra').attr('readonly', false);
		$('#formulario_productos #precio_venta').attr('readonly', false);		
		$('#formulario_productos #cantidad_minima').attr('readonly', false);
		$('#formulario_productos #cantidad_maxima').attr('readonly', false);
		$('#formulario_productos #producto_isv_factura').prop('checked', true);
		$('#formulario_productos #label_producto_isv_factura').html("Sí");
		$('#formulario_productos #concentracion').val('');			
		$('#formulario_productos #cantidad').val('');
		$('#formulario_productos #precio_compra').val('');			
	}	
}

function agregarProductos(){
	funciones();
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
		$('#formulario_productos').attr({ 'data-form': 'save' });
		$('#formulario_productos').attr({ 'action': '<?php echo SERVERURL; ?>php/productos/agregarProductos.php' });			
		$('#reg_producto').show();
		$('#edi_producto').hide();
		$('#delete_producto').hide();		
		$('#delete_producto').hide();		
		$('#formulario_productos')[0].reset();	
		$('#formulario_productos #pro').val('Registro');
		$('#formulario_productos #categoria').attr("disabled", false);
		$('#formulario_productos #medida').attr("disabled", false);
		$('#formulario_productos #almacen').attr("disabled", false);
		$('#formulario_productos #concentracion').attr("readonly", false);		
		$("#formulario_productos #fecha").attr('readonly', false);
		
		//HABIITAR OBJETOS
		$('#formulario_productos #nombre').attr("readonly", false);
		$('#formulario_productos #nombre').attr("readonly", false);		
		$('#formulario_productos #cantidad').attr("readonly", false);
		$('#formulario_productos #precio_compra').attr("readonly", false);
		$('#formulario_productos #precio_venta').attr("readonly", false);
		$('#formulario_productos #descripcion').attr("readonly", false);
		$('#formulario_productos #producto_activo').attr("disabled", false);
		$('#formulario_productos #producto_isv_factura').attr("disabled", false);
		$('#formulario_productos #cantidad_minima').attr("readonly", false);
    	$('#formulario_productos #cantidad_maxima').attr("readonly", false);		

				
		 $('#modal_productos').modal({
			show:true,
			keyboard: false,
			backdrop:'static'
		});			
	}else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error", 
			dangerMode: true
		});				
	} 	
}

function funciones(){
	getAlmacen();
	getMedida();
	getCategoriaProducto();
	getCategoria();
}

function getAlmacen(){
    var url = '<?php echo SERVERURL; ?>php/productos/getAlmacen.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#formulario_productos #almacen').html("");
			$('#formulario_productos #almacen').html(data);		
		}			
     });		
}

function getCategoria(){
    var url = '<?php echo SERVERURL; ?>php/productos/getCategoria.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#formulario_productos #categoria_id').html("");
			$('#formulario_productos #categoria_id').html(data);		

		    $('#form_main #categoria_id').html("");
			$('#form_main #categoria_id').html(data);			
		}			
     });		
}

function getMedida(){
    var url = '<?php echo SERVERURL; ?>php/productos/getMedida.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#formulario_productos #medida').html("");
			$('#formulario_productos #medida').html(data);			
		}			
     });		
}

function getCategoriaProducto(){
    var url = '<?php echo SERVERURL; ?>php/productos/getCategoriaProducto.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#formulario_productos #categoria').html("");
			$('#formulario_productos #categoria').html(data);		
		}			
     });		
}

$('#formulario_productos #descripcion').keyup(function() {
	var max_chars = 150;
	var chars = $(this).val().length;
	var diff = max_chars - chars;
	
	$('#formulario_productos #charNum_descripcion').html(diff + ' Caracteres'); 
	
	if(diff == 0){
		return false;
	}
});

function caracteresDescripcion(){
	var max_chars = 150;
	var chars = $('#formulario_productos #descripcion').val().length;
	var diff = max_chars - chars;
	
	$('#formulario_productos #charNum_descripcion').html(diff + ' Caracteres'); 
	
	if(diff == 0){
		return false;
	}	
}
//FIN FUNCIONES

$('#form_main #actualizar').on('click', function(e){
	e.preventDefault();
	listar_productos();
});

var listar_productos = function(){
	var estado_producto = $("#form_main #estado_producto").val();
	var categoria_id = $("#form_main #categoria_id").val();

	var table_productos  = $("#dataTableProductos").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/productos/getProductosTabla.php",
			"data":{
				"estado_producto":estado_producto,
				"categoria_id":categoria_id
			}					
		},		
		"columns":[
			{"data":"producto"},
			{"data":"cantidad"},
			{"data":"concentracion"},
			{"data":"medida"},
			{"data":"categoria"},			
			{"data":"almacen"},			
			{"data":"precio_compra"},
			{"data":"precio_venta"},
			{"data":"isv"},			
			{"data":"descripcion"},			
			{"defaultContent":"<button class='editar btn btn-warning'><span class='fas fa-edit'></span></button>"},
			{"defaultContent":"<button class='delete btn btn-danger'><span class='fa fa-trash'></span></button>"}
		],		
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,		
		"language": idioma_español,//esta se encuenta en el archivo main.js
		"dom": dom,			
		"buttons":[		
			{
				text:      '<i class="fas fa-sync-alt fa-lg"></i> Actualizar',
				titleAttr: 'Actualizar Productos',
				className: 'btn btn-info',
				action: 	function(){
					listar_entrevista_ts();
				}
			},	
			{
				text:      '<i class="fab fa-product-hunt fa-lg"></i> Crear',
				titleAttr: 'Agregar Productos',
				className: 'btn btn-primary',
				action: 	function(){
					agregarProductos();
				}
			},				
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				title: 'Reporte Productos',
				className: 'btn btn-success'				
			},
			{
				extend:    'pdf',
				orientation: 'landscape',
				text:      '<i class="fas fa-file-pdf fa-lg"></i> PDF',
				titleAttr: 'PDF',
				title: 'Reporte Productos',
				className: 'btn btn-danger',
				customize: function ( doc ) {
					doc.content.splice( 1, 0, {
						margin: [ 0, 0, 0, 12 ],
						alignment: 'left',
						image: imagen,//esta se encuenta en el archivo main.js
						width:170,
                        height:45
					} );
				}				
			}
		]		
	});	 
	table_productos.search('').draw();
	$('#buscar').focus();
	
	edit_productos_dataTable("#dataTableProductos tbody", table_productos);
	delete_productos_dataTable("#dataTableProductos tbody", table_productos);
}

var edit_productos_dataTable = function(tbody, table){
	$(tbody).off("click", "button.editar");
	$(tbody).on("click", "button.editar", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		var url = '<?php echo SERVERURL; ?>php/productos/editarProductos.php';	
		$('#formulario_productos')[0].reset();
		$('#formulario_productos #productos_id').val(data.productos_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formulario_productos').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formulario_productos').attr({ 'data-form': 'update' }); 
				$('#formulario_productos').attr({ 'action': '<?php echo SERVERURL; ?>php/productos/modificarProductos.php' }); 
				
				evaluarCategoriaDetalle(valores[13]);				
				
				//DESHABILITAR OBJETOS
				$('#formulario_productos #categoria').attr("disabled", true);				
				$('#formulario_productos #medida').attr("disabled", true);
				$('#formulario_productos #almacen').attr("disabled", true);
				
				//HABIITAR OBJETOS
				$('#formulario_productos #nombre').attr("readonly", false);
				$('#formulario_productos #descripcion').attr("readonly", false);
				$('#formulario_productos #producto_activo').attr("disabled", false);
				$('#formulario_productos #producto_isv_factura').attr("disabled", false);	
				
				$('#formulario_productos #concentracion').val(valores[2]);
				$('#formulario_productos #cantidad').attr("readonly", true);
				
				$('#reg_producto').hide();
				$('#edi_producto').show();
				$('#delete_producto').hide();
				$('#formulario_productos #nombre').val(valores[0]);
				$('#formulario_productos #categoria').val(valores[1]);
				$('#formulario_productos #medida').val(valores[3]);
				$('#formulario_productos #almacen').val(valores[4]);	
				$('#formulario_productos #cantidad').val(valores[5]);
				$('#formulario_productos #precio_compra').val(valores[6]);
				$('#formulario_productos #precio_venta').val(valores[7]);
				$('#formulario_productos #descripcion').val(valores[8]);
				$('#formulario_productos #cantidad_minima').val(valores[11]);
				$('#formulario_productos #cantidad_maxima').val(valores[12]);				
				$('#formulario_productos #categoria_id').val(valores[16]);
				caracteresDescripcion();

				if(valores[9] == 1){
					$('#formulario_productos #producto_activo').prop('checked', true);
				}else{
					$('#formulario_productos #producto_activo').prop('checked', false);					
				}

				if(valores[10] == 1){
					$('#formulario_productos #producto_isv_factura').prop('checked', true);	
					$('#formulario_productos #label_producto_isv_factura').html("Sí");
				}else{
					$('#formulario_productos #producto_isv_factura').prop('checked', false);							
				    $('#formulario_productos #label_producto_isv_factura').html("No");
				}				
				
				$('#formulario_productos #concentracion').attr("readonly", true);				
						
				$('#formulario_productos #pro').val("Editar");
				$('#modal_productos').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			}
		});			
	});
}

var delete_productos_dataTable = function(tbody, table){
	$(tbody).off("click", "button.delete");
	$(tbody).on("click", "button.delete", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		var url = '<?php echo SERVERURL; ?>php/productos/editarProductos.php';	
		$('#formulario_productos')[0].reset();
		$('#formulario_productos #productos_id').val(data.productos_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formulario_productos').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formulario_productos').attr({ 'data-form': 'delete' }); 
				$('#formulario_productos').attr({ 'action': '<?php echo SERVERURL; ?>php/productos/eliminarProductos.php' }); 
				$('#reg_producto').hide();
				$('#edi_producto').hide();
				$('#delete_producto').show();
								
				$('#formulario_productos #nombre').val(valores[0]);
				$('#formulario_productos #categoria').val(valores[1]);
				$('#formulario_productos #concentracion').val(valores[2]);
				$('#formulario_productos #medida').val(valores[3]);
				$('#formulario_productos #almacen').val(valores[4]);				
				$('#formulario_productos #cantidad').val(valores[5]);
				$('#formulario_productos #precio_compra').val(valores[6]);
				$('#formulario_productos #precio_venta').val(valores[7]);
				$('#formulario_productos #descripcion').val(valores[8]);
				$('#formulario_productos #cantidad_minima').val(valores[11]);
				$('#formulario_productos #cantidad_maxima').val(valores[12]);				
				caracteresDescripcion();

				if(valores[9] == 1){
					$('#formulario_productos #producto_activo').prop('checked', true);
				}else{
					$('#formulario_productos #producto_activo').prop('checked', false);					
				}

				if(valores[10] == 1){
					$('#formulario_productos #producto_isv_factura').prop('checked', true);	
					$('#formulario_productos #label_producto_isv_factura').html("Sí");					
				}else{
					$('#formulario_productos #producto_isv_factura').prop('checked', false);
					$('#formulario_productos #label_producto_isv_factura').html("No");					
				}				
				
				//DESHABILITAR OBJETOS
				$('#formulario_productos #nombre').attr("readonly", true);
				$('#formulario_productos #categoria').attr("disabled", true);				
				$('#formulario_productos #medida').attr("disabled", true);
				$('#formulario_productos #almacen').attr("disabled", true);
				$('#formulario_productos #cantidad').attr("readonly", true);
				$('#formulario_productos #precio_compra').attr("readonly", true);
				$('#formulario_productos #precio_venta').attr("readonly", true);
				$('#formulario_productos #cantidad_minima').attr("readonly", true);
				$('#formulario_productos #cantidad_maxima').attr("readonly", true);				
				$('#formulario_productos #descripcion').attr("readonly", true);	
				$('#formulario_productos #producto_activo').attr("disabled", true);
				$('#formulario_productos #producto_isv_factura').attr("disabled", true);					
										
				$('#formulario_productos #pro').val("Eliminar");
				$('#modal_productos').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			}
		});			
	});
}

//INICIO BOTONES DE producto_activo EN LOS FORMULARIOS
	$('#formulario_productos #label_producto_activo').html("Activo");
	
    $('#formulario_productos .switch').change(function(){    
        if($('input[name=producto_activo]').is(':checked')){
            $('#formulario_productos #label_producto_activo').html("Activo");
            return true;
        }
        else{
            $('#formulario_productos #label_producto_activo').html("Inactivo");
            return false;
        }
    });		
		
	$('#formulario_productos #label_producto_isv_factura').html("No");
	
    $('#formulario_productos .switch').change(function(){    
        if($('input[name=producto_isv_factura]').is(':checked')){
            $('#formulario_productos #label_producto_isv_factura').html("Sí");
            return true;
        }
        else{
            $('#formulario_productos #label_producto_isv_factura').html("No");
            return false;
        }
    });		
//FIN BOTONES DE producto_activo EN LOS FORMULARIOS	

//INICIO BOTON producto_activo
$('#formulario_productos #label_producto_activo').html("Activo");

$('#formulario_productos .switch').change(function(){    
	if($('input[name=producto_activo]').is(':checked')){
		$('#formulario_productos #label_producto_activo').html("Activo");
		return true;
	}
	else{
		$('#formulario_productos #label_producto_activo').html("Inactivo");
		return false;
	}
});		
	
$('#formulario_productos #label_producto_isv_factura').html("No");

$('#formulario_productos .switch').change(function(){    
	if($('input[name=producto_isv_factura]').is(':checked')){
		$('#formulario_productos #label_producto_isv_factura').html("Sí");
		return true;
	}
	else{
		$('#formulario_productos #label_producto_isv_factura').html("No");
		return false;
	}
});
//FIN BOTON producto_activo
</script>