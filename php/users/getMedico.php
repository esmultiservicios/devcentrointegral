<?php 
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT colaborador_id, nombre AS nombre, apellido AS 'apellido'
              FROM colaboradores
              WHERE estatus = 1";
$result = $mysqli->query($consulta);			  

if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';
	while($consulta2 = $result->fetch_assoc()){
		$nombre_ = explode(" ", $consulta2['nombre']);
		$apellido_ = explode(" ", $consulta2['apellido']);
		$colaborador = $nombre_[0]." ".$apellido_[0];
		
		
		echo '<option value="'.$consulta2['colaborador_id'].'">'.$colaborador.'</option>';
	}
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>


               
			   
               