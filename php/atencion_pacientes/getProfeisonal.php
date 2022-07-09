<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];

//CONSULTAR NOMBRE PROFESIONAL
$query = "SELECT CONCAT(nombre, ' ', apellido) AS 'nombre'
	FROM colaboradores
	WHERE colaborador_id = '$colaborador_id'"; 
$result = $mysqli->query($query) or die($mysqli->error);
$consulta2 = $result->fetch_assoc();

$colaborador = '';

if($result->num_rows>0){
	 $colaborador = $consulta2['nombre'];
}	
	
echo $colaborador;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>