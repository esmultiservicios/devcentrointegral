<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 
 
$pacientes_id = $_POST['pacientes_id'];

//CONSULTAR DATOS DE LA ATENCION PENDIENTE
$query = "SELECT am.atencion_id  AS 'atencion_id', CONCAT(c.nombre, ' ', c.apellido) AS 'profesional', s.nombre AS 'servicio', t.monto As 'tarifa', am.fecha AS 'fecha', am.colaborador_id AS 'colaborador_id', am.servicio_id AS 'servicio_id', mp.tarifa_id AS 'tarifa_id', mp.descuento_id AS 'descuento_id'
	FROM atenciones_medicas AS am
	INNER JOIN servicios AS s
	ON am.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON am.colaborador_id = c.colaborador_id
	INNER JOIN tarifas AS t
	ON am.colaborador_id = t.colaborador_id
	INNER JOIN metodo_pago AS mp
	ON am.atencion_id = mp.atencion_id
	WHERE am.pacientes_id = '$pacientes_id' AND am.estado = 1";  
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();   
     
$atencion_id = "";
$profesional = "";
$servicio = "";
$tarifa = "";
$fecha = "";
$colaborador_id = "";
$servicio_id = "";
$tarifa_id = "";
$descuento_id = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$atencion_id = $consulta_registro['atencion_id'];
	$profesional = $consulta_registro['profesional'];	
	$servicio = $consulta_registro['servicio'];
	$tarifa = $consulta_registro['tarifa'];
	$fecha = $consulta_registro['fecha'];	
	$colaborador_id = $consulta_registro['colaborador_id'];	
	$servicio_id = $consulta_registro['servicio_id'];
	$tarifa_id = $consulta_registro['tarifa_id'];	
	$descuento_id = $consulta_registro['descuento_id'];		
}

//OBTENER LA AGENDA_ID DEL USUARIO
$query_agenda = "SELECT agenda_id
  FROM agenda
  WHERE pacientes_id = '$pacientes_id' AND CAST(fecha_cita AS DATE) = '$fecha' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND status = 1";
$result_agemda = $mysqli->query($query_agenda) or die($mysqli->error);
$consulta_agenda = $result_agemda->fetch_assoc();   
     
$agenda_id = "";

if($result_agemda->num_rows>0){
	$agenda_id = $consulta_agenda['agenda_id'];	
}	

$datos = array(
	 0 => $atencion_id, 
 	 1 => $profesional,	
	 2 => $servicio, 
 	 3 => $tarifa,	
 	 4 => $fecha,	 
 	 5 => $colaborador_id,	
 	 6 => $agenda_id,
 	 7 => $tarifa_id,
 	 8 => $descuento_id,	 
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>