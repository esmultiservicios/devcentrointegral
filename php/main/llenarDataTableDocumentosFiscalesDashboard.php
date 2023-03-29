<?php	
	session_start();   
	include "../funtions.php";

	//CONEXION A DB
	$mysqli = connect_mysqli(); 

	//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
	$consulta = "SELECT sf.secuencia_facturacion_id AS 'secuencia_facturacion_id', sf.cai AS 'cai', sf.prefijo AS 'prefijo', sf.relleno AS 'relleno', sf.incremento AS 'incremento', sf.siguiente AS 'siguiente', sf.rango_inicial AS 'rango_inicial', sf.rango_final AS 'rango_final', DATE_FORMAT(sf.fecha_activacion, '%d/%m/%Y') AS 'fecha_activacion', DATE_FORMAT(sf.fecha_registro, '%d/%m/%Y') AS 'fecha_registro', e.nombre AS 'empresa', DATE_FORMAT(sf.fecha_limite, '%d/%m/%Y') AS 'fecha_limite', d.nombre AS 'documento'
	FROM secuencia_facturacion AS sf
	INNER JOIN empresa AS e
	ON sf.empresa_id = e.empresa_id
	INNER JOIN documento as d
	ON sf.documento_id = d.documento_id
	WHERE sf.activo = 1
	ORDER BY sf.fecha_registro";

	$result = $mysqli->query($consulta);	

	$arreglo = array();
	$data = array();
	
	while($row = $result->fetch_assoc()){				
		$data[] = array( 
			"secuencia_facturacion_id"=>$row['secuencia_facturacion_id'],
			"empresa"=>$row['empresa'],
			"documento"=>$row['documento'],
			"cai"=>$row['cai'],
			"inicio"=>$row['rango_inicial'],
			"fin"=>$row['rango_final'],
			"fecha"=>$row['fecha_limite'],
			"siguiente"=>$row['siguiente']			
		);		
	}
	
	$arreglo = array(
		"echo" => 1,
		"totalrecords" => count($data),
		"totaldisplayrecords" => count($data),
		"data" => $data
	);

	echo json_encode($arreglo);
	
?>