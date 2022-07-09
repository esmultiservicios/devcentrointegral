<?php	
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$ubicacion_id = $_POST['ubicacion_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//VERIFICAMOS SI EL PRODUCTO EXISTE EN LA FACTURACION O EN LAS COMPRAS
$query_facturas = "SELECT a.ubicacion_id
	FROM productos AS p
	INNER JOIN almacen AS a
	ON p.almacen_id = a.almacen_id
	WHERE a.ubicacion_id = '$ubicacion_id'";
$result_facturas = $mysqli->query($query_facturas) or die($mysqli->error);


if($result_facturas->num_rows ==0){
	$delete = "DELETE FROM ubicacion WHERE ubicacion_id = '$ubicacion_id'";
	$query = $mysqli->query($delete) or die($mysqli->error);
	
	if($query){
		$datos = array(
			0 => "Eliminado", 
			1 => "Registro Eliminado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_ubicacion",
			5 => "Eliminar",
			6 => "Ubicacion",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_ubicacion", //Modals Para Cierre Automatico
			8 => "",
			9 => "Eliminar", //PERMITE CERRAR EL MODAL SEGUN EL INDICADOR este indicador esta en main.js			
		);			
	
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha eliminado la ubicación con código $ubicacion_id";
		$modulo = "Ubicacion";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$ubicacion_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
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