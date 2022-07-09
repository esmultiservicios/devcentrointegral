<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$paginaActual = $_POST['partida'];
$servicio = $_POST['servicio'];
$medico_general = $_POST['medico_general'];
$status = $_POST['atencion'];
$dato = $_POST['dato'];
$fecha = $_POST['fecha'];
$fechaf = $_POST['fechaf'];

if($servicio != "" && $medico_general == ""){
	$where = "WHERE CAST(a.fecha_cita as date) BETWEEN '$fecha' AND '$fechaf' AND a.status = '$status' AND a.servicio_id = '$servicio' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%')";		
}else if($servicio == "" && $medico_general != ""){
	$where = "WHERE CAST(a.fecha_cita as date) BETWEEN '$fecha' AND '$fechaf' AND a.status = '$status' AND a.colaborador_id = '$medico_general' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%')";		
}else{
	$where = "WHERE CAST(a.fecha_cita as date) BETWEEN '$fecha' AND '$fechaf' AND a.status = '$status' AND a.servicio_id = '$servicio' AND a.colaborador_id = '$medico_general' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%')";	
}
	
$query = "SELECT a.servicio_id AS 'servicio_id', a.agenda_id as 'agenda_id', a.pacientes_id AS 'pacientes_id', a.expediente AS 'expediente', p.nombre AS 'nombre', p.apellido AS 'apellido', a.hora AS 'hora', DATE_FORMAT(CAST(a.fecha_cita AS DATE ), '%d/%m/%Y') AS 'fecha_cita',
   CONCAT(c.nombre, ' ', c.apellido) As doctor, p.telefono1 AS 'telefono1', p.telefono2 AS 'telefono2', c.colaborador_id AS 'colaborador_id', a.observacion as 'observacion', a.comentario as 'comentario', CONCAT(c1.nombre, ' ', c1.apellido) As usuario, p.identidad AS 'identidad', a.expediente AS 'expediente', a.servicio_id AS 'servicio_id', CAST(a.fecha_cita AS DATE) AS 'fecha_cita_consulta', c.puesto_id AS 'puesto_id', CAST(a.fecha_cita AS DATE) AS 'fecha', a.servicio_id AS 'servicio_id', CONCAT(p.nombre, ' ', p.apellido) As 'paciente'
   FROM agenda AS a 
   INNER JOIN pacientes AS p 
   ON a.pacientes_id = p.pacientes_id 
   INNER JOIN colaboradores AS c 
   ON a.colaborador_id = c.colaborador_id
   INNER JOIN colaboradores AS c1
   ON a.usuario = c1.colaborador_id	 
   ".$where."
   ORDER BY a.hora, a.pacientes_id ASC";  
$result = $mysqli->query($query);
   
$nroLotes = 15;
$nroProductos = $result->num_rows;
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

$registro = "SELECT a.servicio_id AS 'servicio_id', a.agenda_id as 'agenda_id', a.pacientes_id AS 'pacientes_id', a.expediente AS 'expediente', p.nombre AS 'nombre', p.apellido AS 'apellido', a.hora AS 'hora', DATE_FORMAT(CAST(a.fecha_cita AS DATE ), '%d/%m/%Y') AS 'fecha_cita',
   CONCAT(c.nombre, ' ', c.apellido) As doctor, p.telefono1 AS 'telefono1', p.telefono2 AS 'telefono2', c.colaborador_id AS 'colaborador_id', a.observacion as 'observacion', a.comentario as 'comentario', CONCAT(c1.nombre, ' ', c1.apellido) AS usuario, p.identidad AS 'identidad', a.expediente AS 'expediente', a.servicio_id AS 'servicio_id', CAST(a.fecha_cita AS DATE) AS 'fecha_cita_consulta', c.puesto_id AS 'puesto_id', CAST(a.fecha_cita AS DATE) AS 'fecha', a.servicio_id AS 'servicio_id', CONCAT(p.nombre, ' ', p.apellido) As 'paciente'
   FROM agenda AS a 
   INNER JOIN pacientes AS p 
   ON a.pacientes_id = p.pacientes_id 
   INNER JOIN colaboradores AS c 
   ON a.colaborador_id = c.colaborador_id
   INNER JOIN colaboradores AS c1
   ON a.usuario = c1.colaborador_id 
   ".$where."
   ORDER BY a.hora, a.pacientes_id ASC LIMIT $limit, $nroLotes";
  
$result = $mysqli->query($registro);

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
		  <tr>
			<th width="2.69%">N°</th>
			<th width="4.69%">Expediente</th>
			<th width="7.69%">Identidad</th>
			<th width="16.69%">Nombre</th>
			<th width="5.69%">Fecha Cita</th>
			<th width="5.69%">Hora</th>	
			<th width="5.69%">Usuario</th>				
			<th width="7.69%">Profesional</th>
			<th width="5.69%">Teléfono</th>
			<th width="9.69%">Observación</th>
			<th width="9.69%">Comentario</th>	
			<th width="9.69%">Usuario</th>				
			<th width="8.69%">Opciones</th>
		   </tr>';
			
$i=1;			
while($registro2 = $result->fetch_assoc()){
	$pacientes_id = $registro2['pacientes_id'];
	$expediente = $registro2['expediente'];
	$servicio_id = $registro2['servicio_id'];
	$colaborador_id = $registro2['colaborador_id'];
	$fecha_cita = $registro2['fecha_cita_consulta'];

	if($registro2['telefono1'] != ""){
	 $telefonousuario = '<a style="text-decoration:none" title = "Teléfono Usuario" href="tel:'.$registro2['telefono1'].'">'.$registro2['telefono1'].'</a>'; 
	 $telefonousuariosms = $registro2['telefono1'];
	}

	if($registro2['telefono2'] != ""){
	 $telefonousuario1 = '<a style="text-decoration:none" title = "Teléfono Usuario" href="tel:9'.$registro2['telefono2'].'">'.$registro2['telefono2'].'</a>'; 
	 $telefonousuariosms .= ", ".$registro2['telefono2'];
	}else{
	 $telefonousuario1 = '';
	}

	if ($registro2['expediente'] == 0){
	  $expediente = "TEMP"; 
	}else{
	  $expediente = $registro2['expediente'];
	}	 

	if ($registro2['observacion'] == ""){
	 $observacion = "No hay ningún observacion";
	}else{
	$observacion = $registro2['observacion'];
	}	  
	  	  
	//FIN CONSULTAR MENSAJE CONFIRMACION AGENDA

	if ($registro2['comentario'] == ""){
	 $comentario = "No hay ningún comentario";
	}else{
	$comentario = $registro2['comentario'];
	}

	//CONSULTAR PUESTO COLABORADOR			  
	$consultar_puesto = "SELECT puesto_id 
	   FROM colaboradores 
	   WHERE colaborador_id = '$colaborador_id'";
	$result_puesto_colaborador = $mysqli->query($consultar_puesto);
	$consultar_puesto1 = $result_puesto_colaborador->fetch_assoc();

	$consultar_colaborador_puesto_id = "";

	if($result_puesto_colaborador->num_rows>0){
		$consultar_colaborador_puesto_id = $consultar_puesto1['puesto_id'];
	}

	$consultar_expediente = "SELECT a.agenda_id AS 'agenda_id'
		FROM agenda AS a
		INNER JOIN colaboradores AS c
		ON a.colaborador_id = c.colaborador_id
		WHERE pacientes_id = '$pacientes_id' AND a.servicio_id = '$servicio_id' AND c.puesto_id = '$consultar_colaborador_puesto_id' AND a.status = 1";
	$result_tipo_usuario = $mysqli->query($consultar_expediente);
	$consultar_expediente1 = $result_tipo_usuario->fetch_assoc();

	$consulta_agenda_id = "";
	
	if($result_tipo_usuario->num_rows>0){
		$consulta_agenda_id = $consultar_expediente1['agenda_id'];
	}

	if($consulta_agenda_id == ""){
		$paciente = 'N';
	}else{
		$paciente = 'S';
	}

	if($expediente >=1 && $expediente <=14000){
		$paciente = 'S';
	}
  //FIN CONSULTAR MENSAJE CONFIRMACION DE AUSENCIA PARA LA REPROGRAMACIÓN
	$tabla = $tabla.'<tr>
	   <td>'.$i.'</td>
	   <td><a style="text-decoration:none" href="javascript:sendOneSMS('.$registro2['pacientes_id'].','.$registro2['agenda_id'].');">'.$expediente.'</a></td>			
	   <td title='.$registro2['usuario'].'>'.$registro2['identidad'].'</td>
	   <td>'.$registro2['paciente'].'</td>
	   <td>'.$registro2['fecha_cita'].'</td>
	   <td>'.date('g:i a',strtotime($registro2['hora'])).'</td>	
       <td>'.$paciente .'</td>	   
	   <td>'.$registro2['doctor'].'</td>
	   <td>'.$telefonousuario.'</td>	   
	   <td>'.$observacion.'</td>
	   <td>'.$comentario.'</td>		   
	   <td>'.$registro2['usuario'].'</td>	   
	   <td>   
		   <a style="text-decoration:none;" title = "Reprogramar Usuarios" href="javascript:editarRegistro('.$registro2['agenda_id'].','.$registro2['colaborador_id'].','.$registro2['pacientes_id'].','.$registro2['servicio_id'].');void(0);" class="fas fa-edit fa-lg"></a>
		   <a style="text-decoration:none;" title = "Imprimir Ticket" href="javascript:reportePDF('.$registro2['agenda_id'].');void(0);" class="fas fa-print fa-lg"></a>
		   <a style="text-decoration:none;" title = "Eliminar Cita a Usuario" href="javascript:modal_eliminar('.$registro2['agenda_id'].');void(0);" class="fas fa-trash fa-lg"></a>
		   <a style="text-decoration:none;" title = "Marcar Ausencia a Usuarios" href="javascript:nosePresentoRegistro('.$registro2['agenda_id'].','.$registro2['pacientes_id'].','.$registro2['fecha'].');void(0);" class="fas fa-times-circle fa-lg"></a>
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
	  <td colspan="13"><b><p ALIGN="center">Total de Registros Encontrados '.number_format($nroProductos).'</p></b>
   </tr>';		
}

$tabla = $tabla.'</table>';	

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>