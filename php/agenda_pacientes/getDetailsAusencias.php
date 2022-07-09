<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$ausencia_id = $_POST['ausencia_id']; 
$lista = '';
$tabla = '';

//CONSULTAT VALORES EN LA ENTIDAD AUSENCIA
$query_ausencias = "SELECT pacientes_id, fecha, servicio_id, colaborador_id, fecha
   FROM ausencias
   WHERE ausencia_id = '$ausencia_id'";
$result_ausencias = $mysqli->query($query_ausencias) or die($mysqli->error);
$registro_ausencias = $result_ausencias->fetch_assoc(); 	

$pacientes_id = "";
$fecha_cita = "";
$servicio_id = "";
$colaborador_id = "";
$desde = "";

if($result_ausencias->num_rows>0){
	$pacientes_id = $registro_ausencias['pacientes_id'];
	$fecha_cita = $registro_ausencias['fecha'];
	$servicio_id = $registro_ausencias['servicio_id'];
	$colaborador_id = $registro_ausencias['colaborador_id'];
	$desde = $registro_ausencias['fecha'];	
}

//INICIO CONSULTA DE FECHAS PARA EVALUAR EL PRIEMR DIA DEL AÑO PARA ENERO Y EL ULTIMO DIA DEL AÑO PARA DICIEMBRE
$año=date("Y", strtotime($desde));
$año_actual = date("Y", strtotime($desde));
$mes_actual = date("m", strtotime($desde));
$dia = date("d", mktime(0,0,0, $mes_actual+1, 0, $año_actual));
    
$dia1 = date('d', mktime(0,0,0, $mes_actual, 1, $año_actual)); //PRIMER DIA DEL MES
$dia2 = date('d', mktime(0,0,0, $mes_actual, $dia, $año_actual)); // ULTIMO DIA DEL MES

$fecha_inicial = date("Y-m-d", strtotime($año_actual."-".$mes_actual."-".$dia1));
$fecha_final = date("Y-m-d", strtotime($año_actual."-".$mes_actual."-".$dia2));
//FIN CONSULTA DE FECHAS PARA EVALUAR EL PRIEMR DIA DEL AÑO PARA ENERO Y EL ULTIMO DIA DEL AÑO PARA DICIEMBRE

//INICIO CONSULTAR EL TOTAL DE CITAS PERDIDAS
$query_citas_perdidas = "SELECT COUNT(a.ausencia_id) AS 'total'
    FROM ausencias AS a
    WHERE a.pacientes_id = '$pacientes_id' AND YEAR(a.fecha) = '$año' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id'"; 
$result_citas_perdidas = $mysqli->query($query_citas_perdidas) or die($mysqli->error);
$registro_citas_perdidas = $result_citas_perdidas->fetch_assoc(); 

$registro_citas_perdidas_ = "";

if($result_citas_perdidas->num_rows>0){
	$registro_citas_perdidas_ = $registro_citas_perdidas['total'];
}	           	  
//FIN ONSULTAR EL TOTAL DE CITAS PERDIDASO
	   
//INICIO CONSULTAR EL TOTAL DE CITAS REPROGRAMADAS 
$query_citas_reprogramadas = "SELECT COUNT(a.agenda_id) AS 'total'
    FROM agenda AS a
    WHERE a.pacientes_id = '$pacientes_id' AND YEAR(a.fecha_cita) = '$año' AND a.reprogramo = 1 AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id'"; 
$result_citas_reprogramadas = $mysqli->query($query_citas_reprogramadas) or die($mysqli->error);
$registro_citas_reprogramadas = $result_citas_reprogramadas->fetch_assoc(); 

$registro_citas_reprogramadas_ = "";

if($result_citas_reprogramadas->num_rows>0){
	$registro_citas_reprogramadas_ = $registro_citas_reprogramadas['total'];
}		   
//FIN CONSULTAR EL TOTAL DE CITAS REPGORAMADAS
	   
//INCIO CONSULTAR FECHA ULTIMA REPROGRAMACION DE CITAS
$query_utltima_reprogramacion = "SELECT DATE_FORMAT(fecha_cita, '%d/%m/%Y') AS 'fecha_cita'
    FROM agenda AS a
    WHERE pacientes_id = '$pacientes_id' AND YEAR(a.fecha_cita) = '$año' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id' AND a.reprogramo = 1
    ORDER BY a.expediente DESC";
$result_utltima_reprogramacion = $mysqli->query($query_utltima_reprogramacion) or die($mysqli->error);
$registro_utltima_reprogramacion = $result_utltima_reprogramacion->fetch_assoc();

$registro_ultima_cita_repgoramada_ = "";

if($result_utltima_reprogramacion->num_rows>0){
	$registro_ultima_cita_repgoramada_ = $registro_utltima_reprogramacion['fecha_cita'];		
}	
//FIN CONSULTAR FECHA ULTIMA REPROGROGRAMACION DE CITAS	

//INICIO CONSULTAR NUEVA FECHA DE CITA PARA EL USUARIO
$query_fecha_cita = "SELECT DATE_FORMAT(fecha_cita, '%d/%m/%Y') AS 'fecha_cita', hora
    FROM agenda
    WHERE pacientes_id = '$pacientes_id' AND YEAR(fecha_cita) = '$año' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id' AND status = 0";
$result_fecha_cita = $mysqli->query($query_fecha_cita) or die($mysqli->error);
$registro_fecha_cita = $result_fecha_cita->fetch_assoc();

$fecha_cita_ = "";
$registro_hora_cita = "";

if($result_fecha_cita->num_rows>0){
	$fecha_cita_ = $registro_fecha_cita['fecha_cita'];
	$registro_hora_cita = $registro_fecha_cita['hora'];	
}	
//FIN CONSULTAR NUEVA FECHA DE CITA PARA EL USUARIO

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
	<tr>
	    <th width="20%">Fecha Nueva Cita</th>
		<th width="20%">Hora</th>
		<th width="20%">Citas Perdidas</th>
		<th width="20%">Citas Reprogramadas</th>				
        <th width="20%">Fecha Ultima Cita</th>			
    </tr>';	
	
	$tabla = $tabla.'<tr>
        <td>'.$fecha_cita_.'</td>	
        <td>'.$registro_hora_cita.'</td>	
		<td>'.$registro_citas_perdidas_ .'</td>
       	<td>'.$registro_citas_reprogramadas_.'</td>		   
		<td>'.$registro_ultima_cita_repgoramada_.'</td>	   
	</tr>';		

$array = array(0 => $tabla,
    		   1 => $lista
	    );

echo json_encode($array);

$result_ausencias->free();//LIMPIAR RESULTADO
$result_citas_perdidas->free();//LIMPIAR RESULTADO
$result_citas_reprogramadas->free();//LIMPIAR RESULTADO
$result_utltima_reprogramacion->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN 
?>