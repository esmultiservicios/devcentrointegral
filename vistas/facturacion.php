<?php
session_start();
include "../php/funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php');
}

$_SESSION['menu'] = "Facturación";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME
$fecha = date("Y-m-d H:i:s");
$comentario = mb_convert_case("Ingreso al Modulo de Facturación", MB_CASE_TITLE, "UTF-8");

if($colaborador_id != "" || $colaborador_id != null){
   historial_acceso($comentario, $nombre_host, $colaborador_id);
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
    <title>Facturación :: <?php echo SERVEREMPRESA;?></title>
	<?php include("script_css.php"); ?>
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->
<!--INICIO VENTANA MODALES-->
   <?php include("modals/modals.php");?>
<!--FIN VENTANA MODALES-->

<?php include("templates/menu.php"); ?>
<?php include("templates/modals.php"); ?>

<br><br><br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb mt-2 mb-4">
			<li class="breadcrumb-item" id="acciones_atras"><a id="ancla_volver" class="breadcrumb-link" style="text-decoration: none;" href="#"><span id="label_acciones_volver"></a></li>
			<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span></li>
		</ol>
	</nav>

	<div id="main_facturacion">
		<div class="card mb-4" id="main_facturacion">
			<div class="card-body">
				<form class="form-inline" id="form_main">
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
					<div class="form-group mx-sm-3 mb-1">
						<div class="input-group">
							<div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div>Fecha Inicial</span>
							</div>
							<input type="date" required="required" id="fecha_b" name="fecha_b" style="width:160px;" data-toggle="tooltip" data-placement="top" title="Fecha Inicial" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
						</div>
					</div>
					<div class="form-group mx-sm-3 mb-1">
						<div class="input-group">
							<div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div>Fecha Final</span>
							</div>
							<input type="date" required="required" id="fecha_f" name="fecha_f" style="width:160px;" value="<?php echo date ("Y-m-d");?>" data-toggle="tooltip" data-placement="top" title="Fecha Final" class="form-control"/>
						</div>
					</div>
					<div class="form-group mx-sm-3 mb-1">
						<input type="text" placeholder="Buscar por: Expediente, Nombre o Identidad" data-toggle="tooltip" data-placement="top" title="Buscar por: Expediente, Nombre, Apellido o Identidad" id="bs_regis" autofocus class="form-control" size="60"/>
					</div>
          <div class="form-group mt-2 mr-1">
            <button class="btn btn-primary" type="submit" id="factura" data-toggle="tooltip" data-placement="top" title="Crear Factura"><div class="sb-nav-link-icon"></div><i class="fas fa-file-invoice fa-lg"></i> Crear Factura</button>
          </div>
          <div class="form-group mt-2">
              <button class="btn btn-primary" type="submit" id="cierre" data-toggle="tooltip" data-placement="top" title="Cierre de Caja"><div class="sb-nav-link-icon"></div><i class="fas fa-calculator fa-lg"></i> Cierre de Caja</button>
          </div>
				</form>
			</div>
		</div>

		<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-file-invoice mr-1"></i>
				Facturación
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
		</div>
    </div>

    <?php include("templates/factura.php"); ?>
</div>

	<?php include("templates/footer.php"); ?>
	<?php include("templates/footer_facturas.php"); ?>
</div>

    <!-- add javascripts -->
	<?php
		include "script.php";

		include "../js/main.php";
		include "../js/invoice.php";
		include "../js/myjava_facturacion.php";
		include "../js/sms.php";
		include "../js/select.php";
		include "../js/functions.php";
		include "../js/myjava_cambiar_pass.php";
	?>

</body>
</html>
