<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$empresa_id = $_POST['empresa_id'];

//CONSULTAR DATOS DEL METODO DE PAGO
$query = "SELECT * FROM empresa WHERE empresa_id = '$empresa_id'";
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();   
     
$empresa = "";
$cai = "";
$rtn = "";
$direccion = "";
$otra_informacion = "";
$eslogan = "";
$celular = "";
$horario = "";
$facebook = "";
$sitioweb = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$empresa = $consulta_registro['nombre'];
	$rtn = $consulta_registro['rtn'];
	$telefono = $consulta_registro['telefono'];
	$correo = $consulta_registro['correo'];	
	$direccion = $consulta_registro['ubicacion'];	
	$otra_informacion = $consulta_registro['otra_informacion'];
	$eslogan = $consulta_registro['eslogan'];	
	$celular = $consulta_registro['celular'];
	$horario = $consulta_registro['horario'];	
	$facebook = $consulta_registro['facebook'];
	$sitioweb = $consulta_registro['sitioweb'];		
}
	
$datos = array(
	 0 => $empresa, 
	 1 => $rtn,
	 2 => $telefono,
	 3 => $correo,	 
	 4 => $direccion,
	 5 => $otra_informacion,
	 6 => $eslogan,	 
	 7 => $celular,
	 8 => $horario,
	 9 => $facebook,
	 10 => $sitioweb	 
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>