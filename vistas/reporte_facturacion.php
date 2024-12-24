<?php
session_start();
include '../php/funtions.php';

// CONEXION A DB
$mysqli = connect_mysqli();

if (isset($_SESSION['colaborador_id']) == false) {
  header('Location: login.php');
}

$_SESSION['menu'] = 'Reporte de Facturación';

if (isset($_SESSION['colaborador_id'])) {
  $colaborador_id = $_SESSION['colaborador_id'];
} else {
  $colaborador_id = '';
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);  // HOSTNAME
$fecha = date('Y-m-d H:i:s');
$comentario = mb_convert_case('Ingreso al Modulo de Reporte de Facturación', MB_CASE_TITLE, 'UTF-8');

if ($colaborador_id != '' || $colaborador_id != null) {
  historial_acceso($comentario, $nombre_host, $colaborador_id);
}

// OBTENER NOMBRE DE EMPRESA
$usuario = $_SESSION['colaborador_id'];

$query_empresa = "SELECT e.nombre AS 'nombre'
\tFROM users AS u
\tINNER JOIN empresa AS e
\tON u.empresa_id = e.empresa_id
\tWHERE u.colaborador_id = '$usuario'";
$result = $mysqli->query($query_empresa) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();

$empresa = '';

if ($result->num_rows > 0) {
  $empresa = $consulta_registro['nombre'];
}

$mysqli->close();  // CERRAR CONEXIÓN
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="author" content="Script Tutorials" />
    <meta name="description" content="Responsive Websites Orden Hospitalaria de San Juan de Dios">
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reporte de Facturación :: <?php echo $empresa; ?></title>
	<?php include ('script_css.php'); ?>
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->
  <?php include ('templates/modals.php'); ?>

<!--INICIO MODAL-->
<div class="modal fade" id="cobros">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Generar Cargos de Facturación</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">
			<form class="FormularioAjax" id="formCobros" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" name="profesional" id="profesional" class="form-control" placeholder="profesional" required>
					    <input type="hidden" name="colaborador_id" id="colaborador_id" class="form-control" placeholder="Colaborador" required>
					    <input type="hidden" name="fechai" id="fechai" class="form-control" placeholder="Fecha Inicial" required>
					    <input type="hidden" name="fechaf" id="fechaf" class="form-control" placeholder="Fecha Final" required>
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-row" id="grupo_expediente">
					<div class="col-md-4 mb-3">
					  <label for="expedoente">Fecha <span class="priority">*<span/></label>
				      <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" placeholder="Profesional" required readonly="readonly">
					</div>
					<div class="col-md-8 mb-3">
					  <label for="edad">Comentario</label>
					  <input type="text" name="comentario" id="comentario" class="form-control" placeholder="Comentario" required="required">
					</div>
				</div>
			  <div class="modal-footer">
				<button class="btn btn-primary ml-2" type="submit" id="generar"><div class="sb-nav-link-icon"></div><i class="fas fa-calculator fa-lg"></i> Generar</button>
			  </div>
			</form>
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="mensaje_show">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Detalles de Facturación</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">
			<form class="FormularioAjax" id="formCobros" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">
				<div class="form-row">
					<div class="col-md-12 mb-3">
					   <span id="mensaje_mensaje_show"></span>
					</div>
				</div>
			  <div class="modal-footer">
				<button class="btn btn-primary ml-2" type="button" id="okay" data-dismiss="modal"><div class="sb-nav-link-icon" id="okay"></div><i class="fas fa-thumbs-up fa-lg"></i> Okay</button>
				<button class="btn btn-danger ml-2" type="button" id="bad" data-dismiss="modal"><div class="sb-nav-link-icon" id="bad"></div><i class="fas fa-times-circle fa-lg"></i> Okay</button>
			  </div>
			</form>
        </div>
      </div>
    </div>
</div>
   <?php include ('modals/modals.php'); ?>
<!--FIN MODAL-->

   <!--Fin Ventanas Modales-->
	<!--MENU-->
       <?php include ('templates/menu.php'); ?>
    <!--FIN MENU-->

<br><br><br>
<div class="container-fluid">
	<ol class="breadcrumb mt-2 mb-4">
		<li class="breadcrumb-item"><a class="breadcrumb-link" href="inicio.php">Dashboard</a></li>
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Reporte de Facturación</li>
	</ol>

  <div class="card mb-4">
    <div class="card-header">
      <i class="fas fa-search  mr-1"></i>
      Búsqueda
    </div>
    <div class="card-body">
      <form id="form_main_facturacion_reportes" class="form-inline">
        <div class="form-group mr-1">
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text"><div class="sb-nav-link-icon"></div>Profesional</span>
            </div>
            <select id="profesional" name="profesional" class="selectpicker" title="Profesional" data-live-search="true">
            </select>
          </div>
        </div>
        <div class="form-group mr-1">
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text"><div class="sb-nav-link-icon"></div>Cliente</span>
            </div>
            <select id="clientes" name="clientes" class="selectpicker" title="Cliente" data-live-search="true">
            </select>
          </div>
        </div>
        <div class="form-group mr-1">
          <div class="input-group">
            <div class="input-group-append">
            <span class="input-group-text"><div class="sb-nav-link-icon"></div>Estado</span>
            </div>
            <select id="estado" name="estado" class="selectpicker" title="Estado" data-live-search="true">
            </select>
          </div>
        </div>
        <div class="form-group mr-1">
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text"><div class="sb-nav-link-icon"></div>Fecha Inicio</span>
            </div>
            <input type="date" required="required" id="fecha_b" name="fecha_b" style="width:160px;" data-toggle="tooltip" data-placement="top" title="Fecha Inicial" value="<?php
$fecha = date('Y-m-d');

$año = date('Y', strtotime($fecha));
$mes = date('m', strtotime($fecha));
$dia = date('d', mktime(0, 0, 0, $mes + 1, 0, $año));

$dia1 = date('d', mktime(0, 0, 0, $mes, 1, $año));  // PRIMER DIA DEL MES
$dia2 = date('d', mktime(0, 0, 0, $mes, $dia, $año));  // ULTIMO DIA DEL MES

$fecha_inicial = date('Y-m-d', strtotime($año . '-' . $mes . '-' . $dia1));
$fecha_final = date('Y-m-d', strtotime($año . '-' . $mes . '-' . $dia2));

echo $fecha_inicial;
?>" class="form-control"/>
          </div>
        </div>
        <div class="form-group mr-1">
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text"><div class="sb-nav-link-icon"></div>Fecha Fin</span>
            </div>
            <input type="date" required="required" id="fecha_f" name="fecha_f" style="width:160px;" value="<?php echo date('Y-m-d'); ?>" data-toggle="tooltip" data-placement="top" title="Fecha Final" class="form-control"/>
          </div>
        </div>
      </form>
    </div>
    <div class="card-footer small text-muted">

    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
        <i class="fab fa-sellsy mr-1"></i>
        Reporte de Facturación
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id="formPrincipal">
                <div class="col-md-12 mb-3">
                    <table id="dataTableReporteFacturacionMain" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Factura</th>
                                <th>Identidad</th>
                                <th>Cliente</th>
                                <th>Número</th>
                                <th>Importe</th>
                                <th>ISV</th>
                                <th>Descuento</th>
                                <th>Neto</th>
                                <th>Servicio</th>
                                <th>Profesional</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="4"></th>
                                <th colspan="1">Total:</th>
                                <th id="footer-importe"></th>
                                <th id="footer-isv"></th>
                                <th id="footer-descuento"></th>
                                <th id="footer-neto"></th>
                                <th colspan="3"></th>
                            </tr>
                            <tr>
                              <th colspan="2">Detalles de pago</th>    
                              <th colspan="10"></th>
                            </tr>
                            <tr>
                              <th id="tipo_pago"></th>
                              <th id="total_pago"></th>
                              <th colspan="10"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer small text-muted">
    </div>
  </div>
  

  <div class="card mb-4">
    <div class="card-header">
      <i class="fab fa-sellsy mr-1"></i>
      Resultado
    </div>
    <div class="card-body">
      <div class="form-group">
    	  <div class="col-sm-12">
    		    <div class="registros overflow-auto" id="agrega-registros"></div>
    	   </div>
  	  </div>
    	<nav aria-label="Page navigation example">
    		<ul class="pagination justify-content-center" id="pagination"></ul>
    	</nav>
    </div>
    <div class="card-footer small text-muted">

    </div>
  </div>

    <?php include ('templates/footer.php'); ?>
</div>

    <!-- add javascripts -->
	<?php
  include 'script.php';

  include '../js/main.php';
  include '../js/myjava_reportes_facturacion.php';
  include '../js/select.php';
  include '../js/functions.php';
  include '../js/myjava_cambiar_pass.php';
  ?>

</body>
</html>