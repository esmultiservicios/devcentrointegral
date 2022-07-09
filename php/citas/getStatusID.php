<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$consulta = "SELECT status_id, descripcion 
   FROM status_repro";
$result = $mysqli->query($consulta);

if($result->num_rows>0){
	while($consulta2 = $result->fetch_assoc()){
	    echo '<option value="'.$consulta2['status_id'].'">'.$consulta2['descripcion'].'</option>';
	}
}
?>