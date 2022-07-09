<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$empresa = cleanString($_POST['empresa']);
$rtn = $_POST['rtn'];
$telefono = $_POST['telefono'];
$celular = $_POST['celular'];
$correo = $_POST['correo'];
$direccion = cleanString($_POST['direccion']);
$horario_atencion = cleanString($_POST['horario_atencion']);
$otra_info = cleanString($_POST['otra_info']);
$eslogan = cleanString($_POST['eslogan']);
$facebook = cleanString($_POST['facebook_empresa']);
$sitio_web = cleanString($_POST['sitioweb_empresa']);
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

//VERIFICAMOS SI EXISTE LA EMPRESA
$query = "SELECT empresa_id FROM empresa WHERE nombre = '$empresa' AND rtn = '$rtn'";
$result_empresa = $mysqli->query($query) or die($mysqli->error);  

if($result_empresa->num_rows==0){
	//ALMACENAMOS EL REGISTRO DE LA EMPRESA
	$correlativo = correlativo('empresa_id', 'empresa');
	$insert = "INSERT INTO empresa
		VALUES('$correlativo','$empresa','$otra_info','$eslogan','$telefono','$celular','$correo','$rtn','$direccion','$facebook','$sitio_web','$horario_atencion','$usuario','$fecha_registro')";
	$query = $mysqli->query($insert) or die($mysqli->error);
	
	if($query){
		echo 1;//REGISTRO ALMACENADO CORRECTAMENTE
		
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado una nueva empresa con los los datos: empresa: $empresa y RTN:  $rtn";
		$modulo = "Empresa";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$correlativo','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
		$mysqli->query($insert) or die($mysqli->error);
		/********************************************/	 		
	}else{
		echo 2;//ERROR AL ALMACENAR ESTE REGISTRO
	}	
}else{
	echo 3;//ESTE REGISTRO YA EXISTE
}

$mysqli->close();//CERRAR CONEXIÓN
?>