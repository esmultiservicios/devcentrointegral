<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$usuario = $_SESSION['colaborador_id'];

$consulta = "SELECT servicio_id, nombre 
   FROM servicios 
   WHERE servicio_id NOT IN(9,10)
   ORDER BY nombre"; 
$result = $mysqli->query($consulta) or die($mysqli->error);

if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';
	while($consulta2 = $result->fetch_assoc()){
	     echo '<option value="'.$consulta2['servicio_id'].'">'.$consulta2['nombre'].'</option>';
	}
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>