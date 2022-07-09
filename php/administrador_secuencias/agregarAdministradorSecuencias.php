<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$empresa = $_POST['empresa'];
$entidad = $_POST['entidad'];
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

//VERIFICAMOS SI EXSTE LA SECUENCIA YA EXISTE PARA LA ENTIDAD A GUARDAR
$query = "SELECT secuencias_id
	FROM secuencias
	WHERE entidad = '$entidad' AND estado = 1";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows==0){
	$secuencias_id  = correlativo('secuencias_id', 'secuencias');
	$insert = "INSERT INTO secuencias 
		VALUES('$secuencias_id','$empresa','$entidad','$prefijo','$sufijo','$relleno','$incremento','$siguiente','$estado','$comentario','$fecha_registro')";
	$query = $mysqli->query($insert) or die($mysqli->error);
	
    if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formularioAdministradorSecuencias",
			5 => "Registro",
			6 => "AdministradorSecuencias",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modalAdministradorSecuencias", //Modals Para Cierre Automatico
		);
		
		/*********************************************************************************************************************************************************************/
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado un nuevo administrador de secuencias: $secuencias_id en la tabla $entidad";
		$modulo = "Administrador de Secuencias";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$secuencias_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
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