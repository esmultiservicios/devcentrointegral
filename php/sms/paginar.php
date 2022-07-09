<?php
session_start(); 
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$paginaActual = $_POST['partida'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$profesional = $_POST['profesional'];
$usuario = $_POST['usuario'];
$dato = $_POST['dato'];

if($profesional != "" && $usuario == ""){
  $where = "WHERE CAST(s.fecha_registro AS DATE) BETWEEN '$fechai ' AND '$fechaf' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR p.telefono1 LIKE '$dato%' OR p.telefono2 LIKE '$dato%')";	
}if($profesional != "" && $usuario != ""){
  $where = "WHERE CAST(s.fecha_registro AS DATE) BETWEEN '$fechai ' AND '$fechaf' AND s.user = '$usuario' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR p.telefono1 LIKE '$dato%' OR p.telefono2 LIKE '$dato%')";	
}else{
  $where = "WHERE CAST(s.fecha_registro AS DATE) BETWEEN '$fechai ' AND '$fechaf' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR p.telefono1 LIKE '$dato%' OR p.telefono2 LIKE '$dato%')";		
}

$query = "SELECT s.*, c.nombre AS 'colaborador_nombre', c.apellido As 'colaborador_apellido', c1.colaborador_id AS 'user_id', c1.nombre AS 'usuario_nombre', c1.apellido As 'usuario_apellido', p.identidad, p.expediente AS 'expediente', p.nombre AS 'nombre', p.apellido AS 'apellido', serv.nombre AS 'servicio', s.dias AS 'dias', (CASE WHEN s.paciente = 'N' THEN 'N' ELSE 'S' END) AS 'paciente', (CASE WHEN p.genero = 'H' THEN 'H' ELSE 'M' END) AS 'sexo'
   FROM sms AS s 
   INNER JOIN colaboradores AS c 
   ON s.colaborador_id = c.colaborador_id 
   INNER JOIN colaboradores AS c1 
   ON s.user = c1.colaborador_id 
   INNER JOIN pacientes AS p 
   ON s.pacientes_id = p.pacientes_id  
   INNER JOIN servicios AS serv
   ON s.servicio_id = serv.servicio_id	
   ".$where."
   ORDER BY s.fecha_registro, s.pacientes_id ASC";

$result = $mysqli->query($query);
   
$nroLotes = 20;
$nroProductos = $result->num_rows;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li><a href="javascript:pagination('.(1).');void(0);">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li><a href="javascript:pagination('.($paginaActual-1).');void(0);">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li><a href="javascript:pagination('.($paginaActual+1).');void(0);">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li><a href="javascript:pagination('.($nroPaginas).');void(0);">Ultima</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}

$registro = "SELECT s.*, c.nombre AS 'colaborador_nombre', c.apellido As 'colaborador_apellido', c1.colaborador_id AS 'user_id', c1.nombre AS 'usuario_nombre', c1.apellido As 'usuario_apellido', p.identidad, p.expediente AS 'expediente', p.nombre AS 'nombre', p.apellido AS 'apellido', serv.nombre AS 'servicio', s.dias AS 'dias', (CASE WHEN s.paciente = 'N' THEN 'N' ELSE 'S' END) AS 'paciente', (CASE WHEN p.genero = 'H' THEN 'H' ELSE 'M' END) AS 'sexo'
   FROM sms AS s 
   INNER JOIN colaboradores AS c 
   ON s.colaborador_id = c.colaborador_id 
   INNER JOIN colaboradores AS c1 
   ON s.user = c1.colaborador_id 
   INNER JOIN pacientes AS p 
   ON s.pacientes_id = p.pacientes_id
   INNER JOIN servicios AS serv
   ON s.servicio_id = serv.servicio_id	   
   ".$where."
   ORDER BY s.fecha_registro, s.pacientes_id ASC LIMIT $limit, $nroLotes";

$result = $mysqli->query($registro);

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
		  <tr>
			<th width="2.66%">N°</th>
			<th width="4.66%">Expediente</th>
			<th width="6.66%">Identidad</th>
			<th width="8.66%">Nombre</th>
			<th width="9.66%">Apellido</th>
			<th width="3.66%">Sexo</th>
			<th width="3.66%">Paciente</th>
			<th width="8.66%">Fecha Cita</th>
			<th width="6.66%">Servicio</th>				
			<th width="6.66%">Profesional</th>
			<th width="5.66%">Para</th>	
			<th width="14.66%">Mensaje</th>
			<th width="4.66">Estado</th>	
			<th width="4.66%">Días</th>
			<th width="8.66%">Usuario</th>				
		   </tr>';
			
$i=1;			
while($registro2 = $result->fetch_assoc()){
  
	$to = '<a style="text-decoration:none" title = "Teléfono Usuario" href="tel:9'.$registro2['para'].'">'.$registro2['para'].'</a>'; 

	if ($registro2['expediente'] == 0){
		$expediente = "TEMP"; 
	}else{
		$expediente = $registro2['expediente'];
	}	 

	if( strlen($registro2['identidad'])<10 ){
	   $identidad = 'No porta identidad';
	}else{
	   $identidad = $registro2['identidad'];
	}
   
	
	if ($registro2['dias'] == 1){
		$dias_ = $registro2['dias']. " Día";
	}else{
		$dias_ = $registro2['dias']." Días";
	}
	
	$usuario_nombre = explode(" ", $registro2['usuario_nombre']);
	$nombre_usuario = $usuario_nombre[0];
	$usuario_apellido = explode(" ", $registro2['usuario_apellido']);	
	$apellido_usuario = $usuario_apellido[0];
	$nombre_completo_usuario = $apellido_usuario.' '.$nombre_usuario;
	
	$colaborador_nombre = explode(" ", $registro2['colaborador_nombre']);
	$nombre_colaborador = $colaborador_nombre[0];
	$colaborador_apellido = explode(" ", $registro2['colaborador_apellido']);	
	$apellido_colaborador = $colaborador_apellido[0];
	$nombre_completo_colaborador = $apellido_colaborador.' '.$nombre_colaborador;
	
	$tabla = $tabla.'<tr>
	   <td>'.$i.'</td>
	   <td>'.$registro2['expediente'].'</td>
	   <td>'.$identidad.'</td>
	   <td>'.$registro2['nombre'].'</td>
	   <td>'.$registro2['apellido'].'</td>
	   <td>'.$registro2['sexo'].'</td>
	   <td>'.$registro2['paciente'].'</td>
	   <td>'.$registro2['fecha'].'</td>
	   <td>'.$registro2['servicio'].'</td>		   
	   <td>'.$nombre_completo_colaborador.'</td>
	   <td>'.$to.'</td> 
	   <td>'.$registro2['mensaje'].'</td>		   
	   <td>'.$registro2['status'].'</td>
	   <td>'.$dias_.'</td>		   
	   <td>'.$nombre_completo_usuario.'</td>	   
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