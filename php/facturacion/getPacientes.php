<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$consulta = "SELECT pacientes_id, CONCAT(nombre, ' ', apellido) AS 'paciente' 
    FROM pacientes
	WHERE estado = 1 AND expediente > 0";
$result = $mysqli->query($consulta) or die($mysqli->error);

if($result->num_rows>0){
    echo "<optgroup label='Pacientes'>";	
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['pacientes_id'].'">'.$consulta2['paciente'].'</option>';
	}
	echo "</optgroup>";	
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>