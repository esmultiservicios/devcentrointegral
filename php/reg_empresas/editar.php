<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$fact_empresas_id = $_POST['fact_empresas_id'];

//CONSULTAR DATOS DEL METODO DE PAGO
$query = "SELECT * FROM fact_empresas WHERE fact_empresas_id = '$fact_empresas_id'";
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();   
     
$empresa = "";
$rtn = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$empresa = $consulta_registro['nombre'];
	$rtn = $consulta_registro['rtn'];	
}
	
$datos = array(
	 0 => $empresa, 
	 1 => $rtn,	 
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>