<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#modal_categoria").on('shown.bs.modal', function(){
        $(this).find('#formularioCategoria #categoria').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$(document).ready(function() {
	listar_categoria();	
});	

function agregarCategoria(){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		$('#formularioCategoria').attr({ 'data-form': 'save' });
		$('#formularioCategoria').attr({ 'action': '<?php echo SERVERURL; ?>php/categoria/agregarCategoria.php' });			
		$('#reg_categoria').show();
		$('#edi_categoria').hide();
		$('#delete_categoria').hide();		
		$('#formularioCategoria')[0].reset();	
		$('#formularioCategoria #pro').val('Registro');
		
		//HABILITAR OBJETOS
		$('#formularioCategoria #categoria').attr("readonly", false);				
				
		 $('#modal_categoria').modal({
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

var listar_categoria = function(){
	var table_categoria  = $("#dataTablCategoria").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/categoria/getCategoriaTabla.php"
		},		
		"columns":[
			{"data":"nombre"},		
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
				titleAttr: 'Actualizar Categoría',
				className: 'btn btn-info',
				action: 	function(){
					listar_categoria();
				}
			},		
			{
				text:      '<i class="fas fa-balance-scale-left fa-lg"></i> Crear',
				titleAttr: 'Agregar Categoría',
				className: 'btn btn-primary',
				action: 	function(){
					agregarCategoria();
				}
			},				
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				title: 'Reporte Categoría',
				className: 'btn btn-success'				
			},
			{
				extend:    'pdf',
				orientation: 'landscape',
				text:      '<i class="fas fa-file-pdf fa-lg"></i> PDF',
				titleAttr: 'PDF',
				title: 'Reporte Categoría',
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
	table_categoria.search('').draw();
	$('#buscar').focus();
	
	edit_categoria_dataTable("#dataTablCategoria tbody", table_categoria);
	delete_categoria_dataTable("#dataTablCategoria tbody", table_categoria);
}

var edit_categoria_dataTable = function(tbody, table){
	$(tbody).off("click", "button.editar");
	$(tbody).on("click", "button.editar", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		var url = '<?php echo SERVERURL; ?>php/categoria/editarCategoria.php';	
		$('#formularioCategoria')[0].reset();
		$('#formularioCategoria #categoria_id').val(data.categoria_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formularioCategoria').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formularioCategoria').attr({ 'data-form': 'update' }); 
				$('#formularioCategoria').attr({ 'action': '<?php echo SERVERURL; ?>php/categoria/modificarCategoria.php' }); 
				$('#reg_categoria').hide();
				$('#edi_categoria').show();
				$('#delete_categoria').hide();
				$('#formularioCategoria #categoria').val(valores[0]);

				if(valores[1] == 1){
					$('#formularioCategoria #categoria_activo').prop('checked', true);	
					$('#formularioCategoria #label_categoria_activo').html("Sí");
				}else{
					$('#formularioCategoria #categoria_activo').prop('checked', false);							
				    $('#formularioCategoria #label_categoria_activo').html("No");
				}					

				//DESHABILITAR OBJETOS
				$('#formularioCategoria #categoria').attr("readonly", false);
							
				$('#formularioCategoria #pro').val("Editar");
				$('#modal_categoria').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			}
		});			
	});
}

var delete_categoria_dataTable = function(tbody, table){
	$(tbody).off("click", "button.delete");
	$(tbody).on("click", "button.delete", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		var url = '<?php echo SERVERURL; ?>php/categoria/editarCategoria.php';	
		$('#formularioCategoria')[0].reset();
		$('#formularioCategoria #categoria_id').val(data.categoria_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formularioCategoria').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formularioCategoria').attr({ 'data-form': 'update' }); 
				$('#formularioCategoria').attr({ 'action': '<?php echo SERVERURL; ?>php/categoria/eliminarCategoria.php' }); 
				$('#reg_categoria').hide();
				$('#edi_categoria').hide();
				$('#delete_categoria').show();
				$('#formularioCategoria #categoria').val(valores[0]);

				if(valores[1] == 1){
					$('#formularioCategoria #categoria_activo').prop('checked', true);	
					$('#formularioCategoria #label_categoria_activo').html("Sí");
				}else{
					$('#formularioCategoria #categoria_activo').prop('checked', false);							
				    $('#formularioCategoria #label_categoria_activo').html("No");
				}					


				//DESHABILITAR OBJETOS
				$('#formularioCategoria #categoria').attr("readonly", true);				
				
				$('#formularioCategoria #pro').val("Eliminar");
				$('#modal_categoria').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			}
		});			
	});
}

$('#formularioCategoria #label_categoria_activo').html("Activo");
	
    $('#formularioCategoria .switch').change(function(){    
        if($('input[name=categoria_activo]').is(':checked')){
            $('#formularioCategoria #label_categoria_activo').html("Activo");
            return true;
        }
        else{
            $('#formularioCategoria #label_categoria_activo').html("Inactivo");
            return false;
        }
    });	
</script>