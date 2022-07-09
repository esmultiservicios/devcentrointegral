<?php
session_start();   
include "../funtions.php";

header("Content-Type: text/html;charset=utf-8");

//CONEXION A DB
$mysqli = connect_mysqli();	

$html = '';
$key = $_POST['key'];

$query = 'SELECT * 
   FROM pacientes 
   WHERE nombre LIKE "'.strip_tags($key).'%"
   GROUP BY nombre
   ORDER BY nombre DESC LIMIT 0,5';
$result = $mysqli->query($query);

if ($result->num_rows>0) {
    while ($row = $result->fetch_assoc()) {                
        $html .= '<div><a style="text-decoration:none;" class="suggest-element" data="'.utf8_encode($row['nombre']).'" id="product'.$row['pacientes_id'].'">'.utf8_encode($row['nombre']).'</a></div>';
    }
}
echo $html;
?>