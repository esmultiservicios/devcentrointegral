<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$categoria = $_POST['categoria'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT p.nombre AS 'producto', p.concentracion AS 'concentracion', me.nombre AS 'medida', m.cantidad_entrada AS 'entrada', m.cantidad_salida AS 'salida', m.saldo AS 'saldo', m.fecha_registro AS 'fecha_registro'
	FROM movimientos AS m
	INNER JOIN productos AS p
	ON m.productos_id = p.productos_id
	INNER JOIN medida AS me
	ON p.medida_id = me.medida_id
	WHERE p.categoria_producto_id = '$categoria' AND CAST(m.fecha_registro AS DATE) BETWEEN '$fechai' AND '$fechaf'
	ORDER BY m.fecha_registro ASC";
$result = $mysqli->query($consulta);	

$arreglo = array();

while($data = $result->fetch_assoc()){				
	$arreglo["data"][] = $data;		
}
 
echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>