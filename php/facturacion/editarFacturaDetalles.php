<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 
 
$facturas_id = $_POST['facturas_id'];

//CONSULTAR DETALLES DE LA RECETA
$query = "SELECT fd.facturas_detalle_id AS 'facturas_detalle_id', fd.facturas_detalle_id AS 'facturas_detalle_id', fd.productos_id AS 'productos_id', p.nombre AS 'producto', fd.cantidad AS 'cantidad', fd.precio AS 'precio', fd.descuento AS 'descuento', fd.isv_valor AS 'isv_valor', p.isv AS 'producto_isv'
	FROM facturas_detalle AS fd
	INNER JOIN facturas As f
	ON fd.facturas_id = f.facturas_id
	INNER JOIN productos AS p
	ON fd.productos_id = p.productos_id
WHERE fd.facturas_id = '$facturas_id'";
$result_factura = $mysqli->query($query);

$arreglo = array();

while( $row = $result_factura->fetch_assoc()){
  $arreglo[] = $row;  
}	

echo json_encode($arreglo);
?>