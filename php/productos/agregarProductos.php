<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$nombre = cleanStringStrtolower($_POST['nombre']);

if(isset($_POST['categoria'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['categoria'] == ""){
		$categoria = 0;
	}else{
		$categoria = $_POST['categoria'];
	}
}else{
	$categoria = 0;
}

if(isset($_POST['medida'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['medida'] == ""){
		$medida = 0;
	}else{
		$medida = $_POST['medida'];
	}
}else{
	$medida = 0;
}

if(isset($_POST['almacen'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['almacen'] == ""){
		$almacen = 0;
	}else{
		$almacen = $_POST['almacen'];
	}
}else{
	$almacen = 0;
}

$concentracion = $_POST['concentracion'];
$cantidad = $_POST['cantidad'];
$precio_compra = $_POST['precio_compra'];
$precio_venta = $_POST['precio_venta'];
$cantidad_minima = $_POST['cantidad_minima'];
$cantidad_maxima = $_POST['cantidad_maxima'];
$categoria_id = $_POST['categoria_id'];

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

$descripcion = cleanStringStrtolower($_POST['descripcion']);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

//VERIFICAMOS QUE NO EXISTA EL REGISTRO
$query = "SELECT productos_id
	FROM productos
	WHERE nombre = '$nombre'";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows==0){
	$productos_id  = correlativo('productos_id  ', 'productos');
	$insert = "INSERT INTO productos 
		VALUES('$productos_id','$almacen','$medida','$concentracion','$nombre','$descripcion','$categoria','$cantidad','$precio_compra','$precio_venta','$cantidad_minima','$cantidad_maxima','$estado','$isv','$usuario','$fecha_registro','$categoria_id')";
	$query = $mysqli->query($insert) or die($mysqli->error);

    if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_productos",
			5 => "Registro",
			6 => "Productos",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_productos", //Modals Para Cierre Automatico
		);
		
		//CONSULTAMOS LA CATEGORIA DEL PRODUCTOS
		$query_categoria = "SELECT nombre
			FROM categoria_producto
			WHERE categoria_producto_id  = '$categoria'";
		$result_categoria = $mysqli->query($query_categoria) or die($mysqli->error);
		
		$categoria_producto = "";
		
		if($result_categoria->num_rows > 0){
			$valores2 = $result_categoria->fetch_assoc();

			$categoria_producto = $valores2['nombre'];			
		}
		
		//ACTUALIZAMOS LOS MOVIMIENTOS DE LOS PRODUCTOS
		if ($categoria_producto == "Producto" || $categoria_producto == "Insumos"){
			$movimientos_id  = correlativo('movimientos_id  ', 'movimientos');
			$documento = "Entrada Productos ".$movimientos_id;
			$insert = "INSERT INTO movimientos VALUES('$movimientos_id','$productos_id','$documento','$cantidad','$0','$cantidad','$fecha_registro')";
			$mysqli->query($insert) or die($mysqli->error);			
		}		
		
		/*********************************************************************************************************************************************************************/
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado un nuevo producto: $nombre con codigo: $productos_id";
		$modulo = "Productos";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$productos_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
		$mysqli->query($insert) or die($mysqli->error);
		/*********************************************************************************************************************************************************************/		
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
}else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos este registro ya existe no se puede almacenar", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",		
	);
}

echo json_encode($datos);
?>