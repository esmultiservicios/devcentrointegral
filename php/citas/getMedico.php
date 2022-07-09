<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT c.colaborador_id AS 'colaborador_id', c.nombre AS nombre, c.apellido AS 'apellido'
              FROM jornada_colaboradores AS jc
              INNER JOIN colaboradores AS c
              ON jc.colaborador_id = c.colaborador_id
              INNER JOIN puesto_colaboradores AS pc
              ON c.puesto_id = pc.puesto_id
              INNER JOIN users AS u
              ON jc.colaborador_id = u.colaborador_id
              WHERE u.estatus = 1";
$result = $mysqli->query($consulta);			  

if($result->num_rows>0){
	echo '<option value="">Profesional</option>';
	while($consulta2 = $result->fetch_assoc()){
		$nombre_ = explode(" ", $consulta2['nombre']);
		$apellido_ = explode(" ", $consulta2['apellido']);
		$colaborador = $nombre_[0]." ".$apellido_[0];
		
		
		echo '<option value="'.$consulta2['colaborador_id'].'">'.$colaborador.'</option>';
	}
}else{
	echo '<option value="">Profesional</option>';
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>


               
			   
               