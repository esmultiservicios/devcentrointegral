<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$facturas_detalle_id = $_POST['facturas_detalle_id'];

//ELIMINAMOS EL DETALLE DE LA FACTURA
$delete_detalle = "DELETE FROM facturas_detalle WHERE facturas_detalle_id = '$facturas_detalle_id'";
$mysqli->query($delete_detalle);

if($query){
	echo 1;//REGISTRO ELIMINADO CORRECTAMENTE
}else{
	echo 2;//NO SE PUEDO ELIMINAR EL REGISTRO
}
 
$mysqli->close();//CERRAR CONEXIÓN   
?>