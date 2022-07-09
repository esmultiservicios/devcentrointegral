<?php
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$ubicacion = cleanStringStrtolower($_POST['ubicacion']);
if(isset($_POST['empresa'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['empresa'] == ""){
		$empresa = 0;
	}else{
		$empresa = $_POST['empresa'];
	}
}else{
	$empresa = 0;
}

$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

//VERIFICAMOS SI EXSTE LA UBICACIÓN
$query = "SELECT ubicacion_id
	FROM ubicacion
	WHERE nombre = '$ubicacion'";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows==0){
	$ubicacion_id  = correlativo('ubicacion_id', 'ubicacion');
	$insert = "INSERT INTO ubicacion VALUES('$ubicacion_id','$empresa','$ubicacion')";	
	$query = $mysqli->query($insert) or die($mysqli->error);
	
    if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_ubicacion",
			5 => "Registro",
			6 => "Ubicacion",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_ubicacion", //Modals Para Cierre Automatico
		);
		
		/*********************************************************************************************************************************************************************/
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado un nueva ubicacion: $ubicacion";
		$modulo = "Ubicacion";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$ubicacion_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
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