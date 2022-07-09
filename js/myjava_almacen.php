<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#modal_almacen").on('shown.bs.modal', function(){
        $(this).find('#formulario_almacen #almacen').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$(document).ready(function() {
	funciones();
	listar_almacen();	
});	

function funciones(){
	getUbicacion();
}	

function agregarAlmacen(){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		funciones();
		$('#formulario_almacen').attr({ 'data-form': 'save' });
		$('#formulario_almacen').attr({ 'action': '<?php echo SERVERURL; ?>php/almacen/agregarAlmacen.php' });			
		$('#reg_almacen').show();
		$('#edi_almacen').hide();
		$('#delete_almacen').hide();		
		$('#formulario_almacen')[0].reset();	
		$('#formulario_almacen #pro').val('Registro');
		$("#formulario_almacen #fecha").attr('readonly', false);
		
		//HABILITAR OBJETOS
		 $('#formulario_almacen #almacen').attr("readonly", false);				
		 $('#formulario_almacen #ubicacion').attr("disabled", false);
				
		 $('#modal_almacen').modal({
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

var listar_almacen = function(){
	var table_almacen  = $("#dataTableAlmacen").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/almacen/getAlmacenTabla.php"
		},		
		"columns":[
			{"data":"almacen"},
			{"data":"ubicacion"},			
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
				titleAttr: 'Actualizar Almacén',
				className: 'btn btn-info',
				action: 	function(){
					listar_almacen();
				}
			},		
			{
				text:      '<i class="fab fas fa-warehouse fa-lg"></i> Crear',
				titleAttr: 'Agregar Almacén',
				className: 'btn btn-primary',
				action: 	function(){
					agregarAlmacen();
				}
			},				
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				title: 'Reporte Almacén',
				className: 'btn btn-success'				
			},
			{
				extend:    'pdf',
				orientation: 'landscape',
				text:      '<i class="fas fa-file-pdf fa-lg"></i> PDF',
				titleAttr: 'PDF',
				title: 'Reporte Almacén',
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
			}/*,
			{
				extend:    'print',
				text:      '<i class="fas fa-print fa-lg"></i>',
				titleAttr: 'Imprimir',
				title: 'Reporte Productos',				
				className: 'btn btn-secondary'					
			}*/
		]		
	});	 
	table_almacen.search('').draw();
	$('#buscar').focus();
	
	edit_alamcen_dataTable("#dataTableAlmacen tbody", table_almacen);
	delete_almacen_dataTable("#dataTableAlmacen tbody", table_almacen);
}

var edit_alamcen_dataTable = function(tbody, table){
	$(tbody).off("click", "button.editar");
	$(tbody).on("click", "button.editar", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		var url = '<?php echo SERVERURL; ?>php/almacen/editarAlmacen.php';	
		$('#formulario_almacen')[0].reset();
		$('#formulario_almacen #almacen_id').val(data.almacen_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formulario_almacen').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formulario_almacen').attr({ 'data-form': 'update' }); 
				$('#formulario_almacen').attr({ 'action': '<?php echo SERVERURL; ?>php/almacen/modificarAlmacen.php' }); 
				$('#reg_almacen').hide();
				$('#edi_almacen').show();
				$('#delete_almacen').hide();
				$('#formulario_almacen #almacen').val(valores[0]);
				$('#formulario_almacen #ubicacion').val(valores[1]);

				//HABILITAR OBJETOS
				$('#formulario_almacen #almacen').attr("readonly", false);	

				//DESHABILITAR OBJETOS
				$('#formulario_almacen #ubicacion').attr("disabled", true);
				
				$('#formulario_almacen #pro').val("Editar");
				$('#modal_almacen').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			}
		});			
	});
}

var delete_almacen_dataTable = function(tbody, table){
	$(tbody).off("click", "button.delete");
	$(tbody).on("click", "button.delete", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		var url = '<?php echo SERVERURL; ?>php/almacen/editarAlmacen.php';	
		$('#formulario_almacen')[0].reset();
		$('#formulario_almacen #almacen_id').val(data.almacen_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formulario_almacen').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formulario_almacen').attr({ 'data-form': 'update' }); 
				$('#formulario_almacen').attr({ 'action': '<?php echo SERVERURL; ?>php/almacen/eliminarAlmacen.php' }); 
				$('#reg_almacen').hide();
				$('#edi_almacen').hide();
				$('#delete_almacen').show();
				$('#formulario_almacen #almacen').val(valores[0]);
				$('#formulario_almacen #ubicacion').val(valores[1]);

				//DESHABILITAR OBJETOS
				$('#formulario_almacen #almacen').attr("readonly", true);				
				$('#formulario_almacen #ubicacion').attr("disabled", true);
				
				$('#formulario_almacen #pro').val("Eliminar");
				$('#modal_almacen').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			}
		});			
	});
}

function getUbicacion(){
    var url = '<?php echo SERVERURL; ?>php/almacen/getUbicacion.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#formulario_almacen #ubicacion').html("");
			$('#formulario_almacen #ubicacion').html(data);			
		}			
     });		
}
</script>