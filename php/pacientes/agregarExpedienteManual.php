<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id'];
$expediente = cleanStringStrtolower($_POST['expediente_usuario_manual']);
$identidad = cleanStringStrtolower($_POST['identidad_ususario_manual']);
$usuario = $_SESSION['colaborador_id'];
$estado = 1; //1. Activo 2. Inactivo
$fecha_registro = date("Y-m-d H:i:s");

//DATOS ANTERIORES
$expediente_anterior = $_POST['expediente_manual'];
$identidad_anterior = $_POST['identidad_manual'];

$fecha_registro = $_POST['fecha_re_manual'];
$fecha_edicion = date('Y-m-d'); 

//CONSULTAR DATOS DEL USUARIO
$consulta = "SELECT * 
    FROM pacientes 
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta);

if($result->num_rows>0){
	$consulta_expediente1 = $result->fetch_assoc();
	
	$expediente_sistema = $consulta_expediente1['expediente'];
	$nombre = $consulta_expediente1['nombre'];
	$apellido = $consulta_expediente1['apellido'];
	$sexo = $consulta_expediente1['genero'];
	$telefono1 = $consulta_expediente1['telefono1'];
	$telefono2 = $consulta_expediente1['telefono2'];
	$fecha_nacimiento = $consulta_expediente1['fecha_nacimiento'];
	$correo = $consulta_expediente1['email'];
	$fecha = $consulta_expediente1['fecha'];
	$departamento_id = $consulta_expediente1['departamento_id'];
	$municipio_id = $consulta_expediente1['municipio_id'];
	$localidad = $consulta_expediente1['localidad'];
	$religion_id = $consulta_expediente1['religion_id'];
	$profesion_id = $consulta_expediente1['profesion_id'];
	$identidad_sistema = $consulta_expediente1['identidad'];
	$observacion = "Expediente ha sido cambiado correctamente";

	$pacientes_id_historial = correlativo('historial_id', 'historial_pacientes');
	$insert = "INSERT INTO historial_pacientes VALUES ('$pacientes_id_historial','$pacientes_id','$expediente','$identidad','$nombre','$apellido','$sexo','$telefono1','$telefono2','$fecha_nacimiento','$correo','$fecha','$departamento_id','$municipio_id','$localidad','$religion_id','$profesion_id','$usuario','$estado','$observacion','$fecha_registro')";	
	
	$mysqli->query($insert);
	//HISTORIAL DE PACIENTES	

	if($expediente != "" && $identidad == ""){
		$update = "UPDATE pacientes SET expediente = '$expediente' 
			 WHERE pacientes_id = '$pacientes_id'";
		$query = $mysqli->query($update);	
	}else if($identidad != "" && $expediente == ""){
		$update = "UPDATE pacientes SET identidad = '$identidad' 
			 WHERE pacientes_id = '$pacientes_id'";
		$query = $mysqli->query($update);
	}else if($expediente != "" && $identidad != ""){
		$update = "UPDATE pacientes SET identidad = '$identidad', expediente = '$expediente' 
			WHERE pacientes_id = '$pacientes_id'";
		$query = $mysqli->query($update);
	}else{
		echo 4;//ERROR EN LOS DATOS
	}	
	
	if($query){
		echo 1;//REGISTRO ALMACENADO CORRECTAMENTE
	}else{
		echo 2;//ERRROR AL ALMACENAR EL REGISTRO
	}
}else{
	echo 3; //REGISTRO NO EXISTE
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>