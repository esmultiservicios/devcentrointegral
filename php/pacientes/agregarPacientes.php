<?php
session_start();
include '../funtions.php';

// CONEXION A DB
$mysqli = connect_mysqli();

$expediente = 0;
$nombre = cleanStringStrtolower($_POST['name']);
$apellido = cleanStringStrtolower($_POST['lastname']);
$sexo = $_POST['sexo'];
$telefono1 = $_POST['telefono1'];
$telefono2 = $_POST['telefono2'];
$fecha_nacimiento = $_POST['fecha_nac'];
$correo = strtolower(cleanString($_POST['correo']));
$fecha = date('Y-m-d');
$pais_id = isset($_POST['pais_id']) && $_POST['pais_id'] !== '' ? $_POST['pais_id'] : 0;
$departamento_id = isset($_POST['departamento_id']) && $_POST['departamento_id'] !== '' ? $_POST['departamento_id'] : 0;
$municipio_id = isset($_POST['municipio_id']) && $_POST['municipio_id'] !== '' ? $_POST['municipio_id'] : 0;
$responsable_id = isset($_POST['responsable_id']) && $_POST['responsable_id'] !== '' ? $_POST['responsable_id'] : 0;
$responsable = $_POST['responsable'];
$localidad = cleanStringStrtolower($_POST['direccion']);
$religion_id = 0;
$profesion_id = 0;
$identidad = $_POST['identidad'];
$usuario = $_SESSION['colaborador_id'];
$estado = 1;  // 1. Activo 2. Inactivo
$fecha_registro = date('Y-m-d H:i:s');

// CONSULTAR IDENTIDAD DEL USUARIO
if ($identidad == 0) {
	$flag_identidad = true;
	while ($flag_identidad) {
		$d = rand(1, 99999999);
		$query_identidadRand = "SELECT pacientes_id 
\t       FROM pacientes 
	\t   WHERE identidad = '$d'";
		$result_identidad = $mysqli->query($query_identidadRand);
		if ($result_identidad->num_rows == 0) {
			$identidad = $d;
			$flag_identidad = false;
		} else {
			$flag_identidad = true;
		}
	}
}

// EVALUAR SI EXISTE EL PACIENTE
$select = "SELECT pacientes_id
	FROM pacientes
	WHERE identidad = '$identidad' AND nombre = '$nombre' AND apellido = '$apellido' AND telefono1 = '$telefono1'";
$result = $mysqli->query($select) or die($mysqli->error);

if ($result->num_rows == 0) {
	$pacientes_id = correlativo('pacientes_id ', 'pacientes');
	$insert = "INSERT INTO pacientes 
	(`pacientes_id`, `expediente`, `identidad`, `nombre`, `apellido`, `genero`, `telefono1`, `telefono2`, `fecha_nacimiento`, `email`, `fecha`, `pais_id`, `departamento_id`, `municipio_id`, `localidad`, `religion_id`, `profesion_id`, `responsable`, `responsable_id`, `usuario`, `estado`, `fecha_registro`) 
	VALUES 
	(
		'{$pacientes_id}', 
		'{$expediente}', 
		'{$identidad}', 
		'{$nombre}', 
		'{$apellido}', 
		'{$sexo}', 
		'{$telefono1}', 
		'{$telefono2}', 
		'{$fecha_nacimiento}', 
		'{$correo}', 
		'{$fecha}', 
		'{$pais_id}', 		
		'{$departamento_id}', 
		'{$municipio_id}', 
		'{$localidad}', 
		'{$religion_id}', 
		'{$profesion_id}', 
		'{$responsable}', 
		'{$responsable_id}', 
		'{$usuario}', 
		'{$estado}', 
		'{$fecha_registro}'
	)";

	$query = $mysqli->query($insert);
	if ($query) {
		$datos = array(
			0 => 'Almacenado',
			1 => 'Registro Almacenado Correctamente',
			2 => 'success',
			3 => 'btn-primary',
			4 => 'formulario_pacientes',
			5 => 'Registro',
			6 => 'formPacientes',
			7 => 'modal_pacientes',
		);
	} else {
		$datos = array(
			0 => 'Error',
			1 => 'No se puedo almacenar este registro, los datos son incorrectos por favor corregir',
			2 => 'error',
			3 => 'btn-danger',
			4 => '',
			5 => '',
		);
	}
} else {
	$datos = array(
		0 => 'Error',
		1 => 'Lo sentimos este registro ya existe no se puede almacenar',
		2 => 'error',
		3 => 'btn-danger',
		4 => '',
		5 => '',
	);
}

echo json_encode($datos);
