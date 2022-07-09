<?php
session_start(); 
include "../funtions.php";

header('Content-Type: text/html; charset=utf-8');
	
//CONEXION A DB
$mysqli = connect_mysqli();

$contraseña_anterior = $_POST['contranaterior'];
$id = $_POST['id'];

$consultar = "SELECT * 
   FROM users 
   WHERE colaborador_id = '$id' AND password = MD5('$contraseña_anterior')";
$result = $mysqli->query($consultar);

if($result->num_rows==0){
	echo 0;
}else
	echo 1;
?>