<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$expediente_valor = $_POST['expediente'];

$consultar_expediente = "SELECT expediente
    FROM pacientes 
	WHERE expediente = '$expediente_valor' OR identidad = '$expediente_valor'";
$result = $mysqli->query($consultar_expediente);	
$consultar_expediente2 = $result->fetch_assoc();
$expediente = $consultar_expediente2['expediente'];

//OBTENEMOS LOS VALORES DEL REGISTRO

//CONSULTA EN LA ENTIDAD CORPORACION
$valores = "SELECT identidad, CONCAT(nombre,' ',apellido) AS 'nombre'
     FROM pacientes
     WHERE expediente = '$expediente' OR identidad = '$expediente'";
$result = $mysqli->query($valores);	 

$valores2 = $result->fetch_assoc();
$fecha = date('Y-m-d');

if($expediente != 0){
   if($result->num_rows>0){
	   $datos = array(
				0 => $valores2['identidad'],  	
                1 => $valores2['nombre'],	
	   );
   }else{
	   $datos = array(
				0 => 'Error', 
				1 => '', 
 				2 => '',
	    );	
   }
}else{
	   $datos = array(
				0 => 'Error1', 
				1 => '', 
 				2 => '',
	    );		
}

echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>