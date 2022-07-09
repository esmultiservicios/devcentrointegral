<?php
session_start();   
include "../funtions.php";

$fecha = $_POST['fecha'];
$fecha_mes = date('Y-m',strtotime($fecha));
$fecha_sistema = date("Y-m-d");
$fecha_sistema_mes = date('Y-m',strtotime($fecha_sistema));

if($fecha_sistema_mes == $fecha_mes){
	echo 1;//ESTA DENTRO DEL MES, SE PUEDEN REALIZAR CAMBIOS.
}else{
	echo 2;//NO ESTA PERMITIDO REALIZAR CAMBIOS FUERA DE ESTE MES.
}
?>