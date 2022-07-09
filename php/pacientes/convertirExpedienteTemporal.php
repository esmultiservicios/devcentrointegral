<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id'];
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$expediente = 0;

//CONSULTAR ID
$consulta_identidad = "SELECT identidad, CONCAT(apellido,' ',nombre) AS 'paciente'
     FROM pacientes 
     WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta_identidad);
$consulta_identidad1 = $result->fetch_assoc();
$identidad = $consulta_identidad1['identidad'];
$paciente = $consulta_identidad1['paciente'];

//CONSULTA EXPEDIENTE DE USUAIRO
$update = "UPDATE pacientes 
	SET expediente = '$expediente'
    WHERE pacientes_id = '$pacientes_id'";
$query = $mysqli->query($update);

if($query){
    //ACTUALIZAMOS EL EXPEDIENTE DEL USUARIO EN LA ENTIDAD AGENDA		
	$update = "UPDATE agenda 
       SET expediente = '$expediente' 
		WHERE pacientes_id = '$pacientes_id'";	
	$query_agenda = $mysqli->query($update);
		
	if($query_agenda){
	   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
       $historial_numero = historial();
       $estado_historial = "Actualizar";
       $observacion_historial = "Se ha actualizado el numero de expediente en la entidad Agenda, para el usuario: $paciente con identidad n° $identidad";
       $modulo = "Agenda";
       $insert = "INSERT INTO historial 
	   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$pacientes_id','0','0','$fecha_registro','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";
       $mysqli->query($insert);   
       /*****************************************************/   			
	}		
			
    //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
    $historial_numero = historial();
    $estado = "Actualizar";
    $observacion = "Se convirtio el registro a temporal";
    $modulo = "Pacientes";
    $insert = "INSERT INTO historial 
			   VALUES('$historial_numero','$pacientes_id','0','$modulo','0','0','0','$fecha_registro','$estado','$observacion','$usuario','$fecha_registro')";
    $mysqli->query($insert);
    /*****************************************************/   
 
	echo 1;
}else{
	echo 2;
}

$mysqli->close();//CERRAR CONEXIÓN
?>