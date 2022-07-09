<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$proceso = $_POST['pro'];
$id = $_POST['id-registro'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$empresa = $_POST['empresa'];
$puesto = $_POST['puesto'];
$identidad = $_POST['identidad'];
$estatus = $_POST['estatus'];
$nombres = cleanStringStrtolower($nombre);
$apellidos = cleanStringStrtolower($apellido);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];
	
$update = "UPDATE colaboradores 
   SET nombre = '$nombres', apellido = '$apellidos', empresa_id = '$empresa', puesto_id = '$puesto', identidad = '$identidad', estatus = '$estatus'
   WHERE colaborador_id = '$id'";
   		
//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
$historial_numero = historial();
$estado_historial = "Actualizar";
$observacion_historial = "Se ha actualizado la información para el colaborador: $nombre $apellido con identidad n° $identidad";
$modulo = "Colaboradores";
$insert = "INSERT INTO historial 
   VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";
$mysqli->query($insert);   
/********************************************/
				
$query = $mysqli->query($update);

if($query){
	$datos = array(
		0 => "Modificado", 
		1 => "Registro Modificado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "formulario_colaboradores",
		5 => "Registro",
		6 => "Colaboradores",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "registrar_colaboradores", //Modals Para Cierre Automatico
	);
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

echo json_encode($datos);
$mysqli->close();//CERRAR CONEXIÓN
?>