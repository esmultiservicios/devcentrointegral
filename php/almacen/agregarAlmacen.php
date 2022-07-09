<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$almacen = cleanStringStrtolower($_POST['almacen']);
if(isset($_POST['ubicacion'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['ubicacion'] == ""){
		$ubicacion = 0;
	}else{
		$ubicacion = $_POST['ubicacion'];
	}
}else{
	$ubicacion = 0;
}

$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

//VERIFICAMOS SI EXSTE EL ALMACEN
$query = "SELECT almacen_id
	FROM almacen
	WHERE nombre = '$almacen'";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows==0){
	$almacen_id  = correlativo('almacen_id', 'almacen');
	$insert = "INSERT INTO almacen VALUES('$almacen_id','$ubicacion','$almacen')";
	$query = $mysqli->query($insert) or die($mysqli->error);
	
    if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_almacen",
			5 => "Registro",
			6 => "Almacen",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_almacen", //Modals Para Cierre Automatico
		);
		
		/*********************************************************************************************************************************************************************/
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado un nuevo almacen: $almacen";
		$modulo = "Productos";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$almacen_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
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