<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();
 
$pacientes_id = $_POST['pacientes_id'];

$query = "SELECT CONCAT(nombre,' ',apellido) AS 'nombre' 
    FROM pacientes 
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($query);   
$consulta2 = $result->fetch_assoc(); 

$nombre = $consulta2['nombre'];

echo $nombre;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>