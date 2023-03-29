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
	
	while($data = $result->fetch_assoc()){				
		$arreglo["data"][] = $data;
	}
	 
	echo json_encode($arreglo);
	
	$result->free();//LIMPIAR RESULTADO
	$mysqli->close();//CERRAR CONEXIÓN
?>