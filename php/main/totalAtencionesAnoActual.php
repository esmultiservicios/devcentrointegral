<?php
	include('../funtions.php');
	session_start(); 
		
	//CONEXION A DB
	$mysqli = connect_mysqli();

	date_default_timezone_set('America/Tegucigalpa');
	$año_actual = date("Y");
	
	$query = "SELECT MONTHNAME(fecha) as 'mes', COUNT(*) as 'total' 
		FROM atenciones_medicas 
		WHERE YEAR(fecha) = '$año_actual'
		GROUP BY MONTH(fecha)";
	$result = $mysqli->query($query);

	$arreglo = array();

	while( $row = $result->fetch_assoc()){
	  $arreglo[] = $row;  
	}	

	echo json_encode($arreglo);
	
	$result->free();//LIMPIAR RESULTADO
	$mysqli->close();//CERRAR CONEXIÓN	
?>