<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$pacientes_id = $_POST['pacientes_id'];

$consulta = "SELECT CONCAT(nombre,' ', apellido) AS 'paciente'
    FROM pacientes
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta) or die($mysqli->error);
$consulta2 = $result->fetch_assoc();

$paciente = '';

if($result->num_rows>0){
    $paciente = $consulta2['paciente'];
}

echo $paciente;
 
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>