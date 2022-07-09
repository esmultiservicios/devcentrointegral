<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$id = $_POST['id'];

//OBTENEMOS LOS VALORES DEL REGISTRO

//CONSULTA EN LA ENTIDAD AGGENDA
$valores = "SELECT p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', CONCAT(c.nombre,' ',c.apellido) AS 'profesional', CAST(a.fecha_cita AS DATE) AS 'fecha_cita'
    FROM agenda AS a
    INNER JOIN pacientes AS p
    ON a.pacientes_id = p.pacientes_id
    INNER JOIN colaboradores AS c
    ON a.colaborador_id = c.colaborador_id	
    WHERE a.agenda_id = '$id'";
$result = $mysqli->query($valores);	

$valores2 = $result->fetch_assoc();

$datos = array(
				0 => $valores2['paciente'], 
				1 => $valores2['identidad'],
                2 => $valores2['expediente'], 	
                3 => $valores2['profesional'],
                4 => $valores2['fecha_cita'],				
				);
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>