<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$categoria = $_POST['categoria'];

$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

if(isset($_POST['categoria_activo'])){
	if(isset($_POST['categoria_activo'])){
		$categoria_activo = $_POST['categoria_activo'];
	}else{
		$categoria_activo = 2;
	}	
}else{
	$categoria_activo = 2;
}

//VERIFICAMOS SI EXSTE LA UNIDAD DE MEDIDA
$query = "SELECT categoria_id
	FROM categoria
	WHERE nombre = '$categoria'";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows==0){
	$categoria_id  = correlativo('categoria_id', 'categoria');
	$insert = "INSERT INTO categoria VALUES('$categoria_id','$categoria','$categoria_activo','$fecha_registro')";
	$query = $mysqli->query($insert) or die($mysqli->error);
	
    if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formularioCategoria",
			5 => "Registro",
			6 => "Categoria",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_categoria", //Modals Para Cierre Automatico
		);
		
		/*********************************************************************************************************************************************************************/
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado una nueva categoria: $categoria";
		$modulo = "Categoria";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$categoria_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
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