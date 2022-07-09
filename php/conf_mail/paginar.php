<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$paginaActual = $_POST['partida'];

$dato = $_POST['dato'];

if($dato == ""){
	$query = "SELECT c.correo_id AS 'correo_id', c.correo AS 'correo', c.server AS 'servidor', c.port AS 'puerto', c.smtp_secure AS 'smtpSecure', ct.nombre AS 'tipo_correo'
	FROM correo AS c
	INNER JOIN correo_tipo AS ct
	ON c.correo_tipo_id = ct.correo_tipo_id";
}else{
	$query = "SELECT c.correo_id AS 'correo_id', c.correo AS 'correo', c.server AS 'servidor', c.port AS 'puerto', c.smtp_secure AS 'smtpSecure', ct.nombre AS 'tipo_correo'
	FROM correo AS c
	INNER JOIN correo_tipo AS ct
	ON c.correo_tipo_id = ct.correo_tipo_id 
	WHERE c.correo LIKE '$dato%'";
}

$result = $mysqli->query($query);
$nroProductos = $result->num_rows;

$nroLotes = 10;
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

if($dato == ""){
	$query = "SELECT c.correo_id AS 'correo_id', c.correo AS 'correo', c.server AS 'servidor', c.port AS 'puerto', c.smtp_secure AS 'smtpSecure', ct.nombre AS 'tipo_correo'
	FROM correo AS c
	INNER JOIN correo_tipo AS ct
	ON c.correo_tipo_id = ct.correo_tipo_id";
}else{
	$query = "SELECT c.correo_id AS 'correo_id', c.correo AS 'correo', c.server AS 'servidor', c.port AS 'puerto', c.smtp_secure AS 'smtpSecure', ct.nombre AS 'tipo_correo'
		FROM correo AS c
		INNER JOIN correo_tipo AS ct
		ON c.correo_tipo_id = ct.correo_tipo_id
		WHERE c.correo LIKE '$dato%'";
}

$result = $mysqli->query($query);

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="2.28%">No.</th>
			<th width="14.28%">Tipo</th>
			<th width="29.28%">Correo</th>
			<th width="17.28%">Servidor</th>
			<th width="17.28%">Puerto</th>
			<th width="14.28%">SMTP Secure</th>			
			<th width="5.28%">Opciones</th>
			</tr>';
$i = 1;				
while($registro2 = $result->fetch_assoc()){	  
	$tabla = $tabla.'<tr>
			<td>'.$i.'</td>
			<td>'.$registro2['tipo_correo'].'</td>
			<td>'.$registro2['correo'].'</td>
			<td>'.$registro2['servidor'].'</td>
			<td>'.$registro2['puerto'].'</td>
			<td>'.$registro2['smtpSecure'].'</td>
			<td>
			   <a style="text-decoration:none;" title = "Editar Registros" href="javascript:editarRegistro('.$registro2['correo_id'].');" class="fas fa-edit fa-lg"></a>
			   <a style="text-decoration:none;" href="javascript:modal_eliminar('.$registro2['correo_id'].');" class="fas fa-trash fa-lg"></a>				   
			</td>				
			</tr>';	
			$i++;				
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="7" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="7"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>