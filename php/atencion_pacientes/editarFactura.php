<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 
 
$atencion_id = $_POST['atencion_id'];

//CONSULTAR DATOS DEL METODO DE PAGO
$query = "SELECT p.pacientes_id AS pacientes_id, CONCAT(p.nombre, ' ', p.apellido) AS 'paciente', at.servicio_id AS 'servicio_id', at.colaborador_id AS 'colaborador_id', CONCAT(c.nombre, ' ', c.apellido) AS 'profesional', at.fecha AS 'fecha'
	FROM atenciones_medicas AS at
	INNER JOIN pacientes AS p
	ON at.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
	ON at.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON at.servicio_id = s.servicio_id
	WHERE at.atencion_id = '$atencion_id'";	
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();   
     
$pacientes_id = "";
$paciente = "";
$fecha = "";
$servicio_id = "";
$profesional = "";
$colaborador_id = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$pacientes_id = $consulta_registro['pacientes_id'];
	$paciente = $consulta_registro['paciente'];
	$fecha = $consulta_registro['fecha'];	
	$profesional = $consulta_registro['profesional'];
	$colaborador_id = $consulta_registro['colaborador_id'];	
	$servicio_id = $consulta_registro['servicio_id'];		
}
	
$datos = array(
	 0 => $pacientes_id, 
	 1 => $paciente, 
	 2 => $fecha,	 
	 3 => $colaborador_id, 	 
	 4 => $profesional, 
	 5 => $servicio_id, 	 
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>