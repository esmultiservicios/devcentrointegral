<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$facturas_id = $_POST['facturas_id']; 

//CONSULTAR NOMBRE
$consulta = "SELECT sf.prefijo AS 'prefijo', f.number AS 'numero', sf.relleno AS 'relleno'
	FROM facturas AS f
	INNER JOIN secuencia_facturacion AS sf
	ON f.secuencia_facturacion_id = sf.secuencia_facturacion_id
	WHERE f.facturas_id = '$facturas_id'";
$result = $mysqli->query($consulta) or die($mysqli->error);	 
$consulta_numero = $result->fetch_assoc();

$numero = "";

if($result->num_rows>0){
	$numero = $consulta_numero['prefijo'].''.rellenarDigitos($consulta_numero['numero'],$consulta_numero['relleno']);
}

echo $numero;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>