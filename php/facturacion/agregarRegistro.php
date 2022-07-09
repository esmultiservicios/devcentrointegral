<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$metodo_pago_id = $_POST['metodo_pago_id'];
$colaborador_id = $_POST['colaborador_id'];
$agenda_id = $_POST['agenda_id'];
$activo = 1; //Secuencia de Factiración 1. Sí 2. No
$estado = 2; //1. Borrador 2. Pagada 3. Cancelado
$cierre = 2; //1. Sí 2. No
$estado_atencion = 2;//ESTADO DE LA ATENCION DEL PACIENTE PARA LA FACTURACION 1. PENDIENTE 2. PAGADA

if(isset($_POST['tipo_pago'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['tipo_pago'] == ""){
		$tipo_pago = 0;
	}else{
		$tipo_pago = $_POST['tipo_pago'];
	}
}else{
	$tipo_pago = 0;
}

//CONSULTAR FECHA DE REGISTRO
$query = "SELECT CAST(fecha_cita AS DATE) AS 'fecha', pacientes_id, servicio_id
   FROM agenda
   WHERE agenda_id = '$agenda_id'"; 
$result = $mysqli->query($query) or die($mysqli->error);
$consulta2 = $result->fetch_assoc();

$fecha = "";
$pacientes_id = "";
$servicio = "";

if($result->num_rows>0){
	$fecha = $consulta2['fecha'];
	$pacientes_id = $consulta2['pacientes_id'];
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
//COONSULTAR DATOS DE LA SECUENCIA DE FACTURACION
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
//CONSULTAMOS EL NUMERO DE ATENCION
$query_atencion = "SELECT atencion_id  
    FROM atenciones_medicas
	WHERE pacientes_id = '$pacientes_id' AND servicio_id = '$servicio' AND colaborador_id = '$colaborador_id' AND fecha = '$fecha'";
$result_atencion = $mysqli->query($query_atencion) or die($mysqli->error);
$consultaDatosAtencion = $result_atencion->fetch_assoc();

$atencion_id = "";

if($result_atencion->num_rows>0){
	$atencion_id = $consultaDatosAtencion['atencion_id'];	
}
/*******************************************************************************************************************************************************************/

$monto = $_POST['monto'];
$neto = $_POST['neto'];
$descuento = $monto - $neto;

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
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha_actual = date("Y-m-d");
$estado = 2;//Estado del Metodo de Pago 1. Borrador 2. Pagado 3. Cancelado

//VERIFICAMOS SI EXISTE EL REGISTRO
$query_factura = "SELECT factura_id
   FROM factura
   WHERE pacientes_id = '$pacientes_id' AND servicio_id = '$servicio' AND colaborador_id = '$colaborador_id ' AND fecha = '$fecha' AND estado IN(1,2)";
$result_facturas = $mysqli->query($query_factura) or die($mysqli->error);

if($result_facturas->num_rows < 3){
	//VERIFICAMOS QUE EL NUMERO CONSULTADO SEA MENOR QUE EL RANGO_FINAL
	if ($numero > $rango_final){
		echo 1;//EL NUMERO GENERADO ES MAYOR QUE EL RANGO PERMITIDO, SE DEBE ADQUIRIR UNA NUEVA FACTURACION
	}else if($fecha_actual > $fecha_limite){
		echo 2;//LA FECHA ES MAYOR A LA FECHA LIMITE PERMITIDA, SE DEBE ADQUIRIR UNA NUEVA FACTURACION
	}else{
		//GUARDAMOS LOS DATOS DE LA FACTURACIÓN
		$correlativo = correlativo('factura_id ', 'factura');
		$insert = "INSERT INTO factura VALUES('$correlativo','$metodo_pago_id','$fecha','$numero','$secuencia_facturacion_id','$pacientes_id','$servicio','$colaborador_id','$monto','$descuento','$neto','$banco','$referencia','$referencia1','$usuario','$estado','$cierre','$fecha_registro')";
		
		$query = $mysqli->query($insert) or die($mysqli->error);

		if($query){
			echo 3;//REGISTRO ALMACENADO CORRECTAMENTE
			
			//ACTUALIZAMOS EL METODO DE PAGO
			$update_metodo_pago = "UPDATE metodo_pago SET tipo_pago_id = '$tipo_pago' WHERE metodo_pago_id = '$metodo_pago_id'";
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
			$update = "UPDATE metodo_pago SET estado = '$estado' WHERE metodo_pago_id = '$metodo_pago_id'";
			$mysqli->query($update) or die($mysqli->error);
			/*********************************************************************************************************************************************************************/

			/*********************************************************************************************************************************************************************/
			//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			$historial_numero = historial();
			$estado_historial = "Agregar";
			$observacion_historial = "Se ha agregado la factura para este paciente: $paciente_nombre con identidad n° $identidad";
			$modulo = "Facturas";
			$insert = "INSERT INTO historial 
			   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$correlativo','$colaborador_id','$servicio','$fecha','$estado_historial','$observacion_historial','$colaborador_id','$fecha_registro')";	 
			$mysqli->query($insert) or die($mysqli->error);
			/********************************************/			
		}else{
			echo 4;//ERROR AL ALMACENAR ESTE REGISTRO
		}
	}
}else{
	echo 5;//ESTA FACTURA YA EXISTE, NO SE PUEDE ALMACENAR
}

$mysqli->close();//CERRAR CONEXIÓN
?>