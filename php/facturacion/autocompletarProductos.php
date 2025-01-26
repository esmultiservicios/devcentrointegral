<?php
session_start();   
include "../funtions.php";

header("Content-Type: text/html;charset=utf-8");

//CONEXION A DB
$mysqli = connect_mysqli();	

$html = '';
$key = $_POST['key'];

$query = "SELECT p.productos_id AS productos_id, p.nombre AS producto, p.descripcion AS descripcion, p.cantidad AS cantidad, m.nombre AS medida, p.precio_venta AS precio_venta, p.concentracion AS concentracion, p.categoria_producto_id AS categoria_producto_id, cp.nombre AS categoria, (CASE WHEN p.estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado', (CASE WHEN p.isv = '1' THEN 'Sí' ELSE 'No' END) AS 'isv', p.isv AS 'impuesto_venta'
   FROM productos AS p
   INNER JOIN medida AS m
   ON p.medida_id = m.medida_id
   INNER JOIN categoria_producto AS cp
   ON p.categoria_producto_id = cp.categoria_producto_id
   WHERE p.nombre LIKE '%" . strip_tags($key) . "%'
   GROUP BY p.nombre
   ORDER BY p.nombre DESC LIMIT 0,5";
$result = $mysqli->query($query);
$result = $mysqli->query($query);

if ($result->num_rows>0) {
    while ($row = $result->fetch_assoc()) { 
		$categoria = $row['categoria'];
		
		if($categoria == "Servicio"){
			$producto = $row['producto'];
		}else{
			$producto = $row['producto'].' '.$row['concentracion'].' '.$row['medida'].'';
		}
		
        $html .= '<div><a style="text-decoration:none;" class="suggest-element" data="'.utf8_encode($producto).'" id="'.$row['productos_id'].'">'.utf8_encode($producto).'</a></div>';
    }
}
echo $html;
?>