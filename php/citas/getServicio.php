<?php  
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT * FROM servicios"; 
$result = $mysqli->query($consulta); 

if($result->num_rows>0){
	echo '<option value="">Servicio</option>';	
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['servicio_id'].'">'.$consulta2['nombre'].'</option>';
	}
}else{
	echo '<option value="">Servicio</option>';	
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>