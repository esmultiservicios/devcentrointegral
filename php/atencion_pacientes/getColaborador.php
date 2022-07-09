<?php
session_start();   
include "../funtions.php";

$colaborador_id = $_SESSION['colaborador_id'];

echo $colaborador_id;
?>