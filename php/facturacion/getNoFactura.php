<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$facturas_id = $_POST['facturas_id'];

//CONSULTAR DATOS DE FACTURA
$query = "SELECT f.number AS 'numero', sf.prefijo AS 'prefijo', sf.relleno AS 'relleno'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sf
	ON f.secuencia_facturacion_id = sf.secuencia_facturacion_id
	WHERE f.facturas_id  = '$facturas_id'";
$result = $mysqli->query($query);
$no_factura = "";

if($result->num_rows>=0){
	 $factura = $result->fetch_assoc();
	 $no_factura = $factura['prefijo'].''.rellenarDigitos($factura['numero'],$factura['relleno']);
}

$datos = array(
	0 => $no_factura,
);
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>              