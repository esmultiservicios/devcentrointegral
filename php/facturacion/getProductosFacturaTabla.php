<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT p.productos_id AS 'productos_id', p.nombre AS 'producto', p.descripcion AS 'descripcion', p.concentracion AS 'concentracion', p.cantidad AS 'cantidad', m.nombre AS 'medida', p.precio_compra AS 'precio_compra', p.precio_venta AS 'precio_venta', a.nombre AS 'almacen', u.nombre AS 'ubicacion', cp.nombre AS 'categoria', cp.categoria_producto_id AS 'categoria_producto_id', (CASE WHEN p.estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado', (CASE WHEN p.isv = '1' THEN 'Sí' ELSE 'No' END) AS 'isv', p.isv AS 'impuesto_venta'
FROM productos AS p
INNER JOIN medida AS m
ON p.medida_id = m.medida_id
INNER JOIN almacen AS a
ON p.almacen_id = a.almacen_id
INNER JOIN ubicacion AS u
ON a.ubicacion_id = u.ubicacion_id
INNER JOIN categoria_producto AS cp
ON p.categoria_producto_id = cp.categoria_producto_id
WHERE cp.nombre NOT IN ('Insumos')
GROUP BY p.productos_id
ORDER BY p.nombre ASC";
$result = $mysqli->query($consulta);	

$arreglo = array();

while($data = $result->fetch_assoc()){				
	$arreglo["data"][] = $data;		
}

echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>