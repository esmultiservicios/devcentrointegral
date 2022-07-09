<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$movimiento_categoria = $_POST['movimiento_categoria'];
$movimiento_producto = $_POST['movimiento_producto'];
$movimiento_operacion = $_POST['movimiento_operacion'];
$movimiento_cantidad = $_POST['movimiento_cantidad'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

if($movimiento_operacion == 1){
	//CONSULTAMOS LA CANTIDAD EN LA ENTIDAD PRODUCTOS
	$query_productos = "SELECT cantidad
		FROM productos
		WHERE productos_id = '$movimiento_producto'";
	$result_productos = $mysqli->query($query_productos) or die($mysqli->error);			  

	$cantidad_productos = "";
	
	if($result_productos->num_rows>0){
		$consulta = $result_productos->fetch_assoc();
		$cantidad_productos = $consulta['cantidad'];
	}		
	
	$cantidad = $cantidad_productos +  $movimiento_cantidad;
	
	//ACTUALIZAMOS LA NUEVA CANTIDAD EN LA ENTIDAD PRODUCTOS
	$update_productos = "UPDATE productos
		SET
			cantidad = '$cantidad'
		WHERE productos_id = '$movimiento_producto'";
	$mysqli->query($update_productos);
	
	//CONSULTAMOS EL SALDO DEL PRODUCTO EN LA ENTIDAD MOVIMIENTOS
	$query_movimientos = "SELECT saldo
		FROM movimientos
		WHERE productos_id = '$movimiento_producto'
		ORDER BY movimientos_id DESC LIMIT 1";
	$result_movimientos = $mysqli->query($query_movimientos) or die($mysqli->error);
	
	$saldo_productos = 0;
	
	if($result_movimientos->num_rows>0){
		$consulta = $result_movimientos->fetch_assoc();
		$saldo_productos = $consulta['saldo'];
	}
	
	$saldo = $saldo_productos + $movimiento_cantidad;
	$salida = 0;
	//INGRESAMOS LOS PRODUCTOS
	$movimientos_id   = correlativo('movimientos_id', 'movimientos');
	$documento = "Entrada Movimientos ".$movimientos_id;
	$insert = "INSERT INTO movimientos 
		VALUES('$movimientos_id','$movimiento_producto','$documento','$movimiento_cantidad','$salida','$saldo','$fecha_registro')";
	$query = $mysqli->query($insert);
	
    if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formularioMovimientos",
			5 => "Registro",
			6 => "Movimientos",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_movimientos", //Modals Para Cierre Automatico
		);	
	}else{
		$datos = array(
			0 => "Error", 
			1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);
	}		
}else if($movimiento_operacion == 2){
	//CONSULTAMOS LA CANTIDAD EN LA ENTIDAD PRODUCTOS
	$query_productos = "SELECT cantidad
		FROM productos
		WHERE  productos_id = '$movimiento_producto'";
	$result_productos = $mysqli->query($query_productos) or die($mysqli->error);			  

	$cantidad_productos = "";
	
	if($result_productos->num_rows>0){
		$consulta = $result_productos->fetch_assoc();
		$cantidad_productos = $consulta['cantidad'];
	}		
	
	$cantidad = $cantidad_productos -  $movimiento_cantidad;
	//ACTUALIZAMOS LA NUEVA CANTIDAD EN LA ENTIDAD PRODUCTOS
	$update_productos = "UPDATE productos
		SET
			cantidad = '$cantidad'
		WHERE productos_id = '$movimiento_producto'";
	$mysqli->query($update_productos);
	
	//CONSULTAMOS EL SALDO DEL PRODUCTO EN LA ENTIDAD MOVIMIENTOS
	$query_movimientos = "SELECT saldo
		FROM movimientos
		WHERE productos_id = '$movimiento_producto'
		ORDER BY movimientos_id DESC LIMIT 1";
	$result_movimientos = $mysqli->query($query_movimientos) or die($mysqli->error);
	
	$saldo_productos = 0;
	
	if($result_movimientos->num_rows>0){
		$consulta = $result_movimientos->fetch_assoc();
		$saldo_productos = $consulta['saldo'];
	}
	
	$saldo = $saldo_productos - $movimiento_cantidad;
	$entrada = 0;
	//INGRESAMOS LOS PRODUCTOS
	$movimientos_id   = correlativo('movimientos_id', 'movimientos');
	$documento = "Salida Movimientos ".$movimientos_id;
	$insert = "INSERT INTO movimientos 
		VALUES('$movimientos_id','$movimiento_producto','$documento','$entrada','$movimiento_cantidad','$saldo','$fecha_registro')";
	$query = $mysqli->query($insert);		
		
    if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formularioMovimientos",
			5 => "Registro",
			6 => "Movimientos",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_movimientos", //Modals Para Cierre Automatico
		);	
	}else{
		$datos = array(
			0 => "Error", 
			1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);
	}	
}

echo json_encode($datos);
?>