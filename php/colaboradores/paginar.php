<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$dato = $_POST['dato'];
$estatus = $_POST['estatus'];
$paginaActual = $_POST['partida'];
//EJECUTAMOS LA CONSULTA DE BUSQUEDA

$query = "SELECT c.colaborador_id As 'codigo', CONCAT(c.nombre, ' ', c.apellido) AS 'nombre', p.nombre AS 'puesto', e.nombre AS 'empresa', c.identidad AS 'identidad', c.estatus AS 'estatus'
      FROM colaboradores AS c
      INNER JOIN empresa AS e
      ON c.empresa_id = e.empresa_id
      INNER JOIN puesto_colaboradores AS p
      ON c.puesto_id = p.puesto_id 
	  WHERE c.estatus = '$estatus' AND (c.colaborador_id LIKE '$dato%' OR CONCAT(c.nombre,' ',c.apellido) LIKE '$dato%' OR p.nombre LIKE '$dato%' OR c.identidad LIKE '$dato%')
      ORDER BY c.colaborador_id
	  ";	  
$result = $mysqli->query($query);	  
$nroProductos = $result->num_rows;
	  
    $nroLotes = 20;
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
	  
$registro = "SELECT c.colaborador_id As 'codigo', CONCAT(c.nombre, ' ', c.apellido) AS 'nombre', p.nombre AS 'puesto', e.nombre AS 'empresa', c.identidad AS 'identidad', c.estatus AS 'estatus'
      FROM colaboradores AS c
      INNER JOIN empresa AS e
      ON c.empresa_id = e.empresa_id
      INNER JOIN puesto_colaboradores AS p
      ON c.puesto_id = p.puesto_id 
	  WHERE c.estatus = '$estatus' AND (c.colaborador_id LIKE '%$dato%' OR CONCAT(c.nombre,' ',c.apellido) LIKE '$dato%' OR p.nombre LIKE '$dato%' OR c.identidad LIKE '$dato%')
      ORDER BY c.colaborador_id ASC LIMIT $limit, $nroLotes
	  ";	
$result = $mysqli->query($registro);	  

//CREAMOS NUESTRA VISTA Y LA DEVOLVEMOS AL AJAX
  	$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			  <tr>
                 <th width="7.5%">Código</th>
                 <th width="18.5%">Nombre</th>
				 <th width="12.5%">Identidad</th>
                 <th width="17.5%">Puesto</th>
                 <th width="20.5%">Empresa</th>
                 <th width="5.5%">Estatus</th>				 
             	 <th width="5.5%">Opciones</th>
			  </tr>';
					
	while($registro2 = $result->fetch_assoc()){
          if($registro2['estatus'] == 1){
			  $estatus = 'Activo';
		  }else{
			  $estatus = 'Inactivo';
		  }	
		$tabla = $tabla.'<tr>
		         <td>'.$registro2['codigo'].'</td>		
       	         <td>'.$registro2['nombre'].'</td>
				 <td>'.$registro2['identidad'].'</td>
		         <td>'.$registro2['puesto'].'</td>
		         <td>'.$registro2['empresa'].'</td>	
                 <td>'.$estatus.'</td>					 
			     <td>
				    <a style="text-decoration:none;" href="javascript:editarRegistro('.$registro2['codigo'].');void(0);" class="fas fa-edit fa-lg"></a> 
				    <a style="text-decoration:none; "href="javascript:modal_eliminar('.$registro2['codigo'].');void(0);" class="fas fa-trash fa-lg"></a>
			     </td>   		   		   		   		   		   
	  </tr>';  
	}    
	
	if($nroProductos == 0){
        $tabla = $tabla.'<tr>
	       <td colspan="13" style="color:#C7030D">No se encontraron resultados</td>
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