<?php	
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$productos_id  = $_POST['productos_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//VERIFICAMOS SI EL PRODUCTO EXISTE EN LA FACTURACION O EN LAS COMPRAS
$query_facturas = "SELECT facturas_detalle_id
	FROM facturas_detalle
	WHERE productos_id = '$productos_id'";
$result_facturas = $mysqli->query($query_facturas) or die($mysqli->error);

$query_compras = "SELECT compras_detalles_id
	FROM compras_detalles
	WHERE productos_id = '$productos_id'";
$result_compras = $mysqli->query($query_compras) or die($mysqli->error);

$query_movimientos = "SELECT movimientos_id 
	FROM movimientos
	WHERE productos_id = '$productos_id'";
$result_movimientos = $mysqli->query($query_movimientos) or die($mysqli->error);

if($result_facturas->num_rows >=1 || $result_compras->num_rows >= 1 || $result_movimientos->num_rows >= 1){
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos este registro cuenta con información almacenada (en facturación, compras o movimientos) no se puede eliminar",
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",		
	);	
}else{
	$delete = "DELETE FROM productos WHERE productos_id  = '$productos_id'";
	$query = $mysqli->query($delete) or die($mysqli->error);
	
	if($query){
		$datos = array(
			0 => "Eliminado", 
			1 => "Registro Eliminado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_productos",
			5 => "Eliminar",
			6 => "Productos",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_productos", //Modals Para Cierre Automatico
		);		

		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha eliminado el prodcuto con código $productos_id";
		$modulo = "Productos";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$productos_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
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
}	

echo json_encode($datos);
?>