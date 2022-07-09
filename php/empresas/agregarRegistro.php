<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$empresa_id = $_POST['empresa_id'];
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


//ACTUALIZAMOS LOS REGISTROS DE LA EMPRESA
$update = "UPDATE empresa 
	SET 
		nombre = '$empresa', 
		rtn = '$rtn', 
		telefono = '$telefono', 
		correo = '$correo', 
		ubicacion = '$direccion',
		otra_informacion = '$otra_info',
		eslogan = '$eslogan',
		celular = '$celular',
		horario = '$horario_atencion',
		facebook = '$facebook',
		sitioweb = '$sitio_web'		
	WHERE empresa_id = '$empresa_id'";
$query = $mysqli->query($update) or die($mysqli->error);

if($query){
	echo 1;//REGISTRO MODIFICADO CORRECTAMENTE
	
	/*********************************************************************************************************************************************************************/
	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Agregar";
	$observacion_historial = "Se ha agregado una nueva empresa con los los datos: empresa: $empresa y RTN:  $rtn";
	$modulo = "Empresa";
	$insert = "INSERT INTO historial 
	   VALUES('$historial_numero','0','0','$modulo','$empresa_id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
	$mysqli->query($insert) or die($mysqli->error);
	/********************************************/	 		
}else{
	echo 2;//ERROR AL MODIFICAR ESTE REGISTRO
}	

$mysqli->close();//CERRAR CONEXIÓN
?>