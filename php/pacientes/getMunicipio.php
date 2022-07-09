<?php
session_start();   
include('../funtions.php');
	
//CONEXION A DB
$mysqli = connect_mysqli();

$departamento_id = $_POST['departamento_id'];

$query = "SELECT municipio_id, nombre 
    FROM municipios 
	WHERE departamento_id = '$departamento_id'
	ORDER BY nombre";
$result = $mysqli->query($query);	    
  
if($result->num_rows>0){
	echo '<option value="">Seleccione</option>';
	while($consulta2 = $result->fetch_assoc()){
	     echo '<option value="'.$consulta2['municipio_id'].'">'.$consulta2['nombre'].'</option>';
	}
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>