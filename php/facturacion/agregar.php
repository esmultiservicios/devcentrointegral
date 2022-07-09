<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$usuario = $_SESSION['colaborador_id'];
$atencion_id = $_POST['atencion_id'];
$colaborador_id = $_POST['profesional_facturacion'];
$agenda_id = $_POST['agenda_id'];
$fecha_registro = date("Y-m-d H:i:s");

$pacientes_id = $_POST['paciente'];
$fecha = $_POST['fecha'];
$monto = $_POST['monto'];
$porcentaje = $_POST['porcentaje'];$porcentaje = $_POST['porcentaje'];
$neto = $_POST['neto'];
$descuento = $monto - $neto;

if(isset($_POST['tipo_pago'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['tipo_pago'] == ""){
		$tipo_pago = 0;
	}else{
		$tipo_pago = $_POST['tipo_pago'];
	}
}else{
	$tipo_pago = 0;
}

if(isset($_POST['banco'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['banco'] == ""){
		$banco = 0;
	}else{
		$banco = $_POST['banco'];
	}
}else{
	$banco = 0;
}

$referencia = cleanStringStrtolower($_POST['referencia']);
$referencia1 = cleanStringStrtolower($_POST['referencia1']);
$estado = 2;//ESTADO DE LA ATENCION DEL PACIENTE PARA LA FACTURACION 1. PENDIENTE 2. PAGADA
$activo = 1; //Secuencia de Factiración 1. Sí 2. No
$cierre = 2; //1. Sí 2. No
$estado_atencion = 2;//ESTADO DE LA ATENCION DEL PACIENTE PARA LA FACTURACION 1. PENDIENTE 2. PAGADA
$fecha_registro = date("Y-m-d H:i:s");
$fecha_actual = date("Y-m-d");

//CONSULTAR FECHA DE REGISTRO
$query = "SELECT CAST(fecha_cita AS DATE) AS 'fecha', pacientes_id, servicio_id
   FROM agenda
   WHERE agenda_id = '$agenda_id'"; 
$result = $mysqli->query($query) or die($mysqli->error);
$consulta2 = $result->fetch_assoc();

$fecha = "";
$servicio = "";

if($result->num_rows>0){
	$fecha = $consulta2['fecha'];
	$servicio = $consulta2['servicio_id'];	
}

//OBTENER DATOS DEL PACIENTE
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
/*******************************************************************************************************************************************************************/

/*******************************************************************************************************************************************************************/
//CONSULTAR DATOS DE LA SECUENCIA DE FACTURACION
$query_secuencia = "SELECT secuencia_facturacion_id, prefijo, siguiente AS 'numero', rango_final, fecha_limite
   FROM secuencia_facturacion
   WHERE activo = '$activo'";
$result = $mysqli->query($query_secuencia) or die($mysqli->error);
$consulta2 = $result->fetch_assoc();

$secuencia_facturacion_id = "";
$prefijo = "";
$numero = "";
$rango_final = "";
$fecha_limite = "";

if($result->num_rows>0){
	$secuencia_facturacion_id = $consulta2['secuencia_facturacion_id'];	
	$prefijo = $consulta2['prefijo'];
	$numero = $consulta2['numero'];
	$rango_final = $consulta2['rango_final'];
	$fecha_limite = $consulta2['fecha_limite'];	
}
/*******************************************************************************************************************************************************************/
	
//CONSULTAR TARIFA DE COLABORADOR
$query_tarifa = "SELECT tarifa_id 
   FROM tarifas
   WHERE colaborador_id = '$colaborador_id'";
$result_tarifa = $mysqli->query($query_tarifa) or die($mysqli->error);
$consultaTarifa = $result_tarifa->fetch_assoc();

$tarifa_id = "";

if($result_tarifa->num_rows>0){
	$tarifa_id = $consultaTarifa['tarifa_id'];
}

//VERIFICAMOS SI YA EXISTE LA FACTURA PARA ESTE REGISTRO
$query_existencia_factura = "SELECT factura_id
    FROM factura
	WHERE pacientes_id = '$pacientes_id' AND servicio_id = '$servicio' AND colaborador_id = '$colaborador_id' AND fecha = '$fecha' AND estado IN(1,2)";
$result_existencia_factura = $mysqli->query($query_existencia_factura) or die($mysqli->error);

//VERIFICAMOS SI YA EXISTE EL METODO DE PAGO PARA ESTE REGISTRO
$query_existencia_metodoPago = "SELECT metodo_pago_id
    FROM metodo_pago
	WHERE pacientes_id = '$pacientes_id' AND agenda_id = '$agenda_id' AND fecha = '$fecha' AND estado IN(1,2)";
$result_existenciaMetodoPago = $mysqli->query($query_existencia_metodoPago) or die($mysqli->error);

if($result_existenciaMetodoPago->num_rows==0){
	//AGREGAR METODO DE PAGO
	$metodo_pago_id = correlativo('metodo_pago_id ', 'metodo_pago');
	$insert_metodo = "INSERT metodo_pago VALUES('$metodo_pago_id','$pacientes_id','$agenda_id','$fecha','$tarifa_id','$descuento','$porcentaje','$neto','$tipo_pago','$estado','$usuario','$fecha_registro')";
	$mysqli->query($insert_metodo) or die($mysqli->error);	
}

//CONSULTAMOS EL METODO DE PAGO
$consulta_metodoPago = "SELECT metodo_pago_id
    FROM metodo_pago
	WHERE pacientes_id = '$pacientes_id' AND agenda_id = '$agenda_id' AND fecha = '$fecha' AND estado = 2";
$result_metodoPago = $mysqli->query($consulta_metodoPago) or die($mysqli->error);
$consultaMetodoPago = $result_metodoPago->fetch_assoc();

$metodo_pago_id_consulta = "";

if($result_metodoPago->num_rows>0){
	$metodo_pago_id_consulta = $consultaMetodoPago['metodo_pago_id'];
}
	
if($result_existencia_factura->num_rows < 3){
	//VERIFICAMOS QUE EL NUMERO CONSULTADO SEA MENOR QUE EL RANGO_FINAL
	if ($numero > $rango_final){
		echo 1;//EL NUMERO GENERADO ES MAYOR QUE EL RANGO PERMITIDO, SE DEBE ADQUIRIR UNA NUEVA FACTURACION
	}else if($fecha_actual > $fecha_limite){
		echo 2;//LA FECHA ES MAYOR A LA FECHA LIMITE PERMITIDA, SE DEBE ADQUIRIR UNA NUEVA FACTURACION
	}else{
		/**********************************************************************************************************************************************************************/
		$factura_id = correlativo('factura_id ', 'factura');
		$insert_factura = "INSERT INTO factura VALUES('$factura_id','$metodo_pago_id_consulta','$fecha','$numero','$secuencia_facturacion_id','$pacientes_id','$servicio','$colaborador_id','$monto','$descuento','$neto','$banco','$referencia','$referencia1','$usuario','$estado','$cierre','$fecha_registro')";
		$query = $mysqli->query($insert_factura) or die($mysqli->error);
		/**********************************************************************************************************************************************************************/
			
		if($query){
			echo 3;//REGISTRO ALMACENADO CORRECTAMENTE
			
			//ACTUALIZAMOS EL METODO DE PAGO
			$update_metodo_pago = "UPDATE metodo_pago SET tipo_pago_id = '$tipo_pago' WHERE atencion_id = '$atencion_id'";
			$mysqli->query($update_metodo_pago) or die($mysqli->error);	
			/*********************************************************************************************************************************************************************/
			//ACTUALIZAMOS EL ESTADO DE LA ATENCION PARA SABER SI SE PAGO O NO LA FACTURA
			$update_atencion = "UPDATE atenciones_medicas SET estado = '$estado_atencion' WHERE atencion_id  = '$atencion_id'";
			$mysqli->query($update_atencion) or die($mysqli->error);
			/*********************************************************************************************************************************************************************/
						
			/*********************************************************************************************************************************************************************/
			//ACTUALIZAMOS EL SIGUIENTE NUMERO EN EL ADMINISTRADOR DE SECUENCIAS
			$numero ++;
			$update = "UPDATE secuencia_facturacion SET siguiente = '$numero' WHERE secuencia_facturacion_id = '$secuencia_facturacion_id'";
			$mysqli->query($update) or die($mysqli->error);
			/*********************************************************************************************************************************************************************/
			
			/*********************************************************************************************************************************************************************/
			//ACTUALIZAMOS EL METODO DE PAGO PARA INDICAR QUE YA SE PAGO CORRECTAMENTE LA FACUTRA
			$update = "UPDATE metodo_pago SET estado = '$estado' WHERE metodo_pago_id = '$metodo_pago_id_consulta'";
			$mysqli->query($update) or die($mysqli->error);
			/*********************************************************************************************************************************************************************/

			/*********************************************************************************************************************************************************************/
			//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			$historial_numero = historial();
			$estado_historial = "Agregar";
			$observacion_historial = "Se ha agregado la factura para este paciente: $paciente_nombre con identidad n° $identidad";
			$modulo = "Facturas";
			$insert = "INSERT INTO historial 
			   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$factura_id','$colaborador_id','$servicio','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
			$mysqli->query($insert) or die($mysqli->error);
			/********************************************/				
		}else{
			echo 4;//ERROR AL ALMACENAR ESTE REGISTRO
		}
	}
	}else{
  echo 5;//YA EXISTE UNA FACTURA PARA ESTE REGISTRO
}

$mysqli->close();//CERRAR CONEXIÓN
?>