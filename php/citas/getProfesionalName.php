<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$profesional = $_POST['profesional']; 

//OBTENER EXPEDIENTE DE USUARIO
$consulta_datos = "SELECT CONCAT(nombre,' ',apellido) AS 'profesional'
   FROM colaboradores 
   WHERE colaborador_id = '$profesional'";
$result = $mysqli->query($consulta_datos);
$consulta_datos1 = $result->fetch_assoc();
$profesional = $consulta_datos1['profesional'];

echo $profesional;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>