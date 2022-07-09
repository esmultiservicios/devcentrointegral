<?php
session_start();   
include "../funtions.php";

header( 'Content-type: text/html; charset=iso-8859-1' );
	
//CONEXION A DB
$mysqli = connect_mysqli();

$search = $_POST['expediente'];

$query_services = "SELECT pacientes_id, CONCAT(nombre,' ',apellido) AS 'nombre' 
     FROM pacientes 
	 WHERE CONCAT(nombre,' ',apellido) like '" . $search . "%' ORDER BY  CONCAT(nombre,' ',apellido) DESC";
$result = $mysqli->query($query_services);

while ($row_services = $result->fetch_assoc()) {
    echo '<div class="suggest-element"><a data="'.$row_services['nombre'].'" id="service'.$row_services['service_id'].'">'.utf8_encode($row_services['nombre']).'</a></div>';
}
?>