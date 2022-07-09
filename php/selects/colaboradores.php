<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id'];
$unidad = "";
$servicio = "";
$servicio_name= "";

if ($id=="psiquiatras"){
	$unidad = 2;
	$servicio = 1;
	$servicio_name = "Psiquiatras";
}else if ($id=="psicologos"){
	$unidad = 1;
	$servicio = 1;
	$servicio_name = "Psicólogos";
}else if ($id=="medgeneralsm"){
	$unidad = 4;
	$servicio = 3;
	$servicio_name = "Médico General";	
}else if ($id=="psiquiatrasm"){
	$unidad = 2;
	$servicio = 3;
	$servicio_name = "Psiquiatras";
}else if ($id=="psicologosm"){
	$unidad = 1;
	$servicio = 3;
	$servicio_name = "Psicólogos";	
}else if ($id=="medgenerals"){
	$unidad = 4;
	$servicio = 3;
	$servicio_name = "Médico General";	
}else if ($id=="psiquiatrass"){
	$unidad = 2;
	$servicio = 3;
	$servicio_name = "Psiquiatras";
}else if ($id=="psicologoss"){
	$unidad = 1;
	$servicio = 3;
	$servicio_name = "Psicólogos";	
}

$consulta = "SELECT c.colaborador_id AS colaborador_id, CONCAT(c.nombre,' ',c.apellido) AS 'nombre'
       FROM colaboradores AS c
       INNER JOIN users AS u
       ON c.colaborador_id = u.colaborador_id
       WHERE c.puesto_id = '$unidad' AND u.estatus = 1
	   ORDER BY c.nombre"; 
$result = $mysqli->query($consulta);	   
  
if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['colaborador_id'].'">'.$consulta2['nombre'].'</option>';
	}
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>