<?php
session_start();   
include "../funtions.php";

$fecha = $_POST['fecha'];
$fecha_start = date("Y-m-d", strtotime($fecha));

switch (date('w', strtotime($fecha))){ 
    case 0: $dia_nombre = "Domingo"; break; 
    case 1: $dia_nombre = "Lunes"; break; 
    case 2: $dia_nombre = "Martes"; break; 
    case 3: $dia_nombre = "Miercoles"; break; 
    case 4: $dia_nombre = "Jueves"; break; 
    case 5: $dia_nombre = "Viernes"; break; 
    case 6: $dia_nombre = "Sabado"; break; 
} 

echo $dia_nombre;
?>