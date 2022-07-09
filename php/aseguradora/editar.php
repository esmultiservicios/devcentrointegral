<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$aseguradora_id = $_POST['aseguradora_id'];

//CONSULTAR DATOS DEL METODO DE PAGO
$query = "SELECT * FROM aseguradora WHERE aseguradora_id = '$aseguradora_id'";
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();   
     
$aseguradora = "";
$rtn = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$aseguradora = $consulta_registro['nombre'];
	$rtn = $consulta_registro['rtn'];	
}
	
$datos = array(
	 0 => $aseguradora, 
	 1 => $rtn,	 
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>