<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$agenda_id = $_POST['agenda_id']; 

//CONSULTAR PACIENTE ID
$consulta_paciente = "SELECT pacientes_id 
    FROM agenda 
	WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($consulta_paciente);
$consulta_paciente1 = $result->fetch_assoc();

$pacientes_id = "";

if($result->num_rows>0){
	$pacientes_id = $consulta_paciente1['pacientes_id'];
}
//CONSULTAR NOMBRE USUARIO
$consulta_nombre = "SELECT CONCAT(nombre,' ',apellido) AS 'nombre' 
    FROM pacientes 
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta_nombre);
$consulta_nombre1 = $result->fetch_assoc();

$nombre = "";

if($result->num_rows>0){
    $nombre = $consulta_nombre1['nombre'];	
}

echo $nombre;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>