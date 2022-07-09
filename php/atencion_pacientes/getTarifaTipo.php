<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$usuario = $_SESSION['colaborador_id'];

$consulta = "SELECT t.tarifas_tipo_id AS 'tarifas_tipo_id', tp.nombre AS 'nombre'
	FROM tarifas AS t
	INNER JOIN tarifas_tipo AS tp
	ON t.tarifas_tipo_id = tp.tarifas_tipo_id
	WHERE t.colaborador_id = '$usuario'";
$result = $mysqli->query($consulta);			  

if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['tarifas_tipo_id'].'">'.$consulta2['nombre'].'</option>';
	}
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>


               
			   
               