<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$agenda_id = $_POST['agenda_id'];

//CONSULTAR METODO DE PAGO
$query = "SELECT metodo_pago_id
	FROM pacientes_id
	WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($query);
$metodo_pago_id = "";

if($result->num_rows>=0){
	 $consulta = $result->fetch_assoc();
	 $metodo_pago_id = $consulta['consulta'];
}

$datos = array(
	0 => $metodo_pago_id,
);
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>              