<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$monto = $_POST['monto'];
$porcentaje = $_POST['porcentaje'];

$descuento = $monto * ($porcentaje / 100);
$neto =  $monto - $descuento;

echo $neto;

$mysqli->close();//CERRAR CONEXIÓN
?>