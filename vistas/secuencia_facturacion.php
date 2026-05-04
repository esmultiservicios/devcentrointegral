<?php
session_start();
include "../php/funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php');
}

$_SESSION['menu'] = "Secuencia de Facturación";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME
$fecha = date("Y-m-d H:i:s");
$comentario = mb_convert_case("Ingreso al Modulo Secuencia de Facturación", MB_CASE_TITLE, "UTF-8");

if($colaborador_id != "" || $colaborador_id != null){
   historial_acceso($comentario, $nombre_host, $colaborador_id);
}

//OBTENER NOMBRE DE EMPRESA
$usuario = $_SESSION['colaborador_id'];

$query_empresa = "SELECT e.nombre AS 'nombre'
	FROM users AS u
	INNER JOIN empresa AS e
	ON u.empresa_id = e.empresa_id
	WHERE u.colaborador_id = '$usuario'";
$result = $mysqli->query($query_empresa) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();

$empresa = '';

if($result->num_rows>0){
  $empresa = $consulta_registro['nombre'];
}

$mysqli->close();//CERRAR CONEXIÓN
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
    <title>Secuencia de Facturación :: <?php echo $empresa; ?></title>
	<?php include("script_css.php"); ?>
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->
  <?php include("templates/modals.php"); ?>

<!--INICIO MODAL-->
<div class="modal fade" id="secuenciaFacturacion">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content modal-secuencia-content">

			<div class="modal-header modal-secuencia-header">
				<div>
					<h4 class="modal-title mb-0">
						<i class="fas fa-receipt mr-2"></i> Secuencia de Facturación
					</h4>
					<small class="text-muted">
						Configure el CAI, prefijo, rango autorizado y control de numeración fiscal.
					</small>
				</div>

				<button type="button" class="close" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>

			<div class="modal-body">

				<form class="FormularioAjax" id="formularioSecuenciaFacturacion" method="POST" enctype="multipart/form-data">

					<input type="hidden" id="secuencia_facturacion_id" name="secuencia_facturacion_id">

					<div class="alert alert-warning d-none" id="alertaSecuenciaFacturas">
						<div class="d-flex align-items-start">
							<div class="mr-3">
								<i class="fas fa-lock fa-lg"></i>
							</div>
							<div>
								<strong>Secuencia protegida por facturación emitida.</strong>
								<br>
								Esta secuencia ya tiene facturas asociadas. Por seguridad fiscal, solo puede modificar la
								<strong>Fecha Límite</strong> y el <strong>Estado</strong>.
							</div>
						</div>
					</div>

					<div class="card card-secuencia mb-3">
						<div class="card-header card-secuencia-title">
							<i class="fas fa-cog mr-2"></i> Modo de operación
						</div>

						<div class="card-body">
							<div class="form-row">
								<div class="col-md-12">
									<label class="label-form">Proceso actual</label>
									<div class="input-group">
										<input type="text" readonly id="pro" name="pro" class="form-control" placeholder="Proceso">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="fa fa-plus-square"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Este campo indica si está registrando, modificando o eliminando una secuencia.
									</small>
								</div>
							</div>
						</div>
					</div>

					<div class="card card-secuencia mb-3">
						<div class="card-header card-secuencia-title">
							<i class="fas fa-building mr-2"></i> Datos fiscales principales
						</div>

						<div class="card-body">
							<div class="form-row">

								<div class="col-md-4 mb-3">
									<label class="label-form">Empresa <span class="text-danger">*</span></label>
									<select class="selectpicker form-control" id="empresa" name="empresa"
										required data-live-search="true" title="Seleccione empresa">
									</select>
									<small class="form-text text-muted">
										Empresa a la que pertenece esta secuencia fiscal.
									</small>
								</div>

								<div class="col-md-4 mb-3">
									<label class="label-form">Tipo de Documento <span class="text-danger">*</span></label>
									<div class="input-group">
										<select class="selectpicker form-control" id="documento_id" name="documento_id"
											required data-live-search="true" title="Seleccione documento">
										</select>
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="fas fa-file-alt"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Ejemplo: Factura, Nota de Crédito, Recibo u otro documento fiscal.
									</small>
								</div>

								<div class="col-md-4 mb-3 campo-controlado">
									<label class="label-form">CAI</label>
									<div class="input-group">
										<input type="text" name="cai" id="cai" class="form-control" placeholder="Ejemplo: XXXX-XXXX-XXXX-XXXX">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="far fa-id-card"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Código de Autorización de Impresión otorgado para esta secuencia.
									</small>
								</div>

							</div>
						</div>
					</div>

					<div class="card card-secuencia mb-3">
						<div class="card-header card-secuencia-title">
							<i class="fas fa-list-ol mr-2"></i> Configuración de numeración
						</div>

						<div class="card-body">
							<div class="form-row">

								<div class="col-md-4 mb-3 campo-controlado">
									<label class="label-form">Prefijo</label>
									<div class="input-group">
										<input type="text" name="prefijo" id="prefijo" class="form-control" placeholder="Ejemplo: 000-001-01-">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="fas fa-code"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Parte inicial fija del número de factura.
									</small>
								</div>

								<div class="col-md-4 mb-3 campo-controlado">
									<label class="label-form">Relleno <span class="text-danger">*</span></label>
									<div class="input-group">
										<input type="number" name="relleno" id="relleno" class="form-control" required placeholder="Ejemplo: 8">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="fas fa-hashtag"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Cantidad de dígitos del correlativo. Ejemplo: 8 produce 00000001.
									</small>
								</div>

								<div class="col-md-4 mb-3 campo-controlado">
									<label class="label-form">Incremento <span class="text-danger">*</span></label>
									<div class="input-group">
										<input type="number" name="incremento" id="incremento" class="form-control" required placeholder="Ejemplo: 1">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="fas fa-arrow-up"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Normalmente debe ser 1 para avanzar de forma consecutiva.
									</small>
								</div>

							</div>

							<div class="form-row">

								<div class="col-md-4 mb-3 campo-controlado">
									<label class="label-form">Siguiente <span class="text-danger">*</span></label>
									<div class="input-group">
										<input type="number" name="siguiente" id="siguiente" class="form-control" required placeholder="Ejemplo: 1">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="fas fa-forward"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Número que se usará en la próxima factura.
									</small>
								</div>

								<div class="col-md-4 mb-3 campo-controlado">
									<label class="label-form">Rango Inicial <span class="text-danger">*</span></label>
									<div class="input-group">
										<input type="text" name="rango_inicial" id="rango_inicial" class="form-control" required placeholder="Ejemplo: 00000001">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="fas fa-sort-numeric-down"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Primer número autorizado dentro del rango fiscal.
									</small>
								</div>

								<div class="col-md-4 mb-3 campo-controlado">
									<label class="label-form">Rango Final <span class="text-danger">*</span></label>
									<div class="input-group">
										<input type="number" name="rango_final" id="rango_final" class="form-control" required placeholder="Ejemplo: 00008700">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="fas fa-sort-numeric-up"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Último número autorizado para esta secuencia.
									</small>
								</div>

							</div>
						</div>
					</div>

					<div class="card card-secuencia mb-0">
						<div class="card-header card-secuencia-title">
							<i class="far fa-calendar-alt mr-2"></i> Vigencia y estado
						</div>

						<div class="card-body">
							<div class="form-row">

								<div class="col-md-4 mb-3 campo-controlado">
									<label class="label-form">Fecha Activación <span class="text-danger">*</span></label>
									<div class="input-group">
										<input type="date" id="fecha_activacion" name="fecha_activacion"
											value="<?php echo date('Y-m-d');?>" class="form-control" required>
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="far fa-calendar-check"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Fecha desde la cual la secuencia puede utilizarse.
									</small>
								</div>

								<div class="col-md-4 mb-3">
									<label class="label-form">Fecha Límite <span class="text-danger">*</span></label>
									<div class="input-group">
										<input type="date" id="fecha_limite" name="fecha_limite"
											value="<?php echo date('Y-m-d');?>" class="form-control" required>
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="far fa-calendar-times"></i>
											</span>
										</div>
									</div>
									<small class="form-text text-muted">
										Fecha máxima permitida para emitir documentos con esta autorización.
									</small>
								</div>

								<div class="col-md-4 mb-3">
									<label class="label-form">Estado <span class="text-danger">*</span></label>

									<div class="estado-radio-group" id="estado_group">
										<label class="estado-option estado-activo">
											<input type="radio" name="estado" id="estado_activo" value="1" required>
											<span>
												<i class="fas fa-check-circle"></i>
												Activo
											</span>
										</label>

										<label class="estado-option estado-inactivo">
											<input type="radio" name="estado" id="estado_inactivo" value="2" required>
											<span>
												<i class="fas fa-ban"></i>
												Inactivo
											</span>
										</label>
									</div>

									<small class="form-text text-muted">
										Use activo para permitir facturación; inactivo para bloquear su uso.
									</small>
								</div>

							</div>
						</div>
					</div>

				</form>
			</div>

			<div class="modal-footer modal-secuencia-footer">
				<button class="btn btn-primary" form="formularioSecuenciaFacturacion" type="submit" id="reg">
					<i class="far fa-save"></i> Registrar
				</button>

				<button class="btn btn-warning" form="formularioSecuenciaFacturacion" type="submit" id="edi">
					<i class="fas fa-edit"></i> Modificar
				</button>

				<button class="btn btn-danger" form="formularioSecuenciaFacturacion" type="submit" id="delete">
					<i class="fas fa-trash"></i> Eliminar
				</button>
			</div>

		</div>
	</div>
</div>

   <?php include("modals/modals.php");?>

   <!--Fin Ventanas Modales-->
	<!--MENU-->
       <?php include("templates/menu.php"); ?>
    <!--FIN MENU-->

<br><br><br>
<div class="container-fluid">
	<ol class="breadcrumb mt-2 mb-4">
		<li class="breadcrumb-item"><a class="breadcrumb-link" href="inicio.php">Dashboard</a></li>
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Secuencia de Facturación</li>
	</ol>

    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-search  mr-1"></i>
        Búsqueda
      </div>
      <div class="card-body">
        <form id="form_main" class="form-inline">
          <div class="form-group mr-1">
            <div class="input-group">
              <div class="input-group-append">
                <span class="input-group-text"><div class="sb-nav-link-icon"></div>Empresa</span>
              </div>
              <select id="empresa" name="empresa" class="selectpicker" title="Empresa" data-live-search="true">
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
              <input type="date" required="required" id="fechaf" name="fechaf" style="width: 159px;" value="<?php echo date ("Y-m-d");?>" data-toggle="tooltip" data-placement="top" title="Fecha Inicial" class="form-control"/>
            </div>
          </div>
          <div class="form-group mr-1">
            <button class="btn btn-primary ml-2" type="submit" id="nuevo_registro"><div class="sb-nav-link-icon"></div><i class="fas fa-plus-circle fa-lg"></i> Crear</button>
          </div>
        </form>
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

	</div>
	<?php include("templates/factura.php"); ?>
	<?php include("templates/footer.php"); ?>
</div>

    <!-- add javascripts -->
	<?php
		include "script.php";

		include "../js/main.php";
		include "../js/myjava_secuencia_facturacion.php";
		include "../js/select.php";
		include "../js/functions.php";
		include "../js/myjava_cambiar_pass.php";
	?>

</body>
</html>
