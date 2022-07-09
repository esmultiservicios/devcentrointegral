<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$facturas_id = $_POST['facturas_id'];

//CONSULTAR DATOS DE FACTURA
$query = "SELECT CONCAT(p.nombre,' ',p.apellido) AS 'paciente'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	WHERE f.facturas_id  = '$facturas_id'";
$result = $mysqli->query($query);
$paciente_nombre = "";

if($result->num_rows>=0){
	 $factura = $result->fetch_assoc();
	 $paciente_nombre = $factura['paciente'];
}

$datos = array(
	0 => $paciente_nombre,
);
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>              