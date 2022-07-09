<?php
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id']; 

//CONSULTAR NOMBRE
$consulta_nombre = "SELECT CONCAT(c.nombre, ' ', c.apellido) AS 'nombre' 
     FROM users AS u
	 INNER JOIN colaboradores AS c
	 ON u.colaborador_id = c.colaborador_id
	 WHERE u.id = '$id'";
$result = $mysqli->query($consulta_nombre);	 
$consulta_nombre1 = $result->fetch_assoc();
$nombre = $consulta_nombre1['nombre'];

echo $nombre;
?>