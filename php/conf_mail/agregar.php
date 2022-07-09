<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 
	
$tipo = $_POST['conf_tipo'];	
$correo = $_POST['conf_mail'];
$password = encryption($_POST['conf_pass']);
$servidor = $_POST['conf_servidor'];
$smtpSecure = $_POST['conf_smtp_secure'];
$puerto = $_POST['conf_puerto'];
$fecha_registro = date("Y-m-d H:i:s");
$estado = 1;

//OBTENER CORRELATIVO
$correo_id = correlativo("correo_id", "correo");

//CONSULTAMOS SI EXISTE EL CORREO ALMACENADO

//CONSULTAR EXISTENCIA
$consulta = "SELECT correo_id
	FROM correo
	WHERE correo = '$correo' OR correo_tipo_id = '$tipo'";
$result = $mysqli->query($consulta) or die($mysqli->error);	

if($result->num_rows==0){
	//INSERTAMOS LOS DATOS DEL CORREO
	$insert = "INSERT INTO correo 
		VALUES('$correo_id', '$tipo', '$servidor', '$correo', '$password', '$puerto', '$smtpSecure','$estado', '$fecha_registro')";
	$query = $mysqli->query($insert);
	
	if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_registros",
			5 => "Registro",
			6 => "configuracionVariosemails",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "registrar", //Modals Para Cierre Automatico
			8 => "", //Modals Para Cierre Automatico		
		);		
	}else{
		$datos = array(
			0 => "Error", 
			1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);		
	}	
}else{
	$datos = array(
		0 => "Error", 
		1 => "Este Registro ya existe, no se puede almacenar", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);	
}
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>