<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$colaborador_id = 5;
$paginaActual = $_POST['partida'];
$fecha = $_POST['fecha'];
$dato = $_POST['dato'];
$servicio = $_POST['servicio'];
$unidad = $_POST['unidad'];

if($servicio == "" && $unidad == "" && $dato == ""){      
   $where = "CAST(ag.fecha_cita AS DATE ) = '$fecha'";            
}else if($servicio != "" && $unidad == "" && $dato == ""){
   $where = "CAST(ag.fecha_cita AS DATE ) = '$fecha' AND s.servicio_id = '$servicio'";			
}else if($servicio != "" && $unidad != "" && $dato == ""){
   $where = "CAST(ag.fecha_cita AS DATE ) = '$fecha' AND s.servicio_id = '$servicio' AND pc.puesto_id = '$unidad'";
}else{
   $where = "CAST(ag.fecha_cita AS DATE ) = '$fecha' AND s.servicio_id = '$servicio' AND pc.puesto_id = '$unidad' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%')";
}

$query = "SELECT p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', CAST(ag.fecha_cita AS DATE) AS 'fecha_cita', ag.hora AS 'hora', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', s.nombre AS 'servicio'
	FROM agenda AS ag
	INNER JOIN pacientes AS p
	ON ag.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
	ON ag.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON ag.servicio_id = s.servicio_id
	INNER JOIN puesto_colaboradores AS pc
	"$where."
	ORDER BY ag.servicio_id, ag.hora";
$result = $mysqli->query($query);
$nroProductos = $result->num_rows;
   
$nroLotes = 10;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li><a href="javascript:pagination('.($paginaActual-1).');">Anterior</a></li>';
}
for($i=1; $i<=$nroPaginas; $i++){
	if($i == $paginaActual){
		$lista = $lista.'<li class="active"><a href="javascript:pagination('.$i.');">'.$i.'</a></li>';
	}else{
		$lista = $lista.'<li><a href="javascript:pagination('.$i.');">'.$i.'</a></li>';
	}
}
if($paginaActual < $nroPaginas){
	$lista = $lista.'<li><a href="javascript:pagination('.($paginaActual+1).');">Siguiente</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}

$registro = "SELECT p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', CAST(ag.fecha_cita AS DATE) AS 'fecha_cita', ag.hora AS 'hora', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', s.nombre AS 'servicio'
	FROM agenda AS ag
	INNER JOIN pacientes AS p
	ON ag.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
	ON ag.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON ag.servicio_id = s.servicio_id
	INNER JOIN puesto_colaboradores AS pc
	ON c.colaborador_id = pc.puesto_id		
	"$where."
	ORDER BY ag.servicio_id, ag.hora ASC LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
		  <tr>
			<th width="7.29%">No.</th>
			<th width="7.29%">Expediente</th>
			<th width="21.29%">Paciente</th>
			<th width="14.29%">Fecha Cita</th>
			<th width="14.29%">Hora Cita</th>
			<th width="21.29%">Profesional</th>				
			<th width="21.29%">Servicio</th>			
			<th width="7.29%">Opciones</th>
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
	   <td>'.$registro2['paciente'].'</td>
	   <td>'.$registro2['fecha_cita'].'</td>
	   <td>'.$registro2['hora'].'</td>
	   <td>'.$registro2['colaborador'].'</td>		   
	   <td>'.$registro2['servicio'].'</td>	   
	   <td>
		   <a title = "Cambiar fecha de Cita" href="javascript:editarRegistro('.$registro2['expediente'].','.$registro2['colaborador'].');" class="glyphicon glyphicon-edit"></a>			   
		   <a title = "Eliminar Usuario" href="javascript:modal_eliminar('.$registro2['expediente'].','.$expediente.');" class="glyphicon glyphicon-remove-circle"></a>
	   </td>
  </tr>';		
  $i++;
}
	

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>