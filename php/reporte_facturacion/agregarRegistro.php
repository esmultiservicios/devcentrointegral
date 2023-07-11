<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$secuencia_facturacion_id = $_POST['secuencia_facturacion_id'];
$estado = $_POST['estado'];

//CONSULTAR EL NUMERO DEL ADMINISTRADOR DE SECUENCIAS
$query = "SELECT siguiente as 'numero_anterior'
   FROM secuencia_facturacion
   WHERE secuencia_facturacion_id = '$secuencia_facturacion_id'";
$result_datos = $mysqli->query($query) or die($mysqli->error);
$consulta_datos2 = $result_datos->fetch_assoc();

$numero_anterior = "";

if($result_datos->num_rows>0){
	$numero_anterior = $consulta_datos2['numero_anterior'];		
}

if(isset($_POST['empresa'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['empresa'] == ""){
		$empresa = 0;
	}else{
		$empresa = $_POST['empresa'];
	}
}else{
	$empresa = 0;
}

$cai = $_POST['cai'];
$prefijo = $_POST['prefijo'];
$relleno = $_POST['relleno'];
$incremento = $_POST['incremento'];
$siguiente = $_POST['siguiente'];
$rango_inicial = $_POST['rango_inicial'];
$rango_final = $_POST['rango_final'];
$fecha_activacion = $_POST['fecha_activacion'];
$fecha_limite = $_POST['fecha_limite'];
$usuario = $_SESSION['colaborador_id'];
$comentario = "";

if(isset($_POST['estado'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['estado'] == ""){
		$estado = 0;
	}else{
		$estado = $_POST['estado'];
	}
}else{
	$estado = 0;
}

//CONSULTAMOS SI EL NUMERO SIGUIENTE NO EXISTE EN LA FACTURACION
$query = "SELECT facturas_id
   FROM facturas 
   WHERE number = '$siguiente' AND secuencia_facturacion_id = '$secuencia_facturacion_id'";
$result = $mysqli->query($query) or die($mysqli->error);

//VERIFICAMOS SI HAY UNA SECUENCIA ACTIVA ANTES DE ACTIVAR ESTA
$query_secuencia = "SELECT secuencia_facturacion_id
	FROM secuencia_facturacion
	WHERE activo = 1";
$result_secuencia = $mysqli->query($query_secuencia) or die($mysqli->error);


//ACTUALIZAMOS LOS VALORES
$update = "UPDATE secuencia_facturacion 
	SET 
		cai = '$cai', 
		prefijo = '$prefijo', 
		relleno = '$relleno', 
		incremento = '$incremento', 
		siguiente = '$siguiente', 
		rango_inicial = '$rango_inicial',
		rango_final = '$rango_final', 
		fecha_activacion = '$fecha_activacion', 
		fecha_limite = '$fecha_limite', 
		comentario = '$comentario',  		
		activo = '$estado' 
	WHERE secuencia_facturacion_id = '$secuencia_facturacion_id'";
$query = $mysqli->query($update) or die($mysqli->error);

if($query){
	echo 1;//REGISTRO MODIFICADO CORRECTAMENTE
	
	/*********************************************************************************************************************************************************************/
	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Modificar";
	$observacion_historial = "Se ha modificado el numero a la secuencia de facturacion con el prefijo: $prefijo y rangos desde $rango_inicial a $rango_final, numero anterior: $numero_anterior, numero nuevo: $siguiente";
	$modulo = "Secuencia Facturación";
	$insert = "INSERT INTO historial 
	   VALUES('$historial_numero','0','0','$modulo','$secuencia_facturacion_id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
	$mysqli->query($insert) or die($mysqli->error);
	/*********************************************************************************************************************************************************************/		
}else{
	echo 2;//ERROR AL ALMACENAR ESTE REGISTRO
}	
$mysqli->close();//CERRAR CONEXIÓN
?>