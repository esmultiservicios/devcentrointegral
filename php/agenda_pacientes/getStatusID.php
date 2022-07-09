<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$query = "SELECT status_id, descripcion 
   FROM status_repro";
$result = $mysqli->query($query);

if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';
	while($consulta2 = $result->fetch_assoc()){
	    echo '<option value="'.$consulta2['status_id'].'">'.$consulta2['descripcion'].'</option>';
	}
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>