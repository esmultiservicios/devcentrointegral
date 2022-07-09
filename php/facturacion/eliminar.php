<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$facturas_id = $_POST['facturas_id'];
$usuario = $_SESSION['colaborador_id'];

//ELIMINAMOS EL DETALLE DE LA FACTURA
$delete_detalle = "DELETE FROM facturas_detalle WHERE facturas_id = '$facturas_id' AND estado = 1";
$mysqli->query($delete_detalle);

//ELIIMINAMOS LA FACTURA
$delete_factura = "DELETE FROM facturas WHERE facturas_id = '$facturas_id'";
$query = $mysqli->query($delete_factura);

if($query){
	echo 1;//REGISTRO ELIMINADO CORRECTAMENTE
}else{
	echo 2;//NO SE PUEDO ELIMINAR EL REGISTRO
}
 
$mysqli->close();//CERRAR CONEXIÓN   
?>