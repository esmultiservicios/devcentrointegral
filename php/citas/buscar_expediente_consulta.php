<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$expediente = $_POST['expediente'];

$consultar_paciente = "SELECT pacientes_id, tipo 
    FROM pacientes 
	WHERE expediente = '$expediente' OR identidad = '$expediente' AND tipo = 1";
$result = $mysqli->query($consultar_paciente);	
$consultar_paciente2 = $result->fetch_assoc();
$pacientes_id = $consultar_paciente2['pacientes_id'];
$tipo = $consultar_paciente2['tipo'];

//OBTENEMOS LOS VALORES DEL REGISTRO

//CONSULTA EN LA ENTIDAD CORPORACION
$valores = "SELECT CONCAT(nombre,' ',apellido) AS 'nombre', departamento_id, municipio_id, localidad
     FROM pacientes
     WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($valores);	 

$valores2 = $result->fetch_assoc();
$fecha = date('Y-m-d');

if($result->num_rows>0){
  if($tipo == 1){
	$datos = array(
				0 => $valores2['nombre'],  	
                1 => $valores2['departamento_id'],					
                2 => $valores2['municipio_id'],	
				3 => $valores2['localidad'],	
	);	  
  }else{
	   $datos = array(
				0 => 'Familiar'
	    );		
  }  
}else{
	$datos = array(
				0 => 'Error'
	);	
}				

echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>