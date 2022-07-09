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
$colaborador = $_POST['colaborador'];

$colaborador_where = "";
$dato_where = "";

if($colaborador != ""){
	$where = "WHERE a.fecha BETWEEN '$desde' AND '$hasta' AND f.colaborador_id = '$profesional' AND f.estado = '$estado'";
}else if($dato != ""){
	$where = "WHERE CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%'";
}else{
	$where = "WHERE a.fecha BETWEEN '$desde' AND '$hasta'";
}

$query = "SELECT a.ausencia_id AS 'ausencia_id', DATE_FORMAT(a.fecha, '%d/%m/%Y') AS 'fecha', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', s.nombre AS 'servicio', a.pacientes_id AS 'pacientes_id'
	FROM ausencias AS a
	INNER JOIN pacientes AS p
	ON a.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON a.servicio_id = s.servicio_id
	".$where."
    ORDER BY a.fecha DESC";	
$result = $mysqli->query($query);
$nroProductos = $result->num_rows;
  
$nroLotes = 15;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.(1).');">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($nroPaginas).');">Ultima</a></li>';
}	

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}

$registro = "SELECT a.ausencia_id AS 'ausencia_id',  DATE_FORMAT(a.fecha, '%d/%m/%Y') AS 'fecha', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', s.nombre AS 'servicio', a.pacientes_id AS 'pacientes_id'
	FROM ausencias AS a
	INNER JOIN pacientes AS p
	ON a.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON a.servicio_id = s.servicio_id
	".$where."
    ORDER BY a.fecha DESC
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="10.28%">Fecha</th>
			<th width="28.28%">Paciente</th>
			<th width="14.28%">Identidad</th>
			<th width="14.28%">Colaborador</th>
			<th width="14.28%">Servicio</th>
			<th width="4.28%">Opciones</th>				
			</tr>';			
			
while($registro2 = $result->fetch_assoc()){	
	$tabla = $tabla.'<tr>
	   <td>'.$registro2['fecha'].'</td>
	   <td>'.$registro2['paciente'].'</td>		   
	   <td>'.$registro2['identidad'].'</td>	
       <td>'.$registro2['colaborador'].'</td>	
       <td>'.$registro2['servicio'].'</td>
	   <td>
		   <a data-toggle="tooltip" data-placement="top" title="Eliminar Registro" href="javascript:modal_eliminarAusencias('.$registro2['ausencia_id'].','.$registro2['pacientes_id'].');" class="fas fa-trash fa-lg" style="text-decoration:none;"></a>
	   </td>		   
	</tr>';	        
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="17" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="17"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>