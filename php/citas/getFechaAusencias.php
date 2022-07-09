<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$fecha = date('Y-m-d',strtotime($_POST['fecha']));
$colaborador_id = $_POST['colaborador_id'];

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT fecha_ausencia 
   FROM ausencia_medicos 
   WHERE fecha_ausencia = '$fecha' AND colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta);

if($result->num_rows>0){
	echo 1; //El medico no se presentará en esta fecha
}else{
	echo 2; //El médico si se presentara
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>
