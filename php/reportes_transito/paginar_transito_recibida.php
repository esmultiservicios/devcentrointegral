<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];

$paginaActual = $_POST['partida'];
$servicio = $_POST['servicio'];
$desde = $_POST['desde'];
$hasta = $_POST['hasta'];
$dato = $_POST['dato'];	
	
$query = "SELECT tr.transito_id AS 'transito_id', DATE_FORMAT(tr.fecha, '%d/%m/%Y') As 'fecha', CONCAT(p.nombre,' ',p.apellido) AS 'nombre', p.expediente As 'expediente', p.identidad AS 'identidad', tr.edad As 'edad', (CASE WHEN p.genero = 'H' THEN 'X' ELSE '' END) AS 'h', (CASE WHEN p.genero = 'M' THEN 'X' ELSE '' END) AS 'm', (CASE WHEN tr.paciente = 'n' THEN 'X' ELSE '' END) AS 'nuevo', (CASE WHEN tr.paciente = 'S' THEN 'X' ELSE '' END) AS 'subsiguiente', d.nombre As 'departamento', m.nombre AS 'municipio', s.nombre AS 'recibidade', pc.nombre AS 'recibidade_unidad', tr.observacion AS 'observacion', CONCAT(c.nombre,' ',c.apellido) AS 'medico' 
	FROM transito_recibida AS tr 
	INNER JOIN pacientes AS p 
	ON tr.pacientes_id = p.pacientes_id 
	LEFT JOIN departamentos As d 
	ON p.departamento_id = d.departamento_id 
	LEFT JOIN municipios As m 
	ON p.municipio_id = m.municipio_id 
	INNER JOIN servicios AS s 
	ON tr.recibida_de = s.servicio_id 
	INNER JOIN colaboradores AS c 
	ON tr.colaborador_id = c.colaborador_id 
	INNER JOIN puesto_colaboradores AS pc 
	ON c.puesto_id = pc.puesto_id  		
	WHERE tr.fecha BETWEEN '$desde' AND '$hasta' AND tr.servicio_id = '$servicio' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%')
	ORDER BY tr.fecha, p.expediente ASC";
$result = $mysqli->query($query);
$nroProductos = $result->num_rows;
  
$nroLotes = 15;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_transito('.(1).');">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_transito('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_transito('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_transito('.($nroPaginas).');">Ultima</a></li>';
}	

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}

$registro = "SELECT tr.transito_id AS 'transito_id', DATE_FORMAT(tr.fecha, '%d/%m/%Y') As 'fecha', CONCAT(p.nombre,' ',p.apellido) AS 'nombre', p.expediente As 'expediente', p.identidad AS 'identidad', tr.edad As 'edad', (CASE WHEN p.genero = 'H' THEN 'X' ELSE '' END) AS 'h', (CASE WHEN p.genero = 'M' THEN 'X' ELSE '' END) AS 'm', (CASE WHEN tr.paciente = 'n' THEN 'X' ELSE '' END) AS 'nuevo', (CASE WHEN tr.paciente = 'S' THEN 'X' ELSE '' END) AS 'subsiguiente', d.nombre As 'departamento', m.nombre AS 'municipio', s.nombre AS 'recibidade', pc.nombre AS 'recibidade_unidad', tr.observacion AS 'observacion', CONCAT(c.nombre,' ',c.apellido) AS 'medico' 
	FROM transito_recibida AS tr 
	INNER JOIN pacientes AS p 
	ON tr.pacientes_id = p.pacientes_id 
	LEFT JOIN departamentos As d 
	ON p.departamento_id = d.departamento_id 
	LEFT JOIN municipios As m 
	ON p.municipio_id = m.municipio_id 
	INNER JOIN servicios AS s 
	ON tr.recibida_de = s.servicio_id 
	INNER JOIN colaboradores AS c 
	ON tr.colaborador_id = c.colaborador_id 
	INNER JOIN puesto_colaboradores AS pc 
	ON c.puesto_id = pc.puesto_id 		
	WHERE tr.fecha BETWEEN '$desde' AND '$hasta' AND tr.servicio_id = '$servicio' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%')
	ORDER BY tr.fecha, P.expediente
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="8.25%">Fecha</th>
			<th width="14.25%">Nombre</th>
			<th width="6.25%">Exp</th>
			<th width="6.25%">Identidad</th>
			<th width="2.25%">Edad</th>
			<th width="2.25%">H</th>				
			<th width="2.25%">M</th>
			<th width="2.25%">N</th>	
			<th width="2.25%">S</th>
			<th width="8.25%">Departamento</th>
			<th width="8.25%">Municipio</th>
			<th width="7.25%">Profesional</th>				
			<th width="7.25%">Recibida de</th>
			<th width="6.25%">Unidad</th>
			<th width="10.25%">Observación</th>
			<th width="6.25%">Opciones</th>					
			</tr>';			
			
while($registro2 = $result->fetch_assoc()){	
	$tabla = $tabla.'<tr>
	   <td>'.$registro2['fecha'].'</td>
	   <td>'.$registro2['nombre'].'</td>		   
	   <td>'.$registro2['expediente'].'</td>		
	   <td>'.$registro2['identidad'].'</td>
	   <td>'.$registro2['edad'].'</td>		   
	   <td>'.$registro2['h'].'</td>
	   <td>'.$registro2['m'].'</td>
	   <td>'.$registro2['nuevo'].'</td>
	   <td>'.$registro2['subsiguiente'].'</td>
	   <td>'.$registro2['departamento'].'</td>
	   <td>'.$registro2['municipio'].'</td>
	   <td>'.$registro2['medico'].'</td>
	   <td>'.$registro2['recibidade'].'</td>
	   <td>'.$registro2['recibidade_unidad'].'</td>
	   <td>'.$registro2['observacion'].'</td>
	   <td>
		   <a data-toggle="tooltip" data-placement="top" title="Eliminar Registro" href="javascript:modal_eliminarTransitoRecibida('.$registro2['transito_id'].','.$registro2['expediente'].');void(0);" class="fas fa-trash fa-lg" style="text-decoration:none;"></a>
	   </td>		   
	</tr>';	        
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="17" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="17"><b><p ALIGN="center">Total de Registros Encontrados (Recibidas) '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>