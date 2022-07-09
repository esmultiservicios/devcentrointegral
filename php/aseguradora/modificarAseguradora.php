<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$aseguradora_id = $_POST['aseguradora_id'];
$aseguradora = cleanStringStrtolower($_POST['aseguradora']);
$rtn_aseguradora = cleanStringStrtolower($_POST['rtn_aseguradora']);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

$update = "UPDATE aseguradora
	SET
		nombre = '$aseguradora',
		rtn = '$rtn_aseguradora'
	WHERE aseguradora_id = '$aseguradora_id'";
$query = $mysqli->query($update) or die($mysqli->error);

if($query){
	$datos = array(
		0 => "Editado", 
		1 => "Registro Editado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "formularioAseguradora",
		5 => "Editar",
		6 => "Aseguradora",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "modalAseguradora", //Modals Para Cierre Automatico
	);	
	
	/*********************************************************************************************************************************************************************/
	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Agregar";
	$observacion_historial = "Se ha modificado el la aseguradora $aseguradora con codigo: $aseguradora_id";
	$modulo = "Productos";
	$insert = "INSERT INTO historial 
	   VALUES('$historial_numero','0','0','$modulo','$aseguradora_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
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