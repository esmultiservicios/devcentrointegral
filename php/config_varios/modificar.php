<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$entidad = $_POST['consulta_registro'];
$nombre = $_POST['nombre_registro'];
$id = $_POST['id_registro'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//ACTUALIZAMOS EL REGISTRO
$update = "UPDATE ".$entidad." SET nombre = '$nombre' 
   WHERE ".$entidad."_id = '$id'";
$query = $mysqli->query($update);

if($query){
   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL 
   $historial_numero = historial();
   $estado_historial = "Actualizar";
   $observacion_historial = "Se ha modificado el registro $nombre en la entidad $entidad con código $id";
   $modulo = cleanStringStrtolower($entidad);
   $insert = "INSERT INTO historial 
      VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
   $mysqli->query($insert);	 
   /********************************************/ 	
	   
	$datos = array(
		0 => "Almacenado", 
		1 => "Registro Almacenado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "formulario_registros",
		5 => "Registro",
		6 => "configuracionVarios",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "registrar", //Modals Para Cierre Automatico
		8 => "", //Modals Para Cierre Automatico		
	);
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

$mysqli->close();//CERRAR CONEXIÓN
?>