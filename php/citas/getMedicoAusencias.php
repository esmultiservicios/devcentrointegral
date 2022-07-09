<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$puesto_id = $_POST['puesto_id'];

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT c.colaborador_id AS 'colaborador_id', CONCAT(c.nombre,' ',c.apellido) 'colaborador'
              FROM jornada_colaboradores AS jc
              INNER JOIN colaboradores AS c
              ON jc.colaborador_id = c.colaborador_id
              INNER JOIN puesto_colaboradores AS pc
              ON c.puesto_id = pc.puesto_id
              INNER JOIN users AS u
              ON jc.colaborador_id = u.colaborador_id
              WHERE c.puesto_id = '$puesto_id' AND u.estatus = 1
			  GROUP BY c.colaborador_id";
$result = $mysqli->query($consulta);			  
			  

if($result->num_rows>0){
    echo "<optgroup label='Profesional'>";	
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['colaborador_id'].'">'.$consulta2['colaborador'].'</option>';
	}
	echo "</optgroup>";	
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>


               
			   
               