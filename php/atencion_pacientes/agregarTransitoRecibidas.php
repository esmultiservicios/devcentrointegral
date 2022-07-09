<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['paciente_tr'];
$fecha = $_POST['fecha'];
$colaborador_id = $_SESSION['colaborador_id'];
$recibida = $_POST['recibida'];
$motivo = cleanStringStrtolower($_POST['motivo']);
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");

//CONSULTAR PUESTO
$consulta_puesto = "SELECT puesto_id 
   FROM colaboradores 
   WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta_puesto);
$consulta_puesto1 = $result->fetch_assoc();
$puesto_id = $consulta_puesto1['puesto_id'];	  

//CONSULTAR FECHA DE NACIMIENTO
$consulta_pacientes = "SELECT fecha_nacimiento, departamento_id, municipio_id, expediente
   FROM pacientes 
   WHERE pacientes_id = '$pacientes_id'";
$result_pacientes = $mysqli->query($consulta_pacientes);

$fecha_nacimiento = "";
$expediente = "";
$valores_array = "";
$anos = "";
$meses = "";
$dias = "";

if($result_pacientes->num_rows>0){
	$consulta_pacientes =$result_pacientes->fetch_assoc();
	$fecha_nacimiento = $consulta_pacientes['fecha_nacimiento'];
	$expediente = $consulta_pacientes['expediente'];	
	/*********************************************************************************/
	$valores_array = getEdad($fecha_nacimiento);
	$anos = $valores_array['anos'];
	$meses = $valores_array['meses'];	  
	$dias = $valores_array['dias'];	 
	/*********************************************************************************/
}
	
//CONSULTAMOS SI EXISTE ATENCION ALMACENADA PARA EL USUARIO EN ESTA FECHA CON EL PROFESIONAL
$query_atencion = "SELECT atencion_id, paciente, servicio_id
	FROM atenciones_medicas
	WHERE pacientes_id = '$pacientes_id' AND fecha = '$fecha' AND colaborador_id = '$colaborador_id'";
$result_atencion = $mysqli->query($query_atencion);	


//EVALUAMOS SI EXISTE LA ANTENCION EN LA ENTIDAD TRANSITO ENVIADA
$query_transito = "SELECT transito_id
	FROM transito_recibida
	WHERE pacientes_id = '$pacientes_id' AND fecha = '$fecha' AND colaborador_id = '$colaborador_id'";	 
$result_transito = $mysqli->query($query_transito);	

if($result_transito->num_rows==0){
	if($result_atencion->num_rows>0){
		$consultar_atencion = $result_atencion->fetch_assoc(); 
		$atencion_id = $consultar_atencion['atencion_id'];	
		$paciente = $consultar_atencion['paciente'];
		$servicio_id = $consultar_atencion['servicio_id'];	
		
		//INGRFESAMOS LOS DATOS EN EL TRANSITO ENVIADA
		$transito_id = correlativo("transito_id", "transito_enviada");
		$insert = "INSERT INTO transito_recibida VALUES('$transito_id','$fecha','$atencion_id','$pacientes_id','$colaborador_id','$anos','$paciente','$recibida','$servicio_id','$motivo','$fecha_registro')";
		$query = $mysqli->query($insert);
		
		if($query){
			echo 1;//RFGISTRO ALMACENADO CORRECTAMENTE
		   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		   $historial_numero = historial();
		   $estado = "Agregar";
		   $observacion = "Se ha agregado el transito para este usuario";
		   $modulo = "Transito Recibida";
		   $insert = "INSERT INTO historial 
			   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$transito_id','$colaborador_id','$servicio_id','$fecha','$estado','$observacion','$usuario','$fecha_registro')";	 
		   $mysqli->query($insert);
		   /*****************************************************/			
		}else{
			echo 2;//NO SE PUDO ALMACENAR EL REGISTRO
		}
	}else{
		echo 3; //ESTE REGISTRO NO CUENTA CON ATENCION PARA ESTE DIA
	}
}else{
	echo 4;//ESTE REGISTRO YA EXISTE
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>