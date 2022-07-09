<?php
include('../funtions.php');

session_start(); 	
//CONEXION A DB
$mysqli = connect_mysqli();

//CONSULTAR USUARIOS
$query = "SELECT COUNT(productos_id) AS 'total' 
     FROM productos 
     WHERE estado = 1";
$result = $mysqli->query($query);	 

$consulta2=$result->fetch_assoc();

$total = $consulta2['total'];  

echo number_format($total);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>