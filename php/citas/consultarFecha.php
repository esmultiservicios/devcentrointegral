<?php
session_start();   
include "../funtions.php";

$fecha = $_POST['fecha'];

$fecha_ = date("w", strtotime($fecha));

echo $fecha_;
?>