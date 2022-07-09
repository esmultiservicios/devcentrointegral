<?php
	include('../funtions.php');
	session_start(); 
		
	//CONEXION A DB
	$mysqli = connect_mysqli();

	date_default_timezone_set('America/Tegucigalpa');
	$fechaactual = date('Y-m-d');
	$nuevafecha = strtotime ('-1 year' , strtotime($fechaactual)); //Se resta un año menos
	$año = date ('Y',$nuevafecha);
	
	$query = "SELECT MONTHNAME(fecha) as 'mes', COUNT(*) as 'total' 
		FROM atenciones_medicas 
		WHERE YEAR(fecha) = '$año'
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