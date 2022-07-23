<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$fact_empresas_id = $_POST['fact_empresas_id'];
$empresa = cleanString($_POST['empresa']);
$rtn_empresa = cleanString($_POST['rtn_empresa']);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

$update = "UPDATE fact_empresas
	SET
		nombre = '$empresa',
		rtn = '$rtn_empresa'
	WHERE fact_empresas_id = '$fact_empresas_id'";
$query = $mysqli->query($update) or die($mysqli->error);

if($query){
	$datos = array(
		0 => "Editado", 
		1 => "Registro Editado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "formularioEmpresa",
		5 => "Editar",
		6 => "Aseguradora",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "modalEmpresas", //Modals Para Cierre Automatico
	);	
	
	/*********************************************************************************************************************************************************************/
	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Agregar";
	$observacion_historial = "Se ha modificado el la aseguradora $empresa con codigo: $fact_empresas_id";
	$modulo = "Configurar Empresas";
	$insert = "INSERT INTO historial 
	   VALUES('$historial_numero','0','0','$modulo','$fact_empresas_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
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