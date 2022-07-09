<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 
 
$pacientes_id = $_POST['pacientes_id'];

//CONSULTAR LOS DATOS DEL PACIENTE
$sql = "SELECT identidad AS 'identidad', fecha_nacimiento 'fecha_nacimiento', CONCAT(nombre, ' ', apellido) AS 'paciente', profesion_id AS 'profesion', 
   localidad AS 'localidad', religion_id AS 'religion'
   FROM pacientes
   WHERE pacientes_id = '$pacientes_id'";
   
$result = $mysqli->query($sql) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();   
     
$identidad = "";
$nombre = "";
$fecha_nacimiento = "";
$edad = "";
$profesion = "";
$religion = "";
$servicio_id = "";
$fecha_cita = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$identidad = $consulta_registro['identidad'];
	$fecha_nacimiento = $consulta_registro['fecha_nacimiento'];	
	$paciente = $consulta_registro['paciente'];
	$localidad = $consulta_registro['localidad'];	
	$religion = $consulta_registro['religion'];
	$profesion = $consulta_registro['profesion'];	
	
	//CONSULTA AÑO, MES y DIA DEL PACIENTE
	$valores_array = getEdad($fecha_nacimiento);
	$anos = $valores_array['anos'];
	$meses = $valores_array['meses'];	  
	$dias = $valores_array['dias'];	
	/*********************************************************************************/
}
	
//OBTENER HISTORIA CLINICA
$query_historia = "SELECT pacientes_id, antecedentes, historia_clinica, examen_fisico, diagnostico
	FROM atenciones_medicas
	WHERE pacientes_id = '$pacientes_id'
	ORDER BY atencion_id DESC limit 1";
$result_historia = $mysqli->query($query_historia) or die($mysqli->error);
	
$antecedentes = "";
$historia_clinica = "";
$examen_fisico = "";
$diagnostico = "";

if($result_historia->num_rows>0){
	$consulta_historia = $result_historia->fetch_assoc();
	
	$antecedentes = $consulta_historia['antecedentes'];
	$historia_clinica = $consulta_historia['historia_clinica'];
	$examen_fisico = $consulta_historia['examen_fisico'];
	$diagnostico = $consulta_historia['diagnostico'];	
}
	
//OBTENER SEGUIMIENTO
$query_seguimiento = "SELECT fecha, seguimiento
	FROM atenciones_medicas
	WHERE pacientes_id = '$pacientes_id'";
$result_seguimiento = $mysqli->query($query_seguimiento) or die($mysqli->error);
	
$seguimiento_consulta = "";
	
while($registro_seguimiento = $result_seguimiento->fetch_assoc()){
	$fecha = $registro_seguimiento['fecha'];
	$seguimiento = $registro_seguimiento['seguimiento'];
	
	$seguimiento_consulta.= "Fecha: ".$fecha."\n".$seguimiento."\n\n";
}	
	
$datos = array(
	 0 => $identidad, 
 	 1 => $paciente,	
	 2 => $anos, 	
 	 3 => $localidad,	
	 4 => $religion,
	 5 => $profesion,	 
     6 => $pacientes_id,
	 7 => $antecedentes,
	 8 => $historia_clinica,	 
     9 => $examen_fisico,
     10 => $seguimiento_consulta,
     11 => $diagnostico,	 
     12 => $fecha_nacimiento,	 
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>