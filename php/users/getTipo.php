<?php
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();
$type = $_SESSION['type'];

if($type == 1){
	$consulta = "SELECT tipo_user_id, nombre 
		FROM tipo_user 
		ORDER BY nombre";
 $result = $mysqli->query($consulta);
}else{
	$consulta = "SELECT tipo_user_id, nombre 
		FROM tipo_user 
		WHERE tipo_user_id NOT IN(1)
		ORDER BY nombre";
 $result = $mysqli->query($consulta);
}

if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['tipo_user_id'].'">'.$consulta2['nombre'].'</option>';
	}
}
?>