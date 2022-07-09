<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$expediente = $_POST['expediente'];

$consulta = "SELECT pacientes_id 
    FROM pacientes 
	WHERE expediente = '$expediente' OR identidad = '$expediente' AND tipo = 1";
$result = $mysqli->query($consulta);
$consulta2 = $result->fetch_assoc();


if($consulta2['pacientes_id'] == ""){
	 $pacientes_id = 0;
}else{
    $pacientes_id = $consulta2['pacientes_id'];	
}
echo $pacientes_id;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>