<?php	
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$entidad = $_POST['tabla'];
$secuencias_id = $_POST['secuencias_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];
$estado = 2;

//VERIFICAMOS SI NO HAY DATOS DE LA SECUENCIA EN LA ENTIDAD ANTES DE ELIMINALA
$query = "SELECT secuencias_id
	FROM ".$entidad;
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows==0){
	$delete = "DELETE FROM secuencias WHERE secuencias_id = '$secuencias_id'";
	$query = $mysqli->query($delete) or die($mysqli->error);

	if($query){
		$datos = array(
			0 => "Desactivado", 
			1 => "Registro Desactivado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formularioAdministradorSecuencias",
			5 => "Desactivar",
			6 => "AdministradorSecuencias",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modalAdministradorSecuencias", //Modals Para Cierre Automatico
			8 => "",
			9 => "Eliminar", //PERMITE CERRAR EL MODAL SEGUN EL INDICADOR este indicador esta en main.js			
		);	

		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha desactivado la secuencia con código $secuencias_id";
		$modulo = "Administrador de Secuencias";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$secuencias_id','$usuario','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
		$mysqli->query($insert) or die($mysqli->error);
		/*********************************************************************************************************************************************************************/		
	/*********************************************************************************************************************************************************************/		
	}else{
		$datos = array(
			0 => "Error", 
			1 => "No se puedo eliminar este registro, los datos son incorrectos por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);
	}	
}else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos no podemos procesar su solicitud, verifique los datos o inténtelo de nuevo más tarde", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);	
}

echo json_encode($datos);
?>