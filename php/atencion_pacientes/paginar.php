<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$dato = $_POST['dato'];
$estado = $_POST['estado'];
	
//CONSULTAR PUESTO_ID	
$consultar_puesto = "SELECT puesto_id 
	FROM colaboradores 
	WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_puesto) or die($mysqli->error);

$consultar_puesto2 = $result->fetch_assoc();

$puesto_id = "";

if($result->num_rows>0){
	$puesto_id = $consultar_puesto2['puesto_id'];
}

$where = "WHERE CAST(a.fecha_cita AS DATE) BETWEEN '$fechai' AND '$fechaf' AND a.status = '$estado' AND a.colaborador_id = '$colaborador_id' AND a.preclinica = 1 AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.identidad LIKE '$dato%' OR p.apellido LIKE '$dato%')";


$query = "SELECT p.pacientes_id AS 'pacientes_id',  a.agenda_id AS 'agenda_id', p.identidad AS 'identidad', CONCAT(p.apellido,' ',p.nombre) AS 'paciente', DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', 
    a.hora AS 'hora', a.paciente AS 'tipo_paciente', p.telefono1 AS 'telefono', CONCAT(c.apellido,' ',c.nombre) AS 'colaborador', s.nombre AS 'servicio', 
	a.observacion AS 'observacion', a.comentario AS 'comentario',
   (CASE WHEN a.status = '1' THEN 'Atendido' ELSE 'Pendiente' END) AS 'estatus', CAST(a.fecha_cita AS DATE) AS 'fecha'
	FROM agenda AS a
	INNER JOIN pacientes AS p
	ON a.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s
	ON a.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id
	".$where."
	ORDER BY a.hora, a.pacientes_id ASC";

$result = $mysqli->query($query) or die($mysqli->error);

$nroLotes = 25;
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

$registro = "SELECT p.pacientes_id AS 'pacientes_id', a.agenda_id AS 'agenda_id', p.identidad AS 'identidad', CONCAT(p.apellido,' ',p.nombre) AS 'paciente', p.telefono1 AS 'telefono', 
    DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', a.hora AS 'hora', a.paciente AS 'tipo_paciente', CONCAT(c.apellido,' ',c.nombre) AS 'colaborador', 
	s.nombre AS 'servicio', a.observacion AS 'observacion', a.comentario AS 'comentario',
	(CASE WHEN a.status = '1' THEN 'Atendido' ELSE 'Pendiente' END) AS 'estatus', CAST(a.fecha_cita AS DATE) AS 'fecha'
	FROM agenda AS a
	INNER JOIN pacientes AS p
	ON a.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s
	ON a.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id	  
	".$where."
	ORDER BY a.hora, a.pacientes_id ASC
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro) or die($mysqli->error);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="1.33%">No.</th>
			<th width="10.33%">Identidad</th>				
			<th width="22.33%">Nombre</th>
			<th width="6.33%">Fecha</th>
			<th width="6.33%">Hora</th>
			<th width="4.33%">Paciente</th>
			<th width="10.33%">Servicio</th>
			<th width="4.33%">Teléfono</th>
			<th width="13.33%">Observación</th>
			<th width="13.33%">Comentario</th>
			<th width="4.33%">Estado</th>
			<th width="3.33%">Opciones</th>
			</tr>';
$i = 1;				
while($registro2 = $result->fetch_assoc()){		  
  $telefonousuario = '<a style="text-decoration:none" title = "Teléfono Usuario" href="tel:9'.$registro2['telefono'].'">'.$registro2['telefono'].'</a>'; 
  
	$tabla = $tabla.'<tr>
			<td>'.$i.'</td> 
			<td>'.$registro2['identidad'].'</td>	
			<td>'.$registro2['paciente'].'</td>	
			<td>'.$registro2['fecha_cita'].'</td>
			<td>'.date('g:i a',strtotime($registro2['hora'])).'</td>
			<td>'.$registro2['tipo_paciente'].'</td>
			<td>'.$registro2['servicio'].'</td>
			<td>'.$telefonousuario.'</td>
            <td>'.$registro2['observacion'].'</td>
            <td>'.$registro2['comentario'].'</td>
            <td>'.$registro2['estatus'].'</td>			
			<td>
			  <a style="text-decoration:none;" data-toggle="tooltip" data-placement="right" title = "Agregar Atención a Paciente" href="javascript:editarRegistro('.$registro2['pacientes_id'].','.$registro2['agenda_id'].');void(0);" class="fas fa-book-medical fa-lg"></a>			  			  
			  <a style="text-decoration:none;" data-toggle="tooltip" data-placement="right" title = "Marcar Ausencia" href="javascript:nosePresentoRegistro('.$registro2['pacientes_id'].','.$registro2['agenda_id'].','.$registro2['fecha'].');void(0);" class="fas fa-times-circle fa-lg"></a> 
			</td>
			</tr>';	
			$i++;				
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="12" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="12"><b><p ALIGN="center">Total de Registros Encontrados: '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>