<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$secuencia_facturacion_id = $_POST['secuencia_facturacion_id'];

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

//VERIFICAMOS QUE SOLO EXISTA UN REGISTRO DE ADMINISTRADOR DE SECUENCIAS PARA LA FACTURACION
$query = "SELECT secuencia_facturacion_id
    FROM secuencia_facturacion
	WHERE activo = 1 AND empresa_id = '$empresa'";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows==0){
	//ALMACENAMOS EL ADMINISTRADOR DE SECUENCIAS PARA LA FACTURACION
	$correlativo = correlativo('secuencia_facturacion_id ', 'secuencia_facturacion');
	$rango_inicial =  str_pad($rango_inicial, $relleno, "0", STR_PAD_LEFT);
	$rango_final =  str_pad($rango_final, $relleno, "0", STR_PAD_LEFT);
	
	$insert = "INSERT INTO secuencia_facturacion 
		VALUES('$correlativo','$empresa','$cai','$prefijo','$relleno','$incremento','$siguiente','$rango_inicial','$rango_final','$fecha_activacion','$fecha_limite','$comentario','$estado','$usuario','$fecha_registro')";
	$query = $mysqli->query($insert) or die($mysqli->error);
	
	if($query){
		echo 1;//REGISTRO ALMACENADO CORRECTAMENTE
		
		/*********************************************************************************************************************************************************************/
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Agregar";
		$observacion_historial = "Se ha agregado una nueva secuencia de facturación con el prefijo: $prefijo y rangos desde $rango_inicial a $rango_final";
		$modulo = "Secuencia Facturación";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','0','0','$modulo','$correlativo','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
		$mysqli->query($insert) or die($mysqli->error);
		/*********************************************************************************************************************************************************************/		
	}else{
		echo 2;//ERROR AL ALMACENAR ESTE REGISTRO
	}
	
}else{
	echo 3;//EXISTE UN ADMINISTRADOR DE SECUENCIAS ALMACENADO PARA LA FACTURACION
}

$mysqli->close();//CERRAR CONEXIÓN
?>