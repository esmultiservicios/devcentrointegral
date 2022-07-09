<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 	

$paginaActual = $_POST['partida'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$dato = $_POST['dato'];
$unidad = $_POST['unidad'];
$colaborador = $_POST['colaborador'];
$fecha_registro = date('Y-m-d');

if($colaborador != ""){
	$where = "WHERE CAST(ag.fecha_cita AS DATE ) BETWEEN '$fechai' AND '$fechaf' AND ag.preclinica = 0 AND ag.colaborador_id = '$colaborador'";
}else if($dato != ""){
	$where = "WHERE ag.preclinica = 0  AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%')";
}else{
	$where = "WHERE CAST(ag.fecha_cita AS DATE ) BETWEEN '$fechai' AND '$fechaf' AND ag.preclinica = 0";
}

$query = "SELECT ag.pacientes_id AS 'pacientes_id', ag.agenda_id AS 'agenda_id', p.expediente AS 'expediente', p.identidad AS 'identidad', CONCAT(p.apellido,' ',p.nombre) AS 'paciente', DATE_FORMAT(CAST(ag.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', ag.hora AS 'hora', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', s.nombre AS 'servicio', ag.observacion AS 'observacion', ag.comentario AS 'comentario', CAST(ag.fecha_cita AS DATE) AS 'fecha', pc.puesto_id AS 'puesto_id', ag.servicio_id AS 'servicio_id', CAST(ag.fecha_cita AS DATE) AS 'fecha_cita1'
	FROM agenda AS ag
	INNER JOIN pacientes AS p
	ON ag.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
	ON ag.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON ag.servicio_id = s.servicio_id
	INNER JOIN puesto_colaboradores AS pc
	ON c.puesto_id = pc.puesto_id		
	".$where."
	ORDER BY fecha_cita1, ag.hora";
$result = $mysqli->query($query);
$nroProductos = $result->num_rows;
   
$nroLotes = 25;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.(1).');void(0);">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual-1).');void(0);">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual+1).');void(0);">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($nroPaginas).');void(0);">Ultima</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}

$registro = "SELECT ag.pacientes_id AS 'pacientes_id', ag.agenda_id AS 'agenda_id', p.expediente AS 'expediente', p.identidad AS 'identidad', CONCAT(p.apellido,' ',p.nombre) AS 'paciente', DATE_FORMAT(CAST(ag.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', ag.hora AS 'hora', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', s.nombre AS 'servicio', ag.observacion AS 'observacion', ag.comentario AS 'comentario', CAST(ag.fecha_cita AS DATE) AS 'fecha', pc.puesto_id AS 'puesto_id', ag.servicio_id AS 'servicio_id', CAST(ag.fecha_cita AS DATE) AS 'fecha_cita1'
	FROM agenda AS ag
	INNER JOIN pacientes AS p
	ON ag.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
	ON ag.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON ag.servicio_id = s.servicio_id
	INNER JOIN puesto_colaboradores AS pc
	ON c.puesto_id = pc.puesto_id		
	".$where."
	ORDER BY fecha_cita1, ag.hora ASC LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
		  <tr>
			<th width="3.33%">No.</th>
			<th width="8.33%">Expediente</th>
			<th width="8.33%">Identidad</th>
			<th width="11.33%">Nombre</th>
			<th width="5.33%">Fecha Cita</th>
			<th width="3.33%">Hora</th>
			<th width="8.33%">Profesional</th>				
			<th width="10.33%">Servicio</th>
			<th width="10.33%">Observación</th>
			<th width="10.33">Comentario</th>				
			<th width="5.33%">Opciones</th>
		   </tr>';
			
$i=1;			
while($registro2 = $result->fetch_assoc()){	   	
  if ($registro2['expediente'] == 0){
	  $expediente = "TEMP"; 
  }else{
	  $expediente = $registro2['expediente'];
  }	

	$tabla = $tabla.'<tr>
	   <td>'.$i.'</td>
	   <td>'.$expediente.'</td>	
	   <td>'.$registro2['identidad'].'</td>		   
	   <td>'.$registro2['paciente'].'</td>
	   <td>'.$registro2['fecha_cita'].'</td>
	   <td>'.$registro2['hora'].'</td>
	   <td>'.$registro2['colaborador'].'</td>		   
	   <td>'.$registro2['servicio'].'</td>
	   <td>'.$registro2['observacion'].'</td>	
	   <td>'.$registro2['comentario'].'</td>			   
	   <td>
		   <a style="text-decoration:none;" title = "Agregar Preclínica" href="javascript:editarRegistro('.$registro2['agenda_id'].','.$registro2['expediente'].');void(0);" class="fas fa-notes-medical fa-lg"></a>			   			   
		   <a style="text-decoration:none;" title = "Usuario no se presentó  a su cita" href="javascript:nosePresntoRegistro('.$registro2['agenda_id'].','.$registro2['pacientes_id'].');void(0);" class="fas fa-times-circle fa-lg"></a>
	   </td>
  </tr>';		
  $i++;
}
  
if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="13" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="13"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>