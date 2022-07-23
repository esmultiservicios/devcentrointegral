<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$empresa = cleanString($_POST['empresa']);
$rtn_empresa = cleanString($_POST['rtn_empresa']);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

//VERIFICAMOS SI EXSTE EL ALMACEN
$query = "SELECT fact_empresas_id
	FROM fact_empresas
	WHERE nombre = '$empresa'";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows==0){
	$fact_empresas_id  = correlativo('fact_empresas_id', 'fact_empresas');
	$insert = "INSERT INTO fact_empresas VALUES('$fact_empresas_id','$empresa','$rtn_empresa','$fecha_registro')";
	$query = $mysqli->query($insert) or die($mysqli->error);
	
    if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formularioEmpresa",
			5 => "Registro",
			6 => "Aseguradora",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modalEmpresas", //Modals Para Cierre Automatico
		);
		
		/*********************************************************************************************************************************************************************/
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado un nueva aseguradora: $empresa";
		$modulo = "Configurar Emrpesas";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$fact_empresas_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
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