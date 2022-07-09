<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id'];

//CONSULTAR REGISTROS
$consulta = "SELECT * FROM correo 
    WHERE correo_id = '$id'";
$result = $mysqli->query($consulta) or die($mysqli->error);	

$tipo = "";
$correo = "";
$servidor = "";
$smtpSecure = "";
$puerto = "";

if($result->num_rows>0){
	$consultar = $result->fetch_assoc();
	$tipo = $consultar['correo_tipo_id'];
	$correo = $consultar['correo'];
	$servidor = $consultar['server'];
	$smtpSecure = $consultar['smtp_secure'];
	$puerto = $consultar['port'];
	$password = decryption($consultar['password']);	
}

$datos = array(
	0 => $tipo, 
	1 => $correo, 
	2 => $servidor,	
	3 => $smtpSecure, 
	4 => $puerto,
	5 => $password,				
);

echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>