<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

header("Content-Type: text/html;charset=utf-8");
$proceso = $_POST['pro'];
$id = $_POST['id-registro'];
$expediente_valor = $_POST['expediente'];
$fecha = $_POST['fecha'];
$pa = $_POST['pa'];
$fr = $_POST['fr'];
$fc = $_POST['fc'];
$temperatura = $_POST['temperatura'];
$peso = $_POST['peso'];
$talla = $_POST['talla'];

if(isset($_POST['servicio'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['servicio'] == ""){
		$servicio = 0;
	}else{
		$servicio = $_POST['servicio'];
	}
}else{
	$servicio = 0;
}

if(isset($_POST['medico'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['medico'] == ""){
		$medico = 0;
	}else{
		$medico = $_POST['medico'];
	}
}else{
	$medico = 0;
}

$fecha_registro = date("Y-m-d H:i:s");
$observaciones = cleanStringStrtolower($_POST['observaciones']);
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");

$consultar_expediente = "SELECT expediente, pacientes_id
     FROM pacientes 
	 WHERE expediente = '$expediente_valor' OR identidad = '$expediente_valor' AND estado = 1";
$result = $mysqli->query($consultar_expediente);	 
$consultar_expediente2 = $result->fetch_assoc();
$expediente = $consultar_expediente2['expediente'];
$pacientes_id = $consultar_expediente2['pacientes_id'];

//OBTENER CORRELATIVO
$numero = correlativo("preclinica_id", "preclinica");
	
//CONSULTAR FECHA DE NACIMIENTO
$consulta_nacimiento = "SELECT fecha_nacimiento 
   FROM pacientes 
   WHERE expediente = '$expediente'";
$result = $mysqli->query($consulta_nacimiento);
$consulta_nacimiento2 = $result->fetch_assoc();
$fecha_de_nacimiento = $consulta_nacimiento2['fecha_nacimiento'];

/*********************************************************************************/
//CONSULTA AÑO, MES y DIA DEL PACIENTE
$nacimiento = "SELECT fecha_nacimiento AS fecha 
	FROM pacientes 
	WHERE expediente = '$expediente'";
$result = $mysqli->query($nacimiento);
$nacimiento2 = $result->fetch_assoc();
$fecha_nacimiento = $nacimiento2['fecha'];

$valores_array = getEdad($fecha_nacimiento);
$anos = $valores_array['anos'];
$meses = $valores_array['meses'];	  
$dias = $valores_array['dias'];	
/*********************************************************************************/
$consultar_paciente = "SELECT agenda_id
	 FROM agenda
	 WHERE pacientes_id = '$pacientes_id' AND servicio_id = '$servicio'";
$result = $mysqli->query($consultar_paciente);
if($result->num_rows>0){
   $paciente = 'S';
}else{
	$paciente = 'N';
}
//CONSULTAR AGENDA SI HAY VALORES
$consultar_agenda = "SELECT a.agenda_id 
FROM agenda AS a
INNER JOIN colaboradores AS c
ON a.colaborador_id = c.colaborador_id
WHERE a.pacientes_id = '$pacientes_id' AND cast(a.fecha_cita AS DATE) = '$fecha' AND c.colaborador_id = '$medico' AND a.servicio_id = '$servicio'";
$result_agenda = $mysqli->query($consultar_agenda);

//CONSULTAR Registro
$consultar_preclinica = "SELECT p.preclinica_id 
   FROM preclinica AS p
   WHERE p.expediente = '$expediente' AND p.fecha = '$fecha' AND p.servicio_id = '$servicio' AND p.colaborador_id = '$medico'"; 
$result_preclinica = $mysqli->query($consultar_preclinica);

if($servicio != 0 && $medico){
	if($result_preclinica->num_rows>1){
		$datos = array(
			0 => "Error", 
			1 => "Lo sentimos este registro ya existe no se puede almacenar", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",		
		);
	}else{   
	   $insert = "INSERT INTO preclinica 
		   VALUES('$numero', '$pacientes_id', '$expediente', '$medico', '$anos', '$fecha', '$pa', '$fr', '$fc', '$temperatura', '$peso', '$talla', '$servicio', '$observaciones', '$usuario','$paciente','$fecha_registro')";
	   $query = $mysqli->query($insert);
	   
	   if($query){
			$datos = array(
				0 => "Almacenado", 
				1 => "Registro Almacenado Correctamente", 
				2 => "success",
				3 => "btn-primary",
				4 => "formulario_agregar_preclinica",
				5 => "Registro",
				6 => "Preclinica",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
				7 => "agregar_preclinica", //Modals Para Cierre Automatico
			);
		   
		   if ($result_agenda->num_rows == 0){
				$numero_agenda = correlativo("agenda_id", "agenda");
				$fecha_cita =  date("Y-m-d H:i:s", strtotime($fecha));
				$fecha_cita_end =  date("Y-m-d H:i:s", strtotime($fecha));
				$status = 0;
				$color = "#DF0101";
				$observacion = "Se registro, fuera de admisión";
				$comentario = "Hecho en preclinica";
				$preclinica	= 1;	
				$postclinica = 0;
				$reprogramo = 2;
				$status_id = 0;
				
				$insert = "INSERT INTO agenda 
				 VALUES('$numero_agenda', '$pacientes_id', '$expediente', '$medico', '00:00', '$fecha_cita', '$fecha_cita_end', '$fecha_registro', '$status', '$color', '$observacion','$usuario','$servicio','$comentario','$preclinica','$postclinica','$reprogramo','$paciente','$status_id')";			   
				$mysqli->query($insert);
		   }
		   
		   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		   $historial_numero = historial();
		   $estado = "Actualizar";
		   $observacion = "Se actualiza el campo preclínica en la entidad agenda, desde preclínica";
		   $modulo = "Agenda";
		   $insert = "INSERT INTO historial 
			   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$numero','$medico','$servicio','$fecha','$estado','$observacion','$usuario','$fecha_registro')";	 
		   $mysqli->query($insert);
		   /*****************************************************/	   
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
	} 
}else{
	$datos = array(
		0 => "Error", 
		1 => "No se puedo almacenar este registro, el servicio y el profesional no pueden quedar en blanco, por favor corregir", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);	
}

echo json_encode($datos);
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>