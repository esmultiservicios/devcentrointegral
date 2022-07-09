<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$secuencias_id = $_POST['secuencias_id'];
$prefijo = $_POST['prefijo'];
$sufijo = $_POST['sufijo'];
$relleno = $_POST['relleno'];
$incremento = $_POST['incremento'];
$siguiente = $_POST['siguiente'];

if(isset($_POST['estado'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['estado'] == ""){
		$estado = 2;
	}else{
		$estado = $_POST['estado'];
	}
}else{
	$estado = 2;
}

$comentario = cleanStringStrtolower($_POST['comentario']);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

$update = "UPDATE secuencias
	SET
		prefijo = '$prefijo',
		sufijo = '$sufijo',
		relleno = '$relleno',
		siguiente = '$siguiente',
		incremento = '$incremento',		
		estado = '$estado'
	WHERE secuencias_id = '$secuencias_id'";
$query = $mysqli->query($update) or die($mysqli->error);

if($query){
	$datos = array(
		0 => "Editado", 
		1 => "Registro Editado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "formularioAdministradorSecuencias",
		5 => "Editar",
		6 => "AdministradorSecuencias",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "modalAdministradorSecuencias", //Modals Para Cierre Automatico
	);	
	
	/*********************************************************************************************************************************************************************/
	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Agregar";
	$observacion_historial = "Se ha modificado la secuencia con codigo: $secuencias_id";
	$modulo = "Administrador de Secuencias";
	$insert = "INSERT INTO historial 
	   VALUES('$historial_numero','0','0','$modulo','$secuencias_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
	$mysqli->query($insert) or die($mysqli->error);
	/*********************************************************************************************************************************************************************/		
	/*********************************************************************************************************************************************************************/		
}else{
	$datos = array(
		0 => "Error", 
		1 => "No se puedo modificar este registro, los datos son incorrectos por favor corregir", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);	
}

echo json_encode($datos);
?>