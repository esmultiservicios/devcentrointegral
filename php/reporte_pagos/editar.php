<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$pagos_id = $_POST['pagos_id'];

//OBTENEMOS LOS VALORES DEL REGISTRO
$valores = "SELECT p.fecha AS 'fecha', CONCAT(pa.nombre, ' ', pa.apellido) AS 'paciente', f.number AS 'numero', sf.prefijo AS 'prefijo', sf.relleno AS 'relleno', p.tipo_pago AS 'tipo_pago', p.efectivo AS 'efectivo', p.tarjeta AS 'tarjeta', p.importe AS 'importe'
	FROM pagos AS p
	INNER JOIN facturas AS f
	ON p.facturas_id = f.facturas_id
	INNER JOIN pacientes AS pa
	ON f.pacientes_id = pa.pacientes_id
	INNER JOIN secuencia_facturacion AS sf
	ON f.secuencia_facturacion_id = sf.secuencia_facturacion_id
    WHERE p.pagos_id = '$pagos_id'";
$result = $mysqli->query($valores);	  

$valores2 = $result->fetch_assoc();
$numero = $valores2['prefijo'].''.rellenarDigitos($valores2['numero'], $valores2['relleno']);

$datos = array(
				0 => $valores2['fecha'], 
				1 => $valores2['paciente'], 
 				2 => $numero,
				3 => $valores2['tipo_pago'],
				4 => $valores2['efectivo'],
				5 => $valores2['tarjeta'],	
				6 => $valores2['importe'],												
				);
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>