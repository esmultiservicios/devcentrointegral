<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$aseguradora = cleanStringStrtolower($_POST['aseguradora']);
$rtn_aseguradora = cleanStringStrtolower($_POST['rtn_aseguradora']);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

//VERIFICAMOS SI EXSTE EL ALMACEN
$query = "SELECT aseguradora_id
	FROM aseguradora
	WHERE nombre = '$aseguradora'";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows==0){
	$aseguradora_id  = correlativo('aseguradora_id', 'aseguradora');
	$insert = "INSERT INTO aseguradora VALUES('$aseguradora_id','$aseguradora','$rtn_aseguradora','$fecha_registro')";
	$query = $mysqli->query($insert) or die($mysqli->error);
	
    if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formularioAseguradora",
			5 => "Registro",
			6 => "Aseguradora",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modalAseguradora", //Modals Para Cierre Automatico
		);
		
		/*********************************************************************************************************************************************************************/
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado un nueva aseguradora: $aseguradora";
		$modulo = "Productos";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$aseguradora_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
		$mysqli->query($insert) or die($mysqli->error);
		/*********************************************************************************************************************************************************************/		
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
		1 => "Lo sentimos este registro ya existe no se puede almacenar", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",		
	);
}

echo json_encode($datos);
?>