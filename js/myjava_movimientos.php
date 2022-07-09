<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#modal_movimientos").on('shown.bs.modal', function(){
        $(this).find('#formularioMovimientos #movimiento_categoria').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$(document).ready(function() {
	funciones();

	$('#form_main #categoria_id').on('change', function(){	
		listar_movimientos();
	});
	
	$('#form_main #fechai').on('change', function(){	
		listar_movimientos();
	});

	$('#form_main #fechaf').on('change', function(){	
		listar_movimientos();
	});	
});	

$('#form_main #registrar').on('click', function(e){
	e.preventDefault();
	agregarMovimientos();
});

$('#formularioMovimientos #buscar_productos').on('click', function(e){
	e.preventDefault();
	listar_productos_buscar();
	 $('#modal_busqueda_productos_facturas').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});	
});

function funciones(){
    listar_movimientos();
	getCategoriaProductosMovimientos();
	getCategoriaProductos();
	getCategoriaOperacion();
}

function agregarMovimientos(){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		funciones();
		$('#formularioMovimientos').attr({ 'data-form': 'save' });
		$('#formularioMovimientos').attr({ 'action': '<?php echo SERVERURL; ?>php/movimientos/agregarMovimientos.php' });			
		$('#modal_movimientos #pro').val("Proceso");
		$('#modal_movimientos').show();
				
		 $('#modal_movimientos').modal({
			show:true,
			keyboard: false,
			backdrop:'static'
		});			
	}else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});				
	} 	
}

var listar_movimientos = function(){
	var categoria;
	
	if ($('#form_main #categoria_id').val() == "" || $('#form_main #categoria_id').val() == null){
	  categoria = 1;	
	}else{
	  categoria = $('#form_main #categoria_id').val();
	}	

	var fechai = $("#form_main #fechai").val();
	var fechaf = $("#form_main #fechaf").val();
	
	var table_movimientos  = $("#dataTablaMovimientos").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/movimientos/getMovimientosTabla.php",
			"data":{
				"categoria":categoria,
				"fechai":fechai,
				"fechaf":fechaf				
			}			
		},		
		"columns":[
			{"data":"fecha_registro"},
			{"data":"producto"},
			{"data":"concentracion"},
			{"data":"medida"},			
			{"data":"entrada"},
			{"data":"salida"},
			{"data":"saldo"}
		],		
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,		
		"language": idioma_español//esta se encuenta en el archivo main.js
	});	 
	table_movimientos.search('').draw();
	$('#buscar').focus();
}

function getCategoriaProductos(){
    var url = '<?php echo SERVERURL; ?>php/movimientos/getCategoriaProducto.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#form_main #categoria_id').html("");
			$('#form_main #categoria_id').html(data);		
		}			
     });	
}

function getCategoriaProductosMovimientos(){
    var url = '<?php echo SERVERURL; ?>php/movimientos/getCategoriaProducto.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#formularioMovimientos #movimiento_categoria').html("");
			$('#formularioMovimientos #movimiento_categoria').html(data);		
		}			
     });	
}

function getCategoriaOperacion(){
    var url = '<?php echo SERVERURL; ?>php/movimientos/getOperacion.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#formularioMovimientos #movimiento_operacion').html("");
			$('#formularioMovimientos #movimiento_operacion').html(data);		
		}			
     });	
}

$(document).ready(function() {
	$('#formularioMovimientos #movimiento_categoria').on('change', function(){
		var categoria_producto_id;
		
		if ($('#formularioMovimientos #movimiento_categoria').val() == "" || $('#formularioMovimientos #movimiento_categoria').val() == null){
		  categoria_producto_id = 1;	
		}else{
		  categoria_producto_id = $('#formularioMovimientos #movimiento_categoria').val();
		}	
	
		getProductos(categoria_producto_id);
	  return false;			 				
    });					
});


function getProductos(categoria_producto_id ){
    var url = '<?php echo SERVERURL; ?>php/movimientos/getProductos.php';		
		
	$.ajax({
        type: "POST",
        url: url,
		data:'categoria_producto_id='+categoria_producto_id,
        success: function(data){	
		    $('#formularioMovimientos #movimiento_producto').html("");
			$('#formularioMovimientos #movimiento_producto').html(data);		
		}			
     });	
}

$('#form_main #actualizar').on('click', function(e){
	e.preventDefault();
	var categoria = $('#formularioMovimientos #movimiento_categoria').val();
	listar_movimientos();
	$('#formularioMovimientos #movimiento_categoria').val(categoria);
});

$('#form_main #reporte').on('click', function(e){
	e.preventDefault();
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){	
		reporteEXCEL();
	}else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});		
	}	
});

function reporteEXCEL(){
	var fecha = $('#form_main #fechai').val();
	var fechaf = $('#form_main #fechaf').val();
	var categoria;	
	
	if ($('#form_main #categoria_id').val() == "" || $('#form_main #categoria_id').val() == null){
	  categoria = 1;	
	}else{
	  categoria = $('#form_main #categoria_id').val();
	}
	
	var url = '<?php echo SERVERURL; ?>php/movimientos/reporte.php?fecha='+fecha+'&fechaf='+fechaf+'&categoria='+categoria;
    window.open(url);		 
}

var listar_productos_buscar = function(){
	var categoria_producto_id;
	
	if ($('#formularioMovimientos #movimiento_categoria').val() == "" || $('#formularioMovimientos #movimiento_categoria').val() == null){
	  categoria_producto_id = 1;	
	}else{
	  categoria_producto_id = $('#formularioMovimientos #movimiento_categoria').val();
	}	
		
	var table_productos_buscar = $("#dataTableProductosFacturas").DataTable({		
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/movimientos/getProductosTabla.php",
			"data":{
				"categoria":categoria_producto_id			
			}			
		},
		"columns":[
			{"defaultContent":"<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"},
			{"data":"producto"},
			{"data":"descripcion"},
			{"data":"concentracion"},
			{"data":"medida"},
			{"data":"cantidad"},
			{"data":"precio_venta"}				
		],
		"pageLength" : 5,
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,	
	});	 
	table_productos_buscar.search('').draw();
	$('#buscar').focus();
	
	view_productos_busqueda_dataTable("#dataTableProductosFacturas tbody", table_productos_buscar);
}

var view_productos_busqueda_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  
		$('#formularioMovimientos #movimiento_producto').val(data.productos_id);
		$('#modal_busqueda_productos_facturas').modal('hide');
	});
}
</script>