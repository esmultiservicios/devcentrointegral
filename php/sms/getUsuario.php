<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$query = "SELECT c.nombre AS 'nombre', c.apellido AS 'apellido', c.colaborador_id AS 'colaborador_id'
   FROM sms AS s
   INNER JOIN colaboradores AS c
   ON s.user = c.colaborador_id
   GROUP BY s.user
   ORDER BY s.user";
$result = $mysqli->query($query);   
   
if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';
	while($consulta2 = $result->fetch_assoc()){
		$nombre_ = explode(" ", $consulta2['nombre']);
        $nombre_usuario = $nombre_[0];
	    $apellido_ = explode(" ", $consulta2['apellido']);
		$nombre_apellido = $apellido_[0];
		$nombre_completo = $nombre_usuario." ".$nombre_apellido;
		echo '<option value="'.$consulta2['colaborador_id'].'">'.$nombre_completo.'</option>';
	}
}
?>