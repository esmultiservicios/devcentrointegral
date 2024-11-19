<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id'];
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$estado = 1; //1. Activo 2. Inactivo

$numero_expediente = correlativo('expediente ', 'pacientes');

//ACTUALIZAMOS EL EXPEDIENTE DEL USUARIO EN LA ENTIDAD PACIENTES
$update = "UPDATE pacientes 
	SET expediente = '$numero_expediente' 
	WHERE pacientes_id = '$pacientes_id'";
$query_pacientes = $mysqli->query($update);

//ACTUALIZAMOS EL EXPEDIENTE DEL USUARIO EN LA ENTIDAD AGENDA
$update = "UPDATE agenda 
	SET expediente = '$numero_expediente' 
	WHERE pacientes_id = '$pacientes_id'";
$query_pacientes = $mysqli->query($update);

if($query_pacientes){
	//HISTORIAL DE PACIENTES
	//CONSULTAR EXPEDIENTE
	$consulta_expediente = "SELECT * 
		FROM pacientes 
		WHERE pacientes_id = '$pacientes_id'";
	$result = $mysqli->query($consulta_expediente);   	
	
	if($result->num_rows>0){
		$pacientes_id_historial = correlativo('expediente ', 'pacientes');
		$UpdatePacientes = "
		UPDATE pacientes
		SET 
			expediente = $pacientes_id_historial
		WHERE 
			pacientes_id = $pacientes_id
		";

		$mysqli->query($UpdatePacientes);

/* 		$consulta_expediente1 = $result->fetch_assoc();
		$expediente = $consulta_expediente1['expediente'];
		$nombre = $consulta_expediente1['nombre'];
		$apellido = $consulta_expediente1['apellido'];
		$sexo = $consulta_expediente1['genero'];
		$telefono1 = $consulta_expediente1['telefono1'];
		$telefono2 = $consulta_expediente1['telefono2'];
		$fecha_nacimiento = $consulta_expediente1['fecha_nacimiento'];
		$correo = strtolower($consulta_expediente1['email']);
		$fecha = $consulta_expediente1['fecha'];
		$departamento_id = $consulta_expediente1['departamento_id'];
		$municipio_id = $consulta_expediente1['municipio_id'];
		$localidad = $consulta_expediente1['localidad'];
		$religion_id = $consulta_expediente1['localidad'];
		$profesion_id = $consulta_expediente1['localidad'];
		$identidad = $consulta_expediente1['localidad'];
		$observacion = "Expediente ha sido asignado correctamente";

		$pacientes_id_historial = correlativo('pacientes_id ', 'pacientes');
		$insert = "INSERT INTO pacientes VALUES ('$pacientes_id','$expediente','$identidad','$nombre','$apellido','$sexo','$telefono1','$telefono2','$fecha_nacimiento','$correo','$fecha','$departamento_id','$municipio_id','$localidad','$religion_id','$profesion_id','$usuario','$estado','$observacion','$fecha_registro')";	
		$mysqli->query($insert); */
		//HISTORIAL DE PACIENTES		
	}
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>