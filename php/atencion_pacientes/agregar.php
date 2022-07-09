<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

if(isset($_POST['paciente_consulta'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['paciente_consulta'] == ""){
		$pacientes_id = 0;
	}else{
		$pacientes_id = $_POST['paciente_consulta'];
	}
}else{
	$pacientes_id = 0;
}

$fecha = $_POST['fecha'];
$servicio_id = $_POST['servicio_id'];

$antecedentes = cleanStringStrtolower($_POST['antecedentes']);
$historia_clinica = cleanStringStrtolower($_POST['historia_clinica']);
$exame_fisico = cleanStringStrtolower($_POST['exame_fisico']);
$diagnostico = cleanStringStrtolower($_POST['diagnostico']);
$seguimiento = cleanStringStrtolower($_POST['seguimiento']);

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

$colaborador_id = $_SESSION['colaborador_id'];
$hora = date("H:i", strtotime('00:00'));
$fecha_cita =  date("Y-m-d H:i:s", strtotime($fecha));
$fecha_cita_end =  date("Y-m-d H:i:s", strtotime($fecha));
$fecha_registro = date("Y-m-d H:i:s");
$status = 1;//ESTADO PARA LA AGENDA DEL PACIENTE
$estado = 1;//ESTADO DE LA ATENCION DEL PACIENTE PARA LA FACTURACION 1. PENDIENTE 2. PAGADA

/*********************************************************************************************************************************************************************/
//CONSULTAMOS SI EL PACIENTE ES NUEVO O SUBSIGUIENTE		 
$consultar_expediente = "SELECT a.agenda_id
	FROM agenda AS a 
	INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id
	WHERE a.pacientes_id = '$pacientes_id' AND c.colaborador_id = '$colaborador_id' AND a.status = 1";
$result = $mysqli->query($consultar_expediente) or die($mysqli->error);
$consultar_expediente1 = $result->fetch_assoc(); 

if ($consultar_expediente1['agenda_id']== ""){
	$paciente = 'N';
	$color = '#008000'; //VERDE;
}else{ 
	$paciente = 'S';
	$color = '#0071c5'; //AZUL;
}	
/*********************************************************************************************************************************************************************/

//CONSULTA DATOS DEL PACIENTE
$query = "SELECT CONCAT(nombre, ' ', apellido) AS 'paciente', identidad, expediente AS 'expediente'
	FROM pacientes
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();

$paciente_nombre = '';
$identidad = '';
$expediente = '';

if($result->num_rows>0){
	$paciente_nombre = $consulta_registro['paciente'];
	$identidad = $consulta_registro['identidad'];
	$expediente = $consulta_registro['expediente'];
}	

//CONSULTAMOS SI EXITE LA ATENCION
$query = "SELECT atencion_id 
   FROM atenciones_medicas
   WHERE pacientes_id = '$pacientes_id' AND fecha = '$fecha' AND servicio_id = '$servicio_id'";
$result_existencia = $mysqli->query($query) or die($mysqli->error);  

//VALIDAMOS SI EXISTE AGENDA PARA ESTE USUARIO
$query_agenda = "SELECT agenda_id
   FROM agenda
   WHERE CAST(fecha_cita AS DATE) = '$fecha' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id' AND pacientes_id = '$pacientes_id'"; 
$result_agenda = $mysqli->query($query_agenda) or die($mysqli->error); 

//OBTENER CORRELATIVO
$correlativo = correlativo('atencion_id', 'atenciones_medicas');

if($historia_clinica != "" && $exame_fisico != "" && $diagnostico != "" && $seguimiento != ""){
	if($pacientes_id != 0){
		if($servicio_id != 0){
			if($result_agenda->num_rows < 3){
				if($result_existencia->num_rows < 3){		
					$insert = "INSERT INTO atenciones_medicas VALUES('$correlativo','$pacientes_id','$anos','$fecha','$antecedentes','$historia_clinica','$exame_fisico','$diagnostico','$seguimiento','$paciente','$servicio_id','$colaborador_id','$estado','$fecha_registro')";
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
							  
						$observacion = "Usuario agregado de forma manual";
						$comentario = "";
						$preclinica = 1;
						$postclinica = 0;
						$reprogramo = 2; //1. Sí 2. No
						$status_id = 0;
						
						/*********************************************************************************************************************************************************************/
						//AGREGAMOS LA AGENDA DEL PACINETE
						$correlativo_agenda = correlativo('agenda_id', 'agenda');		
						$insert_agenda = "INSERT INTO agenda VALUES ('$correlativo_agenda','$pacientes_id','$expediente','$colaborador_id','$hora','$fecha_cita','$fecha_cita_end','$fecha_registro','$status','$color','$observacion','$colaborador_id','$servicio_id','$comentario','$preclinica','$postclinica','$reprogramo','$paciente','$status_id')";
						
						$mysqli->query($insert_agenda) or die($mysqli->error);		
						/*********************************************************************************************************************************************************************/
						/*********************************************************************************************************************************************************************/
						//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
						$historial_numero = historial();
						$estado_historial = "Agregar";
						$observacion_historial = "Se ha agregado una nueva atención para este paciente: $paciente_nombre con identidad n° $identidad";
						$modulo = "Atención Pacientes";
						$insert = "INSERT INTO historial 
						   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$correlativo_agenda','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$colaborador_id','$fecha_registro')";	 
						$mysqli->query($insert) or die($mysqli->error);
						/*********************************************************************************************************************************************************************/
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
					1 => "Lo sentimos, este paciente ya cuenta con agenda almacenada para este día, por favor, revise sus registros pendientes", 
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