<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#modal_medidas").on('shown.bs.modal', function(){
        $(this).find('#formulario_medidas #medidas').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$(document).ready(function() {
	listar_medidas();	
});	

function agregarMedidas(){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		$('#formulario_medidas').attr({ 'data-form': 'save' });
		$('#formulario_medidas').attr({ 'action': '<?php echo SERVERURL; ?>php/medidas/agregarMedidas.php' });			
		$('#reg_medidas').show();
		$('#edi_medidas').hide();
		$('#delete_medidas').hide();		
		$('#formulario_medidas')[0].reset();	
		$('#formulario_medidas #pro').val('Registro');
		$("#formulario_medidas #fecha").attr('readonly', false);
		
		//HABILITAR OBJETOS
		$('#formulario_medidas #medidas').attr("readonly", false);				
		$('#formulario_medidas #descripcion_medidas').attr("readonly", false);
				
		 $('#modal_medidas').modal({
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

var listar_medidas = function(){
	var table_medidas  = $("#dataTableMedidas").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/medidas/getMedidasTabla.php"
		},		
		"columns":[
			{"data":"nombre"},
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
				titleAttr: 'Actualizar Medidas',
				className: 'btn btn-info',
				action: 	function(){
					listar_medidas();
				}
			},		
			{
				text:      '<i class="fas fa-balance-scale-left fa-lg"></i> Crear',
				titleAttr: 'Agregar Medidas',
				className: 'btn btn-primary',
				action: 	function(){
					agregarMedidas();
				}
			},				
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				title: 'Reporte Medidas',
				className: 'btn btn-success'				
			},
			{
				extend:    'pdf',
				orientation: 'landscape',
				text:      '<i class="fas fa-file-pdf fa-lg"></i> PDF',
				titleAttr: 'PDF',
				title: 'Reporte Medidas',
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
	table_medidas.search('').draw();
	$('#buscar').focus();
	
	edit_alamcen_dataTable("#dataTableMedidas tbody", table_medidas);
	delete_almacen_dataTable("#dataTableMedidas tbody", table_medidas);
}

var edit_alamcen_dataTable = function(tbody, table){
	$(tbody).off("click", "button.editar");
	$(tbody).on("click", "button.editar", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		var url = '<?php echo SERVERURL; ?>php/medidas/editarMedidas.php';	
		$('#formulario_medidas')[0].reset();
		$('#formulario_medidas #medida_id').val(data.medida_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formulario_medidas').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formulario_medidas').attr({ 'data-form': 'update' }); 
				$('#formulario_medidas').attr({ 'action': '<?php echo SERVERURL; ?>php/medidas/modificarMedidas.php' }); 
				$('#reg_medidas').hide();
				$('#edi_medidas').show();
				$('#delete_medidas').hide();
				$('#formulario_medidas #medidas').val(valores[0]);
				$('#formulario_medidas #descripcion_medidas').val(valores[1]);

				//DESHABILITAR OBJETOS
				$('#formulario_medidas #medidas').attr("readonly", true);
				
				//HABILITAR OBJETOS				
				$('#formulario_medidas #descripcion_medidas').attr("readonly", false);
				
				$('#formulario_medidas #pro').val("Editar");
				$('#modal_medidas').modal({
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
		var url = '<?php echo SERVERURL; ?>php/medidas/editarMedidas.php';	
		$('#formulario_medidas')[0].reset();
		$('#formulario_medidas #medida_id').val(data.medida_id);
			
		$.ajax({
			type:'POST',
			url:url,
			data:$('#formulario_medidas').serialize(),
			success: function(registro){
				var valores = eval(registro);
				$('#formulario_medidas').attr({ 'data-form': 'update' }); 
				$('#formulario_medidas').attr({ 'action': '<?php echo SERVERURL; ?>php/medidas/eliminarMedidas.php' }); 
				$('#reg_medidas').hide();
				$('#edi_medidas').hide();
				$('#delete_medidas').show();
				$('#formulario_medidas #medidas').val(valores[0]);
				$('#formulario_medidas #descripcion_medidas').val(valores[1]);

				//DESHABILITAR OBJETOS
				$('#formulario_medidas #medidas').attr("readonly", true);				
				$('#formulario_medidas #descripcion_medidas').attr("readonly", true);
				
				$('#formulario_medidas #pro').val("Eliminar");
				$('#modal_medidas').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			}
		});			
	});
}
</script>