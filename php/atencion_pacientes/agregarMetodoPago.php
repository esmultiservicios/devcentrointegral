<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$fecha_registro = date("Y-m-d H:i:s");

$usuario = $_SESSION['colaborador_id'];
$pacientes_id = $_POST['pacientes_id'];
$agenda_id = $_POST['agenda_id'];
$monto = $_POST['monto'];
$pago = 1; //1. Borrador 2. Pagado 3. Cancelado

if(isset($_POST['descuento'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['descuento'] == ""){
		$descuento = 0;
	}else{
		$descuento = $_POST['descuento'];
	}
}else{
	$descuento = 0;
}

if(isset($_POST['tipo_tarifa'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['tipo_tarifa'] == ""){
		$tipo_tarifa = 0;
	}else{
		$tipo_tarifa = $_POST['tipo_tarifa'];
	}
}else{
	$tipo_tarifa = 0;
}

$porcentaje = $_POST['porcentaje'];
$neto = str_replace(',','',$_POST['neto']);

$tipo_pago = 0;

//CONSULTAR DATOS DEL PACIENTE
$query_paciente = "SELECT expediente, CONCAT(nombre, ' ', apellido) AS 'paciente', identidad
    FROM pacientes
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($query_paciente) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();

$expediente = '';
$paciente = '';
$identidad = '';

if($result->num_rows>0){
	$expediente = $consulta_registro['expediente'];
	$paciente = $consulta_registro['paciente'];
	$identidad = $consulta_registro['identidad'];	
}	
	
//CONSULTAR DATOS DE LA AGENDA DEL PACIENTE
$query_agenda = "SELECT servicio_id, colaborador_id, CAST(fecha_cita AS DATE) AS 'fecha_cita'
    FROM agenda
	WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($query_agenda) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();	

$servicio_id = '';
$colaborador_id = '';
$fecha_cita = '';

if($result->num_rows>0){
	$servicio_id = $consulta_registro['servicio_id'];
	$colaborador_id = $consulta_registro['colaborador_id'];
	$fecha_cita = $consulta_registro['fecha_cita'];
}	

//CONSULTAR LA ATENCION DEL PACIENTE
$query_atencion = "SELECT atencion_id
	FROM atenciones_medicas 
	WHERE pacientes_id = '$pacientes_id' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id' AND fecha = '$fecha_cita'";
$result_atencion = $mysqli->query($query_atencion) or die($mysqli->error);
$consulta_atencion = $result_atencion->fetch_assoc();	

$atencion_id = "";

if($result_atencion->num_rows>0){
	$atencion_id = $consulta_atencion['atencion_id'];	
}
/*****************************************************************************************************************************************************************/
//OBTENER EL MONTO DE LA CONSULTA SEGUN EL COLABORADOR_ID
$consulta_tarifa = "SELECT tarifa_id
    FROM tarifas
	WHERE colaborador_id = '$colaborador_id' AND tarifas_tipo_id = '$tipo_tarifa'";
$result_tarifa = $mysqli->query($consulta_tarifa) or die($mysqli->error);
$consulta_tarifa2 = $result_tarifa->fetch_assoc();

$tarifa_id = '';

if($result_tarifa->num_rows>0){
    $tarifa_id = $consulta_tarifa2['tarifa_id'];
}
/*****************************************************************************************************************************************************************/

//CONSULTAMOS SI EL METODO DE PAGO NO SE HA ALMACENADO ANTES
$query_metodo_pago = "SELECT metodo_pago_id
   FROM metodo_pago
   WHERE pacientes_id = '$pacientes_id' AND agenda_id = '$agenda_id' AND fecha = '$fecha_cita' AND estado IN(1,2)";
$result = $mysqli->query($query_metodo_pago) or die($mysqli->error);

if($result->num_rows < 3){//NO EXISTE REGISTRO ALMACENADO
    $correlativo = correlativo('metodo_pago_id', 'metodo_pago');
	
	//GUARDAMOS LOS DATOS DEL METODO DE PAGO
	$insert = "INSERT INTO metodo_pago VALUES('$correlativo','$atencion_id','$pacientes_id','$agenda_id','$fecha_cita','$tarifa_id','$descuento','$porcentaje','$neto','$tipo_pago','$pago','$usuario','$fecha_registro')";
	$query = $mysqli->query($insert) or die($mysqli->error);
	
	if($query){
		echo 1;//REGISTRO ALMACENADO CORRECTAMENTE
		
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado el método de pago para este paciente: $paciente con identidad n° $identidad";
		$modulo = "Metodo de Pago";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$correlativo','$colaborador_id','$servicio_id','$fecha_cita','$estado_historial','$observacion_historial','$colaborador_id','$fecha_registro')";	 
		$mysqli->query($insert) or die($mysqli->error);
		/********************************************/		
	}else{
		echo 2;//ERROR AL MOMENTO DE ALMACENAR ESTE REGISTRO
	}
}else{//ESTE REGISTRO YA EXISTE
	echo 3;//ESTE REGISTRO YA EXISTE
}

$mysqli->close();//CERRAR CONEXIÓN
?>