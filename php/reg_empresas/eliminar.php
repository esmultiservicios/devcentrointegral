<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$fact_empresas_id = $_POST['fact_empresas_id'];
$comentario = cleanStringStrtolower($_POST['comentario']);
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

//VERIFICAMOS QUE LA EMPRESA NO PERTENEZCA A NINGUN REGISTRO
$query_registro = "SELECT facturas_id
 FROM facturas WHERE fact_empresas_id = '$fact_empresas_id'";
$result_registro = $mysqli->query($query_registro) or die($mysqli->error);

if($result_registro->num_rows==0){
	//ELIMINAMOS LA EMPRESA
	$delete = "DELETE FROM fact_empresas WHERE fact_empresas_id = '$fact_empresas_id'";
	$query = $mysqli->query($delete) or die($mysqli->error);
	
	if($query){
		echo 1;//REGISTRO ELIMINADO CORRECTAMENTE
	}else{
		echo 2;//ERROR AL ELIMINAR EL REGISTRO
	}
}else{
	echo 3;//ESTE REGISTRO CUENTA CON INFORMACIÓN ALMACENADA NO SE PUEDE ELIMINAR
}	
	
$mysqli->close();//CERRAR CONEXIÓN
?>