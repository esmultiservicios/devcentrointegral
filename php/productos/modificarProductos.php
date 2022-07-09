<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$nombre = $_POST['nombre'];
$productos_id = $_POST['productos_id'];
$cantidad = $_POST['cantidad'];
$precio_compra = $_POST['precio_compra'];
$precio_venta = $_POST['precio_venta'];
$cantidad_minima = $_POST['cantidad_minima'];
$cantidad_maxima = $_POST['cantidad_maxima'];
$categoria_id = $_POST['categoria_id'];
$descripcion = cleanStringStrtolower($_POST['descripcion']);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

if(isset($_POST['producto_activo'])){
	if(isset($_POST['producto_activo'])){
		$estado = $_POST['producto_activo'];
	}else{
		$estado = 2;
	}	
}else{
	$estado = 2;
}

if(isset($_POST['producto_isv_factura'])){
	if(isset($_POST['producto_isv_factura'])){
		$isv = $_POST['producto_isv_factura'];
	}else{
		$isv = 2;
	}	
}else{
	$isv = 2;
}

$update = "UPDATE productos
	SET
		nombre = '$nombre',
		cantidad = '$cantidad',
		precio_compra = '$precio_compra',
		precio_venta = '$precio_venta',
		estado = '$estado',
		isv = '$isv',
		descripcion = '$descripcion',
		cantidad_minima = '$cantidad_minima',
		cantidad_maxima = '$cantidad_maxima',
		categoria_id = '$categoria_id'
	WHERE productos_id = '$productos_id'";
$query = $mysqli->query($update) or die($mysqli->error);

if($query){
	$datos = array(
		0 => "Editado", 
		1 => "Registro Editado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "",
		5 => "Editar",
		6 => "Productos",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "modal_productos", //Modals Para Cierre Automatico
	);	
	
	/*********************************************************************************************************************************************************************/
	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Agregar";
	$observacion_historial = "Se ha modificado el producto producto: $nombre con codigo: $productos_id";
	$modulo = "Productos";
	$insert = "INSERT INTO historial 
	   VALUES('$historial_numero','0','0','$modulo','$productos_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
	$mysqli->query($insert) or die($mysqli->error);
	/*********************************************************************************************************************************************************************/		
	/*********************************************************************************************************************************************************************/		
}else{
	$datos = array(
		0 => "Error", 
		1 => "No se puedo modificar este registro, los datos son incorrectos por favor corregir", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);	
}

echo json_encode($datos);
?>