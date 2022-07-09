<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$html = '';
$key = $_POST['key'];

$query = "SELECT p.productos_id AS 'productos_id', p.nombre AS 'producto', p.concentracion AS 'concentracion', m.nombre AS 'medida', cp.nombre AS 'categoria', p.precio_venta AS 'precio_venta', p.precio_compra AS 'precio_compra', a.nombre AS 'almacen'
	FROM productos AS p
	INNER JOIN almacen AS a
	ON p.almacen_id = a.almacen_id
	INNER JOIN medida AS m
	ON p.medida_id = m.medida_id
	INNER JOIN categoria_producto AS cp
	ON p.categoria_producto_id = cp.categoria_producto_id
	WHERE p.estado = 1 AND p.nombre LIKE '".strip_tags($key)."%'
	ORDER BY p.nombre";
$result = $mysqli->query($query) or die($mysqli->error);	

while ($row = $result->fetch_assoc()) { 
    $producto_ = cleanString(str_replace($row['concentracion'], "", $row['producto']));
	$producto = $producto_.' '.$row['concentracion'].' '.$row['medida'].'';
	
	$html .= '<div><a style="text-decoration:none;" class="suggest-element" data="'.utf8_encode($producto).'" id="'.$row['productos_id'].'">'.utf8_encode($producto).'</a></div>';
}
echo $html;
?>