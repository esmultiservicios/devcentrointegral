<?php 
session_start();   
include "../funtions.php";

// CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$dato = $_POST['dato'];
$estado = $_POST['estado'];

// CONSULTAR PUESTO_ID	
$consultar_puesto = "SELECT puesto_id FROM colaboradores WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_puesto) or die($mysqli->error);
$consultar_puesto2 = $result->fetch_assoc();
$puesto_id = $result->num_rows > 0 ? $consultar_puesto2['puesto_id'] : "";

$where = "WHERE CAST(a.fecha_cita AS DATE) BETWEEN '$fechai' AND '$fechaf' AND a.status = '$estado' AND a.colaborador_id = '$colaborador_id' AND a.preclinica = 1 AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.identidad LIKE '$dato%' OR p.apellido LIKE '$dato%')";

$query = "SELECT p.pacientes_id AS 'pacientes_id', a.agenda_id AS 'agenda_id', p.identidad AS 'identidad', CONCAT(p.apellido,' ',p.nombre) AS 'paciente', DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', 
    a.hora AS 'hora', a.paciente AS 'tipo_paciente', p.telefono1 AS 'telefono', CONCAT(c.apellido,' ',c.nombre) AS 'colaborador', s.nombre AS 'servicio', c.colaborador_id, s.servicio_id,
	a.observacion AS 'observacion', a.comentario AS 'comentario',
   (CASE WHEN a.status = '1' THEN 'Atendido' ELSE 'Pendiente' END) AS 'estatus', CAST(a.fecha_cita AS DATE) AS 'fecha'
	FROM agenda AS a
	INNER JOIN pacientes AS p ON a.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s ON a.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c ON a.colaborador_id = c.colaborador_id
	".$where;

$result = $mysqli->query($query) or die($mysqli->error);

$nroLotes = 25;
$nroProductos = $result->num_rows;
$nroPaginas = ceil($nroProductos / $nroLotes);
$lista = '';
$tabla = '';

if ($paginaActual > 1) {
    $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination(1);">Inicio</a></li>';
    $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination(' . ($paginaActual - 1) . ');">Anterior ' . ($paginaActual - 1) . '</a></li>';
}

if ($paginaActual < $nroPaginas) {
    $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination(' . ($paginaActual + 1) . ');">Siguiente ' . ($paginaActual + 1) . ' de ' . $nroPaginas . '</a></li>';
    $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination(' . $nroPaginas . ');">Última</a></li>';
}

$limit = $paginaActual <= 1 ? 0 : $nroLotes * ($paginaActual - 1);

$registro = "SELECT p.pacientes_id AS 'pacientes_id', a.agenda_id AS 'agenda_id', p.identidad AS 'identidad', CONCAT(p.apellido,' ',p.nombre) AS 'paciente', p.telefono1 AS 'telefono', 
    DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', a.hora AS 'hora', a.paciente AS 'tipo_paciente', CONCAT(c.apellido,' ',c.nombre) AS 'colaborador', 
	s.nombre AS 'servicio', a.observacion AS 'observacion', a.comentario AS 'comentario',
	(CASE WHEN a.status = '1' THEN 'Atendido' ELSE 'Pendiente' END) AS 'estatus', CAST(a.fecha_cita AS DATE) AS 'fecha', c.colaborador_id, s.servicio_id
	FROM agenda AS a
	INNER JOIN pacientes AS p ON a.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s ON a.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c ON a.colaborador_id = c.colaborador_id
	$where
	ORDER BY a.hora, a.pacientes_id ASC
	LIMIT $limit, $nroLotes";

$result = $mysqli->query($registro) or die($mysqli->error);

$registros = [];
while ($registro2 = $result->fetch_assoc()) {
    $registros[] = $registro2;
}

$response = [
    'registros' => $registros,
    'pagination' => $lista,
    'total' => $nroProductos
];

echo json_encode($response);

$result->free();
$mysqli->close();