<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 
 
$pacientes_id = $_POST['pacientes_id'];
$agenda_id = $_POST['agenda_id'];

//CONSULTAR LOS DATOS DEL PACIENTE
$sql = "SELECT p.identidad AS 'identidad', p.fecha_nacimiento 'fecha_nacimiento', CONCAT(p.nombre, ' ', p.apellido) AS 'paciente', p.localidad AS 'localidad', p.religion_id AS 'religion', p.profesion_id AS 'profesion', CAST(a.fecha_cita AS DATE) AS 'fecha', a.servicio_id AS 'servicio_id', a.colaborador_id AS 'colaborador_id'
   FROM agenda AS a
   INNER JOIN pacientes AS p
   ON a.pacientes_id = p.pacientes_id
   WHERE a.agenda_id = '$agenda_id'";
$result = $mysqli->query($sql) or die($mysqli->error);  
     
$identidad = "";
$nombre = "";
$fecha_nacimiento = "";
$edad = "";
$profesion = "";
$religion = "";
$servicio_id = "";
$colaborador_id = "";
$fecha_cita = "";
$palabra_anos = "";
$palabra_mes = "";
$palabra_dia = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$consulta_registro = $result->fetch_assoc();
	
	$identidad = $consulta_registro['identidad'];
	$fecha_nacimiento = $consulta_registro['fecha_nacimiento'];	
	$paciente = $consulta_registro['paciente'];
	$localidad = $consulta_registro['localidad'];	
	$religion = $consulta_registro['religion'];
	$profesion = $consulta_registro['profesion'];
	$fecha_cita = $consulta_registro['fecha'];	
	$servicio_id = $consulta_registro['servicio_id'];
	$colaborador_id = $consulta_registro['colaborador_id'];	
	
	//CONSULTA AÑO, MES y DIA DEL PACIENTE
	$valores_array = getEdad($fecha_nacimiento);
	$anos = $valores_array['anos'];
	$meses = $valores_array['meses'];	  
	$dias = $valores_array['dias'];	
	/*********************************************************************************/
	if ($anos>1 ){
	   $palabra_anos = "Años";
	}else{
	  $palabra_anos = "Año";
	}

	if ($meses>1 ){
	   $palabra_mes = "Meses";
	}else{
	  $palabra_mes = "Mes";
	}

	if($dias>1){
		$palabra_dia = "Días";
	}else{
		$palabra_dia = "Día";
	}	
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

//CONSULTA LOS DATOS EN LA ENTIDAD PRECLINICA
$valores = "SELECT pre.preclinica_id AS 'preclinica', CONCAT(p.nombre,' ',p.apellido) AS 'nombre', p.expediente AS expediente, pre.fecha AS 'fecha', p.identidad AS 'identidad', pre.pa AS 'pa', pre.fr AS 'fr', pre.fc AS 'fc', pre.t AS 'temperatura', pre.peso AS 'peso', pre.talla AS 'talla', pre.observacion AS 'observacion'
      FROM preclinica AS pre
      INNER JOIN pacientes AS p
      ON pre.expediente = p.expediente
      WHERE pre.pacientes_id = '$pacientes_id' AND pre.colaborador_id = '$colaborador_id' AND pre.servicio_id = '$servicio_id' AND pre.fecha = '$fecha_cita'";
$result_preclinica = $mysqli->query($valores);	

if($result_preclinica->num_rows>0){
	$valores2 = $result_preclinica->fetch_assoc();
	$preclincia = "PA: ".$valores2['pa']." FR: ".$valores2['fr']." FC: ".$valores2['fc']." Temperatura: ".$valores2['temperatura']." Peso: ".$valores2['peso']." Talla: ".$valores2['talla'];
}

$datos = array(
	 0 => $identidad, 
 	 1 => $paciente,	
	 2 => $anos, 	
 	 3 => $localidad,	
	 4 => $religion,
	 5 => $profesion,	 
     6 => $pacientes_id,
     7 => $fecha_cita,
     8 => $fecha_nacimiento,
     9 => $antecedentes,
     10 => $historia_clinica,
     11 => $examen_fisico,	 
     12 => $diagnostico,	 	 
	 13 => $seguimiento_consulta,
	 14 => $servicio_id,
	 15 => $anos." ".$palabra_anos.", ".$meses." ".$palabra_mes." y ".$dias." ".$palabra_dia,
	 16 => $preclincia
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>