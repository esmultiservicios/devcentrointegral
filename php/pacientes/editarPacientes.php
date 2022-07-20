<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id'];
$usuario = $_SESSION['colaborador_id'];
$estado = 1; //1. Activo 2. Inactivo
$fecha_registro = date("Y-m-d H:i:s");

$nombre = $_POST['name'];
$apellido = $_POST['lastname'];
$sexo = $_POST['sexo'];
$telefono1 = $_POST['telefono1'];
$telefono2 = $_POST['telefono2'];
$correo = strtolower(cleanString($_POST['correo']));
$departamento_id = $_POST['departamento_id'];
$municipio_id = $_POST['municipio_id'];
$localidad = cleanStringStrtolower($_POST['direccion']);
$responsable = cleanStringStrtolower($_POST['responsable']);
$fecha_nacimiento = $_POST['fecha_nac'];

if(isset($_POST['responsable_id'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['responsable_id'] == ""){
		$responsable_id = 0;
	}else{
		$responsable_id = $_POST['responsable_id'];
	}
}else{
	$responsable_id = 0;
}

$update = "UPDATE pacientes 
	SET 
		nombre = '$nombre', 
		apellido = '$apellido', 
		genero = '$sexo', 
		telefono1 = '$telefono1',
		telefono2 = '$telefono2',		
		email = '$correo', 
		departamento_id = '$departamento_id',
		municipio_id = '$municipio_id',
		localidad = '$localidad',
		responsable = '$responsable',
		responsable_id = '$responsable_id',
		fecha_nacimiento = '$fecha_nacimiento'		
	WHERE pacientes_id = '$pacientes_id'";
$query = $mysqli->query($update);

if($query){
		$datos = array(
			0 => "Editado", 
			1 => "Registro Editado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "",
			5 => "Editar",
			6 => "formPacientes",
			7 => "modal_pacientes",			
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
?>