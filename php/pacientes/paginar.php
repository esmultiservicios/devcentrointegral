<?php
session_start();   
include "../funtions.php";

header("Content-Type: text/html;charset=utf-8");

//CONEXION A DB
$mysqli = connect_mysqli();

$paginaActual = $_POST['partida'];
$estado = $_POST['estado'];
$paciente = $_POST['paciente'];
$dato = $_POST['dato'];

$query_row = "SELECT pacientes_id, CONCAT(nombre,' ',apellido) AS 'paciente', identidad, telefono1, telefono2, fecha_nacimiento, expediente AS 'expediente_', localidad,
(CASE WHEN estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado',
(CASE WHEN genero = 'H' THEN 'Hombre' ELSE 'Mujer' END) AS 'genero',
(CASE WHEN expediente = '0' THEN 'TEMP' ELSE expediente END) AS 'expediente', email
FROM pacientes
WHERE estado = '$estado' AND (expediente LIKE '$dato%' OR nombre LIKE '$dato%' OR apellido LIKE '$dato%' OR CONCAT(apellido,' ',nombre) LIKE '%$dato%' OR CONCAT(nombre,' ',apellido) LIKE '%$dato%' OR telefono1 LIKE '$dato%' OR identidad LIKE '$dato%')
ORDER BY expediente";	

$result = $mysqli->query($query_row);     

$nroProductos=$result->num_rows; 
$nroLotes = 15;
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

$query = "SELECT pacientes_id, CONCAT(nombre,' ',apellido) AS 'paciente', identidad, telefono1, telefono2, fecha_nacimiento, expediente AS 'expediente_', localidad,
(CASE WHEN estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado',
(CASE WHEN genero = 'H' THEN 'Hombre' ELSE 'Mujer' END) AS 'genero',
(CASE WHEN expediente = '0' THEN 'TEMP' ELSE expediente END) AS 'expediente', email
FROM pacientes
WHERE estado = '$estado' AND (expediente LIKE '$dato%' OR nombre LIKE '$dato%' OR apellido LIKE '$dato%' OR CONCAT(apellido,' ',nombre) LIKE '%$dato%' OR CONCAT(nombre,' ',apellido) LIKE '%$dato%' OR telefono1 LIKE '$dato%' OR identidad LIKE '$dato%')
ORDER BY expediente LIMIT $limit, $nroLotes
";
$result = $mysqli->query($query);    
  
$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
	<tr>
		<th width="2.09%">N°</th>
		<th width="5.09%">Expediente</th>
		<th width="11.09%">Identidad</th>
		<th width="15.09%">Paciente</th>
		<th width="9.09%">Genero</th>
		<th width="9.09%">Edad</th>					   
		<th width="9.09%">Telefono1</th>
		<th width="9.09%">Telefono2</th>	
		<th width="12.09%">Correo</th>
		<th width="14.09%">Dirección</th>						   
		<th width="4.09%">Opciones</th>
	</tr>';

$i=1;						
while($registro2 = $result->fetch_assoc()){
	$fecha_nacimiento = $registro2['fecha_nacimiento'];
	//OBTENER LA EDAD DEL USUARIO 
	/*********************************************************************************/
	$valores_array = getEdad($fecha_nacimiento);
	$anos = $valores_array['anos'];
	$meses = $valores_array['meses'];	  
	$dias = $valores_array['dias'];	
	/*********************************************************************************/
	if ($anos>1 ){
	$palabra_anos = "Años";
	}else{
	$palabra_anos = "Año";
	}

	if ($meses>1 ){
	$palabra_mes = "Meses";
	}else{
	$palabra_mes = "Mes";
	}

	if($dias>1){
		$palabra_dia = "Días";
	}else{
		$palabra_dia = "Día";
	}

	$tabla = $tabla.'<tr>
	   <td>'.$i.'</td>
	   <td><a style="text-decoration:none" title = "Información de Usuario" href="javascript:showExpediente('.$registro2['pacientes_id'].');">'.$registro2['expediente'].'</a></td>
	   <td>'.$registro2['identidad'].'</td>
	   <td>'.$registro2['paciente'].'</td>
	   <td>'.$registro2['genero'].'</td>
	   <td>'.$anos." ".$palabra_anos.'</td>
	   <td>'.$registro2['telefono1'].'</td>
	   <td>'.$registro2['telefono2'].'</td>
	   <td>'.$registro2['email'].'</td>	   
	   <td>'.$registro2['localidad'].'</td>	 		   
	   <td>
		   <a style="text-decoration:none;" title="Asignar Expediente a Usuario" href="javascript:modal_agregar_expediente('.$registro2['pacientes_id'].','.$registro2['expediente_'].');void(0);" class="fas fas fa-plus fa-lg"></a>
		   <a style="text-decoration:none;" title = "Asignar Expediente a Usuario de Forma Manual" href="javascript:modal_agregar_expediente_manual('.$registro2['pacientes_id'].');void(0);" class="fas fa-edit fa-lg"></a>			   
		   <a style="text-decoration:none;" title = "Editar Usuario" href="javascript:editarRegistro('.$registro2['pacientes_id'].');void(0);" class="fas fa-user-edit fa-lg"></a>
		   <a style="text-decoration:none;" title = "Eliminar Usuario" href="javascript:modal_eliminar('.$registro2['pacientes_id'].');void(0);" class="fas fa-trash fa-lg"></a>
	   </td>		              		  
	</tr>';
	$i++;
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="11" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="11"><b><p ALIGN="center">Total de Registros Encontrados '.number_format($nroProductos).'</p></b>
   </tr>';		
}   

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);
?>