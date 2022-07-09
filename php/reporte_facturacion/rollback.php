<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$facturas_id = $_POST['facturas_id'];
$comentario = cleanStringStrtolower($_POST['comentario']);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];
$estado = 3; //1. Borrador 2. Pagada 3. Cancelado
$estado_pago = 2; //1. Borrador 2. Cancelado
$estado_atencion = 1;//ESTADO DE LA ATENCION DEL PACIENTE PARA LA FACTURACION 1. PENDIENTE 2. PAGADA

//OBTENER DATOS DE LA FACTURA
$query_factura = "SELECT sf.prefijo AS 'prefijo', f.number AS 'numero', sf.relleno AS 'relleno', f.pacientes_id AS 'pacientes_id', p.expediente AS 'expediente', f.colaborador_id AS 'colaborador_id', f.servicio_id AS 'servicio_id', f.fecha AS 'fecha_factura'
   FROM facturas AS f
   INNER JOIN secuencia_facturacion AS sf
   ON f.secuencia_facturacion_id = sf.secuencia_facturacion_id
   INNER JOIN pacientes AS p
   ON f.pacientes_id = p.pacientes_id
   WHERE f.facturas_id = '$facturas_id'";
$result = $mysqli->query($query_factura) or die($mysqli->error);			  
$consultaDatosFactura = $result->fetch_assoc();

$numero_factura = '';
$pacientes_id = '';
$expediente = '';
$colaborador_id = '';
$servicio_id = '';
$fecha_factura = '';

if($result->num_rows>0){
	$numero_factura = $consultaDatosFactura['prefijo'].''.rellenarDigitos($consultaDatosFactura['numero'], $consultaDatosFactura['relleno']);
	$pacientes_id = $consultaDatosFactura['pacientes_id'];
	$expediente = $consultaDatosFactura['expediente'];	
	$colaborador_id = $consultaDatosFactura['colaborador_id'];
	$servicio_id = $consultaDatosFactura['servicio_id'];
	$fecha_factura = $consultaDatosFactura['fecha_factura'];	
}

/*******************************************************************************************************************************************************************/
//CONSULTAMOS EL NUMERO DE ATENCION
$query_atencion = "SELECT atencion_id  
    FROM atenciones_medicas
	WHERE pacientes_id = '$pacientes_id' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id' AND fecha = '$fecha_factura'";
$result_atencion = $mysqli->query($query_atencion) or die($mysqli->error);
$consultaDatosAtencion = $result_atencion->fetch_assoc();

$atencion_id = "";

if($result_atencion->num_rows>0){
	$atencion_id = $consultaDatosAtencion['atencion_id'];	
}
/*******************************************************************************************************************************************************************/

//ACTUALIZAMOS EL METODO DE PAGO, CAMBIAMOS EL ESTADO A CANCELADO
$update_metodoPago = "UPDATE pagos SET estado = '$estado_pago' WHERE facturas_id = '$facturas_id'";
$query = $mysqli->query($update_metodoPago) or die($mysqli->error);

if($query){
	echo 1;//FACTURA CANCELADA CORRECTAMENTE
	//ACTUALIZAMOS LA FACTURA, CAMBIAMOS EL ESTADO A CANCELADO
	$update_factura = "UPDATE facturas SET estado = '$estado' WHERE facturas_id  = '$facturas_id'";
	$mysqli->query($update_factura) or die($mysqli->error);
	
	/*********************************************************************************************************************************************************************/
	//ACTUALIZAMOS EL ESTADO DE LA ATENCION PARA SABER SI SE PAGO O NO LA FACTURA
	$update_atencion = "UPDATE atenciones_medicas SET estado = '$estado_atencion' WHERE atencion_id  = '$atencion_id'";
	$mysqli->query($update_atencion) or die($mysqli->error);
	/*********************************************************************************************************************************************************************/
	
	/*********************************************************************************************************************************************************************/
	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Agregar";
	$observacion_historial = "el número de factura $numero_factura ha sido eliminada correctamente segun comentario: $comentario";
	$modulo = "Facturas";
	$insert = "INSERT INTO historial 
	   VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$facturas_id','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$estado','$fecha_registro')";	 
	$mysqli->query($insert) or die($mysqli->error);
	/********************************************/		
}else{
	echo 2;//ERROR AL CANCELAR LA FACTURA
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>