<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$fecha = $_POST['fecha'];
$fecha_registro = date('Y-m-d',strtotime($fecha));
$colaborador_id = $_POST['colaborador_id'];

//CONSULTA COMENTARIO
$consulta_comentario = "SELECT comentario 
    FROM ausencia_medicos 
	WHERE CAST(fecha_ausencia AS DATE) = '$fecha_registro' AND colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta_comentario);
$consulta_comentario1 =  $result->fetch_assoc();

if ($consulta_comentario1['comentario'] == ""){
	$observacion = "";
}else{
	$observacion = "Comentario: ".$consulta_comentario1['comentario'];
}

echo $observacion;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>