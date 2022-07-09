<?php	
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$medida_id = $_POST['medida_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//VERIFICAMOS SI LA UNIDAD DE MEDIDA EXISTE EN LA ENTIDAD PRODCUTOS
$query_medidas = "SELECT productos_id  
	FROM productos
	WHERE medida_id = '$medida_id'";
$result_medidas = $mysqli->query($query_medidas) or die($mysqli->error);

if($result_medidas->num_rows ==0){
	$delete = "DELETE FROM medida WHERE medida_id = '$medida_id'";
	$query = $mysqli->query($delete) or die($mysqli->error);
	
	if($query){
		$datos = array(
			0 => "Eliminado", 
			1 => "Registro Eliminado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_medidas",
			5 => "Eliminar",
			6 => "Medidas",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_medidas", //Modals Para Cierre Automatico
			8 => "",
			9 => "Eliminar", //PERMITE CERRAR EL MODAL SEGUN EL INDICADOR este indicador esta en main.js			
		);	

		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha eliminado la unidad de medida con código $medida_id";
		$modulo = "Medidas";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$medida_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
		$mysqli->query($insert) or die($mysqli->error);
		/*********************************************************************************************************************************************************************/		
	/*********************************************************************************************************************************************************************/		
	}else{
		$datos = array(
			0 => "Error", 
			1 => "No se puedo eliminar este registro, los datos son incorrectos por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);
	}
}else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos este registro cuenta con información almacenada no se puede eliminar", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",		
	);	
}

echo json_encode($datos);
?>