<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$agenda_id = $_POST['agenda_id'];
$pacientes_id = $_POST['pacientes_id'];
$fecha = $_POST['fecha'];
$antecedentes = cleanStringStrtolower($_POST['antecedentes']);
$historia_clinica = cleanStringStrtolower($_POST['historia_clinica']);
$exame_fisico = cleanStringStrtolower($_POST['exame_fisico']);
$diagnostico = cleanStringStrtolower($_POST['diagnostico']);
$seguimiento = cleanStringStrtolower($_POST['seguimiento']);
$colaborador_id = $_SESSION['colaborador_id'];
$hora = date("H:i", strtotime('00:00'));
$fecha_cita =  date("Y-m-d H:i:s", strtotime($fecha));
$fecha_cita_end =  date("Y-m-d H:i:s", strtotime($fecha));
$fecha_registro = date("Y-m-d H:i:s");
$status = 1;//ESTADO PARA LA AGENDA DEL PACIENTE
$estado = 1;//ESTADO DE LA ATENCION DEL PACIENTE PARA LA FACTURACION 1. PENDIENTE 2. PAGADA

$fecha_nacimiento = $_POST['fecha_nac'];

if(isset($_POST['religion_id'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['religion_id'] == ""){
		$religion_id = 0;
	}else{
		$religion_id = $_POST['religion_id'];
	}
}else{
	$religion_id = 0;
}

if(isset($_POST['profesion_id'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['profesion_id'] == ""){
		$profesion_id = 0;
	}else{
		$profesion_id = $_POST['profesion_id'];
	}
}else{
	$profesion_id = 0;
}

//CONSULTAR SERVICIO_ID
$query_servicio = "SELECT servicio_id
	FROM agenda
	WHERE pacientes_id = '$pacientes_id' AND CAST(fecha_cita AS DATE) = '$fecha' AND status = 0";
$result_servicio = $mysqli->query($query_servicio) or die($mysqli->error);
$consultar_servicio = $result_servicio->fetch_assoc(); 

$servicio_id = "";

if($result_servicio->num_rows>=0){
	$servicio_id = $consultar_servicio['servicio_id'];
}

/*##############################################################################################################################################################################################*/
//ACTUALIZAMOS LOS DATOS DEL PACIENTE
$update = "UPDATE pacientes SET fecha_nacimiento = '$fecha_nacimiento', religion_id = '$religion_id', profesion_id = '$profesion_id'
	WHERE pacientes_id = '$pacientes_id'";
$mysqli->query($update) or die($mysqli->error);
/*##############################################################################################################################################################################################*/
			
//CONSULTA AÑO, MES y DIA DEL PACIENTE
$valores_array = getEdad($fecha_nacimiento);
$anos = $valores_array['anos'];
$meses = $valores_array['meses'];	  
$dias = $valores_array['dias'];	
/*********************************************************************************/

$consultar_tipo_paciente = "SELECT atencion_id 
FROM atenciones_medicas AS am
INNER JOIN colaboradores AS c
ON am.colaborador_id = c.colaborador_id
WHERE am.pacientes_id = '$pacientes_id' AND am.colaborador_id = '$colaborador_id' AND am.servicio_id = '$servicio_id'";
$result_tipo_paciente = $mysqli->query($consultar_tipo_paciente) or die($mysqli->error);

$tipo_paciente = '';

if($result_tipo_paciente->num_rows==0){
	$tipo_paciente = 'N';
}else{
	$tipo_paciente = 'S';
}

//CONSULTA DATOS DEL PACIENTE
$query = "SELECT CONCAT(nombre, ' ', apellido) AS 'paciente', identidad, expediente AS 'expediente'
	FROM pacientes
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();

$paciente = '';
$identidad = '';
$expediente = '';

if($result->num_rows>1){
	$paciente = $consulta_registro['paciente'];
	$identidad = $consulta_registro['identidad'];
	$expediente = $consulta_registro['expediente'];
}	

//CONSULTAMOS SI EXITE LA ATENCION
$query = "SELECT atencion_id 
   FROM atenciones_medicas
   WHERE pacientes_id = '$pacientes_id' AND fecha = '$fecha' AND servicio_id = '$servicio_id'";
$result_existencia = $mysqli->query($query) or die($mysqli->error);   

//OBTENER CORRELATIVO
$correlativo = correlativo('atencion_id', 'atenciones_medicas');

if($historia_clinica != "" && $exame_fisico != "" && $diagnostico != "" && $seguimiento != ""){
	if($pacientes_id != 0){
		if($servicio_id != 0){
			if($result_existencia->num_rows < 3){		
				$insert = "INSERT INTO atenciones_medicas VALUES('$correlativo','$pacientes_id','$anos','$fecha','$antecedentes','$historia_clinica','$exame_fisico','$diagnostico','$seguimiento','$tipo_paciente','$servicio_id','$colaborador_id','$estado','$fecha_registro')";
				$query = $mysqli->query($insert) or die($mysqli->error);

				if($query){
					$datos = array(
						0 => "Almacenado", 
						1 => "Registro Almacenado Correctamente", 
						2 => "success",
						3 => "btn-primary",
						4 => "formulario_atenciones",
						5 => "Registro",
						6 => "AtencionMedica",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
						7 => "modal_registro_atenciones", //Modals Para Cierre Automatico
						8 => $correlativo,
						9 => "Guardar",
					);
					
					//ACTUALIZAMOS EL ESTADO DE LA AGENDA
					$update = "UPDATE agenda SET status = '$status'
					   WHERE agenda_id = '$agenda_id'";	
					$mysqli->query($update) or die($mysqli->error);
					
					//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
					$historial_numero = historial();
					$estado_historial = "Agregar";
					$observacion_historial = "Se ha agregado una nueva atención para este paciente: $paciente con identidad n° $identidad";
					$modulo = "Atención Pacientes";
					$insert = "INSERT INTO historial 
					   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$correlativo','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$colaborador_id','$fecha_registro')";	 
					$mysqli->query($insert) or die($mysqli->error);
					/********************************************/
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
			}else{
				$datos = array(
					0 => "Error", 
					1 => "Lo sentimos este registro ya existe no se puede almacenar", 
					2 => "error",
					3 => "btn-danger",
					4 => "",
					5 => "",		
				);
			}
		}else{
			$datos = array(
				0 => "Error", 
				1 => "Lo sentimos, debe seleccionar un consultorio antes de continuar, por favor corregir", 
				2 => "error",
				3 => "btn-danger",
				4 => "",
				5 => "",			
			);
		}		
	}else{
		$datos = array(
			0 => "Error", 
			1 => "Lo sentimos, debe seleccionar un paciente antes de continuar, por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);	
	}
}else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos, valide que los Antecedentes, Historia Clínica, Examen Físico, Diagnostico  y Seguimiento no deben quedar vacíos, por favor corregir", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);		
}
echo json_encode($datos);

$mysqli->close();//CERRAR CONEXIÓN
?>