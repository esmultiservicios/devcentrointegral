<?php	
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 
	
$servidor = $_POST['server'];
$correo = $_POST['correo'];
$contraseña = $_POST['password'];
$puerto = $_POST['port'];
$SMTPSecure = $_POST['smtpSecure'];
$CharSet = "UTF-8";

echo testingMail($servidor, $correo, $contraseña, $puerto, $SMTPSecure, $CharSet);
