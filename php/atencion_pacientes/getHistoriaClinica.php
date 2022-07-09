<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$pacientes_id = $_POST['pacientes_id'];

//OBTENER HISTORIA CLINICA
$query_historia = "SELECT pacientes_id, antecedentes, historia_clinica, examen_fisico
	FROM atenciones_medicas
	WHERE pacientes_id = '$pacientes_id'
	ORDER BY atencion_id DESC limit 1"
$result_historia = $mysqli->query($query_historia) or die($mysqli->error);
	
$antecedentes = "";
$historia_clinica = "";
$examen_fisico = "";

if($result_historia->num_rows>0){
	$consulta_historia = $result->fetch_assoc();
	
	$antecedentes = $consulta_historia['antecedentes'];
	$historia_clinica = $consulta_historia['historia_clinica'];
	$examen_fisico = $consulta_historia['examen_fisico'];	
}

$datos = array(
     0 => $antecedentes,
     1 => $historia_clinica,
     2 => $examen_fisico,	 
);	
?>