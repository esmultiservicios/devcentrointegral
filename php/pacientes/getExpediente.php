<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id'];

$query = "SELECT p.pacientes_id As 'pacientes_id',  CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', p.telefono1 AS 'telefono1', p.telefono2 AS 'telefono2', p.fecha_nacimiento As 'fecha_nacimiento', p.email AS 'email',
(CASE WHEN p.estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado',
(CASE WHEN p.genero = 'H' THEN 'Hombre' ELSE 'Mujer' END) AS 'genero',
(CASE WHEN p.expediente = '0' THEN 'TEMP' ELSE expediente END) AS 'expediente', p.responsable As 'responsable', r.nombre AS 'parentesco'
	FROM pacientes AS p
	LEFT JOIN responsable AS r
	ON p.responsable_id = r.responsable_id
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($query);

if($result->num_rows>0){
	$consulta1=$result->fetch_assoc();
	$nombre = $consulta1['paciente'];
	$identidad = $consulta1['identidad'];
	$telefono1 = $consulta1['telefono1'];
	$telefono2 = $consulta1['telefono2'];
	$fecha_nacimiento = $consulta1['fecha_nacimiento'];
	$estado = $consulta1['estado'];
	$genero = $consulta1['genero'];	
	$email = $consulta1['email'];		
	$expediente = $consulta1['expediente'];	
	$responsable = $consulta1['responsable'];	
	$parentesco = $consulta1['parentesco'];		
  
	//CONSULTAR DATOS DEL USUARIO EN LA AGENDA 1ERA VEZ QUE VIENE AL HOSPITAL
	$query_datos = "SELECT a.agenda_id, fecha_registro as 'fecha_registro', DATE_FORMAT(a.fecha_registro, '%d/%m/%Y %h:%i:%s %p') as 'fecha_registro1', 
	CAST(a.fecha_cita AS DATE) AS 'fecha_cita',  DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita1', a.status , a.hora, 
	CONCAT(c.nombre,' ',c.apellido) AS 'profesional', CONCAT(c1.nombre,' ',c1.apellido) AS 'usuario'
		FROM agenda AS a
		INNER JOIN colaboradores AS c
		ON a.colaborador_id = c.colaborador_id
		INNER JOIN colaboradores AS c1
		ON a.usuario = c1.colaborador_id	 
		WHERE a.pacientes_id = '$pacientes_id' 
		ORDER BY a.fecha_registro ASC";
	 
	$result = $mysqli->query($query_datos);

	$consulta_fecha_solicitud_cita = "";
	$consulta_fecha_cita = "";
	$consulta_hora = "";  
	$consulta_status = "";
	$consulta_profesional = "";
	$consulta_usuario = "";  
	$consulta_estado = "";
  
	if($result->num_rows>0){
		$consultar_datos1=$result->fetch_assoc(); 
		$consulta_fecha_solicitud_cita = $consultar_datos1['fecha_registro1'];
		$consulta_fecha_cita = $consultar_datos1['fecha_cita1'];
		$consulta_hora = $consultar_datos1['hora'];  
		$consulta_status = $consultar_datos1['status'];
		$consulta_profesional = $consultar_datos1['profesional'];
		$consulta_usuario = $consultar_datos1['usuario'];

		 if($consulta_status == 0){
			 $consulta_estado = 'Pendiente';
		 }else if($consulta_status == 1){
			$consulta_estado = 'Atendido';
		 }else if($consulta_status == 2){
			$consulta_estado = 'Ausente';
		 }
	}
  
	if($consulta_fecha_solicitud_cita != ""){
		$consulta_datos_cita_primera_vez = "
				<div class='form-row'>
					<div class='col-md-12 mb-6 sm-3'>
					  <p style='color: #077A2F;' align='center'><b>Datos de Cita</b></p>
					</div>					
				</div>
				<div class='form-row'>
					<div class='col-md-12 mb-6 sm-3'>
					  <p style='color: #FF0000;' align='center'><b>Primera vez que llego al Hospital</b></p>
					</div>					
				</div>	
				<div class='form-row'>
					<div class='col-md-4 mb-3'>
					  <p><b>Fecha de Solicitud:</b> $consulta_fecha_solicitud_cita</p>
					</div>
					<div class='col-md-4 mb-3'>
					  <p><b> Creado por:</b> $consulta_usuario</p>
					</div>					
					<div class='col-md-4 mb-3'>
					  <p><b> Creado por:</b> $consulta_usuario</p>
					</div>					
					<div class='col-md-4 mb-3 sm-3'>
					  <p><b>Fecha de Cita:</b> $consulta_fecha_cita <b></p>
					</div>	
					<div class='col-md-4 mb-3 sm-3'>
					  <p>Hora:</b> $consulta_hora</p>
					</div>						
				</div>					
		";
	}else{
	  $consulta_datos_cita_primera_vez = "";
	}
  
  //CONSULTAR DATOS DEL USUARIO EN LA AGENDA ULTIMA CITA ENCONTRADA
  $query_datos_ultima = "SELECT a.agenda_id, fecha_registro as 'fecha_registro', DATE_FORMAT(a.fecha_registro, '%d/%m/%Y %h:%i:%s %p') as 'fecha_registro1', 
     CAST(a.fecha_cita AS DATE) AS 'fecha_cita',  DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita1', a.status , a.hora, 
	 CONCAT(c.nombre,' ',c.apellido) AS 'profesional', CONCAT(c1.nombre,' ',c1.apellido) AS 'usuario'
     FROM agenda AS a
	 INNER JOIN colaboradores AS c
	 ON a.colaborador_id = c.colaborador_id
	 INNER JOIN colaboradores AS c1
	 ON a.usuario = c1.colaborador_id	 
     WHERE a.pacientes_id = '$pacientes_id' 
     ORDER BY a.fecha_registro DESC";
	 
	$result = $mysqli->query($query_datos_ultima);
	$consultar_datos_ultima1=$result->fetch_assoc();

	$consulta_ultima_fecha_solicitud_cita = "";
	$consulta_ultima_fecha_cita = "";
	$consulta_utlima_hora = "";  
	$consulta_utltima_status = "";
	$consulta_ultima_profesional = "";
	$consulta_ultima_usuario = ""; 
	$consulta_ultima_estado = "";
  
	if($result->num_rows>0){
	  $consulta_ultima_fecha_solicitud_cita = $consultar_datos_ultima1['fecha_registro1'];
	  $consulta_ultima_fecha_cita = $consultar_datos_ultima1['fecha_cita1'];
	  $consulta_utlima_hora = $consultar_datos_ultima1['hora'];  
	  $consulta_utltima_status = $consultar_datos_ultima1['status'];
	  $consulta_ultima_profesional = $consultar_datos_ultima1['profesional'];
	  $consulta_ultima_usuario = $consultar_datos_ultima1['usuario'];
	  
	  if($consulta_utltima_status == 0){
		  $consulta_ultima_estado = 'Pendiente';
	  }else if($consulta_utltima_status == 1){
		  $consulta_ultima_estado = 'Atendido';
	  }else if($consulta_utltima_status == 2){
		  $consulta_ultima_estado = 'Ausente';
	  }	  
	}
  
	if($consulta_fecha_solicitud_cita != ""){
		$consulta_datos_cita_ultima = "
				<div class='form-row'>
					<div class='col-md-12 mb-6 sm-3'>
					  <p style='color: #FF0000;' align='center'><b>Última Cita Encontrada</b></p>
					</div>					
				</div>
				<div class='form-row'>
					<div class='col-md-4 mb-3'>
					  <p><b>Fecha de Solicitud:</b> $consulta_ultima_fecha_solicitud_cita</p>
					</div>
					<div class='col-md-4 mb-3'>
					  <p><b>Creado por:</b> $consulta_ultima_usuario</p>
					</div>					
					<div class='col-md-4 mb-3'>
					  <p><b>Fecha de Cita:</b> $consulta_ultima_fecha_cita</p>
					</div>					
					<div class='col-md-4 mb-3 sm-3'>
					  <p><b>Hora:</b> $consulta_utlima_hora <b></p>
					</div>	
					<div class='col-md-4 mb-3 sm-3'>
					  <p>Estado:</b> $consulta_ultima_estado</p>
					</div>	
					<div class='col-md-4 mb-3 sm-3'>
					  <p><b>Profesional:</b> $consulta_ultima_profesional</p>
					</div>						
				</div>				
			 ";	  
	}else{
	  $consulta_datos_cita_ultima = "";
	}  
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

echo "  
	<div class='form-row'>
		<div class='col-md-12 mb-6 sm-3'>
		  <p style='color: #333FFF;' align='center'><b>Información</b></p>
		</div>					
	</div>
	<div class='form-row'>
		<div class='col-md-4 mb-3'>
		  <p><b>Nombre:</b> $nombre</p>
		</div>
		<div class='col-md-4 mb-3'>
		  <p><b>Expediente:</b> $expediente</p>
		</div>					
		<div class='col-md-4 mb-3'>
		  <p><b>Genero:</b> $genero</p>
		</div>							
	</div>	
	<div class='form-row'>				
		<div class='col-md-4 mb-3 sm-3'>
		  <p><b>Fecha Nacimiento:</b> $fecha_nacimiento <b></p>
		</div>	
		<div class='col-md-4 mb-3 sm-3'>
		  <p>Teléfono 1:</b> $telefono1</p>
		</div>	
		<div class='col-md-4 mb-3 sm-3'>
		  <p><b>Telefono 2:</b> $telefono2</p>
		</div>	
		<div class='col-md-4 mb-3 sm-3'>
		  <p><b>Correo:</b> $email</p>
		</div>			
	</div>	
	<div class='form-row'>
		<div class='col-md-4 mb-3 sm-3'>
		  <p><b>Responsasble:</b> $responsable</p>
		</div>	
		<div class='col-md-4 mb-3 sm-3'>
		  <p><b>Parentesco:</b> $parentesco</p>
		</div>			
	</div>	
	<div class='form-row'>
		<div class='col-md-4 mb-3 sm-3'>
		  <p><b>Estado:</b> $estado</p>
		</div>	
		<div class='col-md-4 mb-3 sm-3'>
		  <p><b>Identidad:</b> $identidad</p>
		</div>			
	</div>		
".$consulta_datos_cita_primera_vez. " ".$consulta_datos_cita_ultima;  
}else{
	echo 1;
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>