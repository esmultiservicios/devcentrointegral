<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$colaborador_id = $_POST['colaborador_id'];

//OBTENEMOS EL DESCUENTO A APLICAR SEGUN LO ESTABLECIDO POR EL PROFESIONAL
$consulta = "SELECT d.descuento_id AS 'descuento_id', d.nombre AS 'descuento'
	FROM descuento_profesional AS dp
	INNER JOIN descuento AS d
	ON dp.descuento_id = d.descuento_id
	WHERE dp.colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta) or die($mysqli->error);

if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';	
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['descuento_id'].'">'.$consulta2['descuento'].'</option>';
	}
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>