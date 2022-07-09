<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$producto_id = $_POST['productos_id'];
//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT p.productos_id AS 'productos_id', p.nombre AS 'producto', p.descripcion AS 'descripcion', p.cantidad AS 'cantidad', m.nombre AS 'medida', p.precio_venta AS 'precio_venta'
FROM productos AS p
INNER JOIN medida AS m
ON p.medida_id = m.medida_id
WHERE p.productos_id = '$producto_id'";
$result = $mysqli->query($consulta);

$productos_id = ""; 
$producto = "";
$precio_venta = "";
	 
if($result->num_rows>0){
    $consulta_registro = $result->fetch_assoc(); 
	$productos_id = $consulta_registro['productos_id'];
	$producto = $consulta_registro['producto'];
	$precio_venta = $consulta_registro['precio_venta'];	
}

$datos = array(
	 0 => $productos_id, 
	 1 => $producto, 
	 2 => $precio_venta,	 
);

echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>