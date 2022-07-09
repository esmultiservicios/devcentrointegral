<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

//OBTENEMOS EL DESCUENTO A APLICAR SEGUN LO ESTABLECIDO POR EL PROFESIONAL
$query = "SELECT c.colaborador_id, CONCAT(c.nombre,' ',c.apellido) AS 'profesional'
	FROM  jornada_colaboradores as jc
	INNER JOIN colaboradores AS c
	ON jc.colaborador_id = c.colaborador_id";
$result = $mysqli->query($query) or die($mysqli->error);

if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['colaborador_id'].'">'.$consulta2['profesional'].'</option>';
	}
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>