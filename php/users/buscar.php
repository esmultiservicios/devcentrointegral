<?php
session_start(); 
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$dato = $_POST['dato'];
$paginaActual = $_POST['partida'];
$tipo="";
//EJECUTAMOS LA CONSULTA DE BUSQUEDA

if ($dato == ""){
$query = "SELECT u.id AS id, c.nombre AS nombre, c.apellido AS apellido, u.username AS username, u.email AS email, e.nombre AS empresa, u.type AS tipo, u.estatus AS estatus
       FROM users AS u
       INNER JOIN colaboradores AS c
       ON u.colaborador_id = c.colaborador_id 
       INNER JOIN empresa AS e
       ON c.empresa_id = e.empresa_id
	   ORDER BY u.id ASC"));	
}else{
$query = "SELECT u.id AS id, c.nombre AS nombre, c.apellido AS apellido, u.username AS username, u.email AS email, e.nombre AS empresa, u.type AS tipo, u.estatus AS estatus
       FROM users AS u
       INNER JOIN colaboradores AS c
       ON u.colaborador_id = c.colaborador_id 
       INNER JOIN empresa AS e
       ON c.empresa_id = e.empresa_id
	   WHERE u.id LIKE '$dato%' OR CONCAT(c.nombre,' ',c.apellido) LIKE '$dato%' OR u.username LIKE '$dato%'
	   ORDER BY u.id ASC"));	
}

$result = $mysqli->query($query);
$nroProductos = $result->num_rows;

     $nroLotes = 15;
     $nroPaginas = ceil($nroProductos/$nroLotes);
     $lista = '';
     $tabla = '';

	 if($paginaActual > 1){
        $lista = $lista.'<li><a href="javascript:pagination('.(1).');">Inicio</a></li>';
     }
	
     if($paginaActual > 1){
        $lista = $lista.'<li><a href="javascript:pagination('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
     }
    
     if($paginaActual < $nroPaginas){
        $lista = $lista.'<li><a href="javascript:pagination('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
     }
	
	 if($paginaActual > 1){
        $lista = $lista.'<li><a href="javascript:pagination('.($nroPaginas).');">Ultima</a></li>';
     }
  
  	 if($paginaActual <= 1){
  		$limit = 0;
  	 }else{
  		$limit = $nroLotes*($paginaActual-1);
  	 }	  
	
if ($dato == ""){
	$registro = "SELECT u.id AS id, c.nombre AS nombre, c.apellido AS apellido, u.username AS username, u.email AS email, e.nombre AS empresa, u.type AS tipo, u.estatus AS estatus
       FROM users AS u
       INNER JOIN colaboradores AS c
       ON u.colaborador_id = c.colaborador_id 
       INNER JOIN empresa AS e
       ON c.empresa_id = e.empresa_id
	   ORDER BY u.id ASC LIMIT $limit, $nroLotes";	
	
}else{
	$registro = "SELECT u.id AS id, c.nombre AS nombre, c.apellido AS apellido, u.username AS username, u.email AS email, e.nombre AS empresa, u.type AS tipo, u.estatus AS estatus
       FROM users AS u
       INNER JOIN colaboradores AS c
       ON u.colaborador_id = c.colaborador_id 
       INNER JOIN empresa AS e
       ON c.empresa_id = e.empresa_id
	   WHERE u.id LIKE '$dato%' OR CONCAT(c.nombre,' ',c.apellido) LIKE '$dato%' OR u.username LIKE '$dato%'
	   ORDER BY u.id ASC LIMIT $limit, $nroLotes";	
}

$result = $mysqli->query($registro);

  	$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			            <tr>
                  	      <th width="4%">Código</th>
                          <th width="12%">Nombre</th>
                          <th width="15%">Apellido</th>
                          <th width="7%">Username</th>
                          <th width="17%">Email</th>	
                          <th width="28%">Empresa</th>	
                          <th width="7%">Tipo</th>	
                          <th width="5%">Estatus</th>																																										
             	          <th width="4%">Opciones</th>
			            </tr>';
				
	while($registro2 = $result->fetch_assoc()){	
		if ($registro2['tipo'] == 1)
			$tipo = "Administrador";
		else if ($registro2['tipo'] == 2)
		    $tipo = "Médicos";	
		else if ($registro2['tipo'] == 3)
		    $tipo = "Usuarios";	
		else if ($registro2['tipo'] == 4)
		    $tipo = "Atención Usuarios";				
		else if ($registro2['tipo'] == 5)
		    $tipo = "Agenda";		
		else if ($registro2['tipo'] == 6)
		    $tipo = "Archivo";		
       else if ($registro2['tipo'] == 7)
		    $tipo = "Gerencia";				
	   else if ($registro2['tipo'] == 8)
		    $tipo = "Asistencial";	
	   else if ($registro2['tipo'] == 9)
		    $tipo = "Información Usuarios";	
       else if ($registro2['tipo'] == 10)
		    $tipo = "Reportes Secretaría";
       else if ($registro2['tipo'] == 11)
		    $tipo = "Enfermeras";	
       else if ($registro2['tipo'] == 12)
	        $tipo = "Talento Humano";		
       else if ($registro2['tipo'] == 13)
    	    $tipo = "Coordinadora de Enfermería";		
		
		if ($registro2['estatus'] == 1)
		    $status = "Activo";
		else
		    $status = "Inactivo";	
					
		$tabla = $tabla.'<tr>
		   <td>'.$registro2['id'].'</td>		
       	   <td>'.$registro2['nombre'].'</td>
      	   <td>'.$registro2['apellido'].'</td>
		   <td>'.$registro2['username'].'</td>
 		   <td>'.$registro2['email'].'</td>
		   <td>'.$registro2['empresa'].'</td>
		   <td>'.$tipo.'</td>
		   <td>'.$status.'</td>		   		   		   		   		   		   
		   <td>
               <a href="javascript:editarRegistro('.$registro2['id'].');" class="glyphicon glyphicon-edit" title="Editar Registro"></a>
			   <a href="javascript:modificarContra('.$registro2['id'].');" class="glyphicon glyphicon-repeat" title="Resetear Contraseña"></a>
               <a href="javascript:modal_eliminar('.$registro2['id'].');" class="glyphicon glyphicon-remove-circle" title="Eliminar Registro"></a>
           </td>
	  </tr>';		
	}
        

    $tabla = $tabla.'</table>';

    $array = array(0 => $tabla,
    			   1 => $lista);

    echo json_encode($array);
?>