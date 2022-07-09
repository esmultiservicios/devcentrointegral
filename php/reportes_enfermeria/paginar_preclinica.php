<?php 
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];

$paginaActual = $_POST['partida'];
$desde = $_POST['desde'];
$hasta = $_POST['hasta'];
$dato = $_POST['dato'];
$profesional = $_POST['profesional'];

if($profesional != ""){
	$where = "WHERE pre.fecha BETWEEN '$desde' AND '$hasta' AND pre.colaborador_id = '$profesional'";
}else if($dato != ""){
	$where = "WHERE CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%'";
}else{
	$where = "WHERE pre.fecha BETWEEN '$desde' AND '$hasta'";
}

$query = "SELECT DISTINCT pre.pacientes_id AS 'pacientes_id', pre.preclinica_id AS 'preclinica_id', DATE_FORMAT(pre.fecha, '%d/%m/%Y') AS 'fecha', CONCAT(p.apellido,' ',p.nombre) AS 'nombre', pre.expediente As 'expediente', p.identidad AS 'identidad', pre.edad AS 'edad', (CASE WHEN p.genero = 'H' THEN 'X' ELSE '' END) AS 'h',
  (CASE WHEN p.genero = 'M' THEN 'X' ELSE '' END) AS 'm', (CASE WHEN pre.paciente = 'n' THEN 'X' ELSE '' END) AS 'nuevo', (CASE WHEN pre.paciente = 'S' THEN 'X' ELSE '' END) AS 'subsiguiente', pre.pa AS 'pa', pre.fr As 'fr', pre.fc As 'fc', pre.t As 'temperatura', pre.talla AS 'talla', pre.peso AS 'peso', CONCAT(c.nombre,' ',c.apellido) AS 'medico', CONCAT(c1.nombre,' ',c1.apellido) AS 'usuario'
   FROM preclinica AS pre
   INNER JOIN pacientes AS p
   ON pre.expediente = p.expediente
   INNER JOIN colaboradores AS c
   ON pre.colaborador_id = c.colaborador_id
   INNER JOIN colaboradores AS c1
   ON pre.usuario = c1.colaborador_id
   ".$where."
   ORDER BY pre.fecha DESC"; 
$result = $mysqli->query($query);
$nroProductos = $result->num_rows;
  
$nroLotes = 15;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';	

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_preclinica('.(1).');">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_preclinica('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_preclinica('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_preclinica('.($nroPaginas).');">Ultima</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}	

$registro = "SELECT DISTINCT pre.pacientes_id AS 'pacientes_id', pre.preclinica_id AS 'preclinica_id', DATE_FORMAT(pre.fecha, '%d/%m/%Y') AS 'fecha', CONCAT(p.apellido,' ',p.nombre) AS 'nombre', pre.expediente As 'expediente', p.identidad AS 'identidad', pre.edad AS 'edad', (CASE WHEN p.genero = 'H' THEN 'X' ELSE '' END) AS 'h',
  (CASE WHEN p.genero = 'M' THEN 'X' ELSE '' END) AS 'm', (CASE WHEN pre.paciente = 'n' THEN 'X' ELSE '' END) AS 'nuevo', (CASE WHEN pre.paciente = 'S' THEN 'X' ELSE '' END) AS 'subsiguiente', pre.pa AS 'pa', pre.fr As 'fr', pre.fc As 'fc', pre.t As 'temperatura', pre.talla AS 'talla', pre.peso AS 'peso', CONCAT(c.nombre,' ',c.apellido) AS 'medico', CONCAT(c1.nombre,' ',c1.apellido) AS 'usuario'
   FROM preclinica AS pre
   INNER JOIN pacientes AS p
   ON pre.expediente = p.expediente
   INNER JOIN colaboradores AS c
   ON pre.colaborador_id = c.colaborador_id
   INNER JOIN colaboradores AS c1
   ON pre.usuario = c1.colaborador_id	   
   ".$where."
   ORDER BY pre.fecha DESC
   LIMIT $limit, $nroLotes";
   
$result = $mysqli->query($registro);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="5.55%">Fecha</th>
			<th width="21.55%">Nombre</th>
			<th width="5.55%">Expediente</th>	
			<th width="5.55%">Identidad</th>				
			<th width="2.55%">Edad</th>
			<th width="2.55%">H</th>
			<th width="2.55%">M</th>				
			<th width="2.55%">N</th>
			<th width="2.55%">S</th>	
			<th width="2.55%">PA</th>
			<th width="2.55%">FR</th>
			<th width="2.55%">FC</th>				
			<th width="2.55%">T°</th>
			<th width="5.55%">Peso</th>				
			<th width="5.55%">Talla</th>			
			<th width="10.55%">Profesional</th>
			<th width="10.55%">Usuario</th>
			<th width="5.55%">Opciones</th>				
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
	   <td>'.$registro2['pa'].'</td>
	   <td>'.$registro2['fr'].'</td>
	   <td>'.$registro2['fc'].'</td>
	   <td>'.$registro2['temperatura'].'</td>
	   <td>'.$registro2['peso'].'</td>		   
	   <td>'.$registro2['talla'].'</td>
	   <td>'.$registro2['medico'].'</td>
	   <td>'.$registro2['usuario'].'</td>
	   <td>
		   <a href="javascript:editarPreclinica('.$registro2['preclinica_id'].');" data-toggle="tooltip" data-placement="top" title="Editar Registro" class="fas fa-edit fa-lg" style="text-decoration:none;"></a>
		   <a data-toggle="tooltip" data-placement="top" title="Eliminar Registro" href="javascript:modal_eliminarPreclinica('.$registro2['preclinica_id'].','.$registro2['pacientes_id'].');" class="fas fa-trash fa-lg" style="text-decoration:none;"></a>
	   </td>	   
	</tr>';	        
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="19" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="19"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>