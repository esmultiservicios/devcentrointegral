<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

header("Content-Type: text/html;charset=utf-8");
$proceso = $_POST['pro'];
$id = $_POST['id-registro'];
$expediente = $_POST['expediente'];
$fecha = $_POST['fecha'];
$pa = $_POST['pa'];
$fr = $_POST['fr'];
$fc = $_POST['fc'];
$temperatura = $_POST['temperatura'];
$peso = $_POST['peso'];
$talla = $_POST['talla'];
$fecha_registro = date("Y-m-d H:i:s");
$observaciones = cleanStringStrtolower($_POST['observaciones']);
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");

//CONSULTAR SERVICIO
$consulta_servicio = "SELECT a.servicio_id, a.pacientes_id, a.colaborador_id, c.puesto_id
   FROM agenda AS a
   INNER JOIN colaboradores AS c
   ON a.colaborador_id = c.colaborador_id
   WHERE a.agenda_id = '$id'";
$result = $mysqli->query($consulta_servicio);   
$consulta_servicio2 = $result->fetch_assoc();
$servicio = $consulta_servicio2['servicio_id'];
$pacientes_id = $consulta_servicio2['pacientes_id'];
$medico = $consulta_servicio2['colaborador_id'];
$puesto_id = $consulta_servicio2['puesto_id'];
$postclinica = 0;

if($servicio == 7){
	$postclinica = 0;
}else{
	$postclinica = 1;
}

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
WHERE a.expediente = '$expediente' AND cast(a.fecha_cita AS DATE) = '$fecha' AND c.colaborador_id = '$medico' AND a.servicio_id = '$servicio'";
$result_agenda = $mysqli->query($consultar_agenda);
$consultar_agenda1 = $result_agenda->fetch_assoc();
$agenda_id = $consultar_agenda1['agenda_id'];

//CONSULTAR Registro
$consultar_registro = "SELECT p.preclinica_id 
   FROM preclinica AS p
   WHERE p.expediente = '$expediente' AND p.fecha = '$fecha' AND p.servicio_id = '$servicio' AND p.colaborador_id = '$medico'"; 
$result_preclinica = $mysqli->query($consultar_registro);

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
   
   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
   $historial_numero = historial();
   $estado = "Agregar";
   $observacion = "Se realizó la preclínica para este usuario";
   $modulo = "Preclinica";
   $insert = "INSERT INTO historial 
	   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$numero','$medico','$servicio','$fecha','$estado','$observacion','$usuario','$fecha_registro')";	 
   $mysqli->query($insert);
   /*****************************************************/
   
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
			8 => "",
			9 => "Guardar",			
		);
	   
	   $update = "UPDATE agenda SET preclinica = 1 
		   WHERE agenda_id = '$id' AND CAST(fecha_cita AS DATE) = '$fecha'";
	   $mysqli->query($update);
	   
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

echo json_encode($datos);
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>