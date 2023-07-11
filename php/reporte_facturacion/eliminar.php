<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$secuencia_facturacion_id = $_POST['secuencia_facturacion_id'];
$comentario = cleanStringStrtolower($_POST['comentario']);
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

//OBTENER LOS DATOS DEL REGISTRO
//CONSULTAR DATOS DEL METODO DE PAGO
$query = "SELECT * FROM secuencia_facturacion
     WHERE secuencia_facturacion_id = '$secuencia_facturacion_id'";
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();   
     
$empresa = "";
$cai = "";
$prefijo = "";
$relleno = "";
$incremento = "";
$siguiente = "";
$rango_inicial = "";
$rango_final = "";
$fecha_activacion = "";
$fecha_limite = "";
$activo = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$empresa = $consulta_registro['empresa_id'];
	$cai = $consulta_registro['cai'];	
	$prefijo = $consulta_registro['prefijo'];
	$relleno = $consulta_registro['relleno'];	
	$incremento = $consulta_registro['incremento'];
	$siguiente = $consulta_registro['siguiente'];
	$rango_inicial = $consulta_registro['rango_inicial'];	
	$rango_final = $consulta_registro['rango_final'];
	$fecha_activacion = $consulta_registro['fecha_activacion'];
	$fecha_limite = $consulta_registro['fecha_limite'];
	$activo = $consulta_registro['activo'];		
}

//VERIFICAMOS QUE LA SECUENCIA NO ESTE ALMACENADA EN LA FACTURACION
$query = "SELECT factura_id 
    FROM factura
	WHERE secuencia_facturacion_id = '$secuencia_facturacion_id'";
$result = $mysqli->query($query);

if($result->num_rows==0){
	//ELIMINAOS EL REGISTRO
	$delete = "DELETE FROM secuencia_facturacion WHERE secuencia_facturacion_id = '$secuencia_facturacion_id'";
	$query = $mysqli->query($delete) or die($mysqli->error);
	
	if($query){
		echo 1;//REGISTRO ELIMINADO CORRECTAMENTE
		
		/**************************************************************************************************************************************************/
		//AGREGAMOS LOS DATOS EN EL HISTORIAL DE SECUENCIAS
		$correlativo = correlativo('secuencia_facturacion_historial_id ', 'secuencia_facturacion_historial');
		$insert = "INSERT INTO secuencia_facturacion_historial VALUES('$correlativo','$secuencia_facturacion_id','$empresa', '$cai','$prefijo','$relleno','$incremento','$siguiente','$rango_inicial','$rango_final','$fecha_activacion','$fecha_limite','$activo','$usuario','$comentario','$fecha_registro')";
		$mysqli->query($insert) or die($mysqli->error);
		/**************************************************************************************************************************************************/
		
		/**************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Eliminar";
		$observacion_historial = "Se ha eliminado la secuencia de facturación con el prefijo: $prefijo y rangos desde $rango_inicial a $rango_final";
		$modulo = "Secuencia Facturación";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$correlativo','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
		$mysqli->query($insert) or die($mysqli->error);
		/**************************************************************************************************************************************************/	   	
	}else{
		echo 2;//ERROR AL ELIMINAR ESTE REGISTRO
	}
}else{
	echo 3;//ESTE REGISTRO NO SE PUEDE ELIMINAR CUANTA CON INFORMACIÓN ALMACENADA
}

$mysqli->close();//CERRAR CONEXIÓN  
?>