<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$consulta = "SELECT colaborador_id, CONCAT(nombre,' ',apellido) AS 'nombre' 
     FROM colaboradores O
	 RDER BY nombre"; 
$result = $mysqli->query($consulta);
  
if($result->num_rows>0){
	echo "<optgroup label='Servicios'>";	
	while($consulta2 = $result->fetch_assoc()){
	     echo '<option value="'.$consulta2['colaborador_id'].'">'.$consulta2['nombre'].'</option>';
	}	
	echo "</optgroup>";
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>