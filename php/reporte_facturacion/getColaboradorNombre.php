<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_POST['colaborador_id'];

$query = "SELECT CONCAT(nombre,' ',apellido) 'colaborador'
	FROM colaboradores 
	WHERE colaborador_id = '$colaborador_id'";

$result = $mysqli->query($query) or die($mysqli->error);	
$consulta2 = $result->fetch_assoc();	
$profesional = $consulta2['colaborador'];
	  
echo $profesional;  


$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>


               
			   
               