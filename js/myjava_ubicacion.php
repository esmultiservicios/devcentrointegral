<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#modal_ubicacion").on('shown.bs.modal', function(){
        $(this).find('#formulario_ubicacion #ubicacion').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$(document).ready(function() {
	listar_ubicacion();
	funciones();	
});	

function funciones(){
	getEmpresa();
}	

function agregarUbicacion(){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		$('#formulario_ubicacion').attr({ 'data-form': 'save' });
		$('#formulario_ubicacion').attr({ 'action': '<?php echo SERVERURL; ?>php/ubicacion/agregarUbicacion.php' });			
		$('reg_ubicacion').show();
		$('#edi_ubicacion').hide();
		$('#delete_ubicacion').hide();		
		$('#formulario_ubicacion')[0].reset();	
		$('#formulario_ubicacion #pro').val('Registro');
		$("#formulario_ubicacion #fecha").attr('readonly', false);
		
		//HABILITAR OBJETOS
		$('#formulario_ubicacion #ubicacion').attr("readonly", false);				
		$('#formulario_ubicacion #empresa').attr("disabled", false);		
		
		 $('#modal_ubicacion').modal({
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

var listar_ubicacion = function(){
	var table_ubicacion  = $("#dataTableUbicacion").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/ubicacion/getUbicacionTabla.php"
		},		
		"columns":[
			{"data":"ubicacion"},
			{"data":"empresa"},			
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
				titleAttr: 'Actualizar Ubicación',
				className: 'btn btn-info',
				action: 	function(){
					listar_ubicacion();
				}
			},			
			{
				text:      '<i class="fas fa-search-location fa-lg"></i> Crear',
				titleAttr: 'Agregar Ubicación',
				className: 'btn btn-primary',
				action: 	function(){
					agregarUbicacion();
				}
			},				
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				title: 'Reporte Ubicación',
				className: 'btn btn-success'				
			},
			{
				extend:    'pdf',
				orientation: 'landscape',
				text:      '<i class="fas fa-file-pdf fa-lg"></i> PDF',
				titleAttr: 'PDF',
				title: 'Reporte Ubicación',
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
	table_ubicacion.search('').draw();
	$('#buscar').focus();
	
	edit_ubicacion_dataTable("#dataTableUbicacion tbody", table_ubicacion);
	delete_ubicacion_dataTable("#dataTableUbicacion tbody", table_ubicacion);
}

var edit_ubicacion_dataTable = function(tbody, table){
	$(tbody).off("click", "button.editar");
	$(tbody).on("click", "button.editar", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		var url = '<?php echo SERVERURL; ?>php/ubicacion/editarUbicacion.php';	
		$('#formulario_almacen')[0].reset();
		$('#formulario_ubicacion #ubicacion_id').val(data.ubicacion_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formulario_ubicacion').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formulario_ubicacion').attr({ 'data-form': 'update' }); 
				$('#formulario_ubicacion').attr({ 'action': '<?php echo SERVERURL; ?>php/ubicacion/modificarUbicacion.php' }); 
				$('#reg_ubicacion').hide();
				$('#edi_ubicacion').show();
				$('#delete_ubicacion').hide();
				$('#formulario_ubicacion #ubicacion').val(valores[0]);
				$('#formulario_ubicacion #empresa').val(valores[1]);

				//HABILITAR OBJETOS
				$('#formulario_ubicacion #ubicacion').attr("readonly", false);	

				//DESHABILITAR OBJETOS
				$('#formulario_ubicacion #empresa').attr("disabled", true);
				
				$('#formulario_ubicacion #pro').val("Editar");
				$('#modal_ubicacion').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			}
		});			
	});
}

var delete_ubicacion_dataTable = function(tbody, table){
	$(tbody).off("click", "button.delete");
	$(tbody).on("click", "button.delete", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		var url = '<?php echo SERVERURL; ?>php/ubicacion/editarUbicacion.php';	
		$('#formulario_ubicacion')[0].reset();
		$('#formulario_ubicacion #ubicacion_id').val(data.ubicacion_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formulario_ubicacion').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formulario_ubicacion').attr({ 'data-form': 'update' }); 
				$('#formulario_ubicacion').attr({ 'action': '<?php echo SERVERURL; ?>php/ubicacion/eliminarUbicacion.php' }); 
				$('#reg_ubicacion').hide();
				$('#edi_ubicacion').hide();
				$('#delete_ubicacion').show();
				$('#formulario_ubicacion #ubicacion').val(valores[0]);
				$('#formulario_ubicacion #empresa').val(valores[1]);

				//DESHABILITAR OBJETOS
				$('#formulario_ubicacion #ubicacion').attr("readonly", true);				
				$('#formulario_ubicacion #empresa').attr("disabled", true);
				
				$('#formulario_ubicacion #pro').val("Eliminar");
				$('#modal_ubicacion').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			}
		});			
	});
}

function getEmpresa(){
    var url = '<?php echo SERVERURL; ?>php/ubicacion/getEmpresa.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#formulario_ubicacion #empresa').html("");
			$('#formulario_ubicacion #empresa').html(data);			
		}			
     });		
}
</script>