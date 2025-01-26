<?php
session_start(); 
include "../php/funtions.php";

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}    

$_SESSION['menu'] = "Atenciones Medicas";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s"); 
$comentario = mb_convert_case("Ingreso al Modulo de Atenciones Medicas", MB_CASE_TITLE, "UTF-8");   

if($colaborador_id != "" || $colaborador_id != null){
   historial_acceso($comentario, $nombre_host, $colaborador_id);  
} 
 ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="author" content="Script Tutorials" />
    <meta name="description" content="Responsive Websites Orden Hospitalaria de San Juan de Dios">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Atenciones Medicas :: <?php echo SERVEREMPRESA;?></title>
    <?php include("script_css.php"); ?>
</head>

<body>
    <!--Ventanas Modales-->
    <!-- Small modal -->
    <?php include("templates/modals.php"); ?>

    <!--MODAL BUSCAR ATENCIONES-->
    <div class="modal fade" id="buscar_atencion">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Búsqueda de Atenciones</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="FormularioAjax" id="formulario_buscarAtencion" data-async data-target="#rating-modal"
                        action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <input type="hidden" id="atencion_id" name="atencion_id" class="form-control"
                                    required="required">
                                <input type="hidden" id="pacientes_id" name="pacientes_id" class="form-control"
                                    required="required">
                            </div>
                        </div>
                        <div class="form-row" id="grupo_expediente">
                            <div class="col-md-12 mb-3">
                                <input type="text" name="busqueda" id="busqueda"
                                    placeholder="Buscar por: Nombre, Apellido o Identidad" data-toggle="tooltip"
                                    data-placement="top"
                                    title="Búsqueda de Atenciones por: Nombre, Apellido o Identidad"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <div class="registros overflow-auto" id="agrega_registros_busqueda"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center" id="pagination_busqueda"></ul>
                                </nav>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <div class="registros overflow-auto" id="agrega_registros_busqueda_"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center" id="pagination_busqueda_"></ul>
                                </nav>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    <!-- FIN MODAL BUSCAR ATENCIONES

<!--INICIO MODAL TRANSITO-->
    <div class="modal fade" id="registro_transito_eviada">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transito Enviada</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="FormularioAjax" id="formulario_transito_enviada" data-async data-target="#rating-modal"
                        action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <input type="hidden" id="pacientes_id" name="pacientes_id" class="form-control"
                                    required="required">
                                <input type="hidden" id="colaborador_id" name="colaborador_id" class="form-control"
                                    required="required">
                                <div class="input-group mb-3">
                                    <input type="text" required readonly id="pro" name="pro" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="paciente_te">Paciente <span class="priority">*<span /></label>
                                <div class="input-group mb-3">
                                    <select class="selectpicker" id="paciente_te" name="paciente_te"
                                        data-live-search="true" title="Paciente" data-width="100%" data-size="7">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha">Fecha <span class="priority">*<span /></label>
                                <input type="date" required id="fecha" name="fecha" value="<?php echo date ("Y-m-d");?>"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="identidad">Identidad</label>
                                <input type="text" name="identidad" id="identidad" placeholder="Identidad" readonly
                                    class="form-control" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="enviada">Enviada a <span class="priority">*<span /></label>
                                <div class="input-group mb-3">
                                    <select class="selectpicker" id="enviada" name="enviada" data-live-search="true"
                                        title="Enviada a" data-width="100%" data-size="7">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="motivo">Motivo <span class="priority">*<span /></label>
                                <textarea id="motivo" name="motivo" required placeholder="Motivo de la Referencia"
                                    class="form-control" maxlength="255" rows="3"></textarea>
                                <p id="charNumMotivoTE">255 Caracteres</p>
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary ml-2" form="formulario_transito_enviada" type="submit"
                        id="reg_transitoe">
                        <div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registro_transito_recibida">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transito Recibida</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="FormularioAjax" id="formulario_transito_recibida" data-async
                        data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off"
                        enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <input type="hidden" id="pacientes_id" name="pacientes_id" class="form-control"
                                    required="required">
                                <input type="hidden" id="colaborador_id" name="colaborador_id" class="form-control"
                                    required="required">
                                <div class="input-group mb-3">
                                    <input type="text" required readonly id="pro" name="pro" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="paciente_tr">Paciente <span class="priority">*<span /></label>
                                <div class="input-group mb-3">
                                    <select class="selectpicker" id="paciente_tr" name="paciente_tr"
                                        data-live-search="true" title="Paciente" data-width="100%" data-size="7">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha">Fecha <span class="priority">*<span /></label>
                                <input type="date" required id="fecha" name="fecha" value="<?php echo date ("Y-m-d");?>"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="identidad">Identidad</label>
                                <input type="text" name="identidad" id="identidad" placeholder="Identidad" readonly
                                    class="form-control" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="recibida">Recibida de <span class="priority">*<span /></label>
                                <div class="input-group mb-3">
                                    <select class="selectpicker" id="recibida" name="recibida" data-live-search="true"
                                        title="Recibida de" data-width="100%" data-size="7">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="motivo">Motivo <span class="priority">*<span /></label>
                                <textarea id="motivo" name="motivo" required placeholder="Motivo de la Referencia"
                                    class="form-control" maxlength="255" rows="3"></textarea>
                                <p id="charNumMotivoTE">255 Caracteres</p>
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary ml-2" form="formulario_transito_recibida" type="submit"
                        id="reg_transitor">
                        <div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar
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
            <li class="breadcrumb-item" id="acciones_atras"><a id="ancla_volver" class="breadcrumb-link"
                    href="#">Atenciones Medicas</a></li>
            <li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span></li>
        </ol>

        <div id="main_facturacion">
            <form class="form-inline" id="form_main">
                <div class="form-group mr-1">
                    <div class="input-group">
                        <select class="selectpicker" id="estado" name="estado" data-live-search="true" title="Atención"
                            data-width="100%" data-size="7">
                        </select>
                    </div>
                </div>
                <div class="form-group mr-1">
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <div class="sb-nav-link-icon"></div>Fecha Inicio
                            </span>
                        </div>
                        <input type="date" required="required" id="fecha_b" name="fecha_b" style="width:160px;"
                            value="<?php echo date ("Y-m-d");?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group mr-1">
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <div class="sb-nav-link-icon"></div>Fecha Fin
                            </span>
                        </div>
                        <input type="date" required="required" id="fecha_f" name="fecha_f" style="width:160px;"
                            value="<?php echo date ("Y-m-d");?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group mr-1">
                    <input type="text" placeholder="Buscar por: Expediente, Nombre o Identidad" data-toggle="tooltip"
                        data-placement="top" title="Buscar por: Expediente, Nombre, Apellido o Identidad" id="bs_regis"
                        autofocus class="form-control" size="40" />
                </div>
                <div class="form-group">
                    <act class="form-group mr-1">
                        <button class="btn btn-primary ml-1" type="submit" id="nuevo_registro">
                            <div class="sb-nav-link-icon"></div><i class="fas fa-plus-circle fa-lg"></i> Generar
                            Atención
                        </button>
                </div>
                <div class="form-group mr-1">
                    <button class="btn btn-primary ml-1" type="submit" id="nueva_receta_medica">
                        <div class="sb-nav-link-icon"></div><i class="fas fa-prescription-bottle-alt fa-lg"></i> Receta Medica
                    </button>
                </div>

                <div class="form-group mr-1">
                    <button class="btn btn-primary ml-1" type="submit" id="nueva_factura">
                        <div class="sb-nav-link-icon"></div><i class="fas fa-file-invoice fa-lg"></i> Pre Factura
                    </button>
                </div>
                <div class="dropdown show mr-1">
                    <a class="btn btn-primary dropdown-toggle ml-1" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-plus-circle fa-lg"></i> Transito Pacientes
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="#" id="transito_enviada">Transito Enviada</a>
                        <a class="dropdown-item" href="#" id="transito_recibida">Transito Recibida</a>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-success ml-1" type="submit" id="historial">
                        <div class="sb-nav-link-icon"></div><i class="fas fa-search fa-lg"></i> Buscar
                    </button>
                </div>
            </form>
            <hr />
            <div class="form-group">
                <div class="col-sm-12">
                    <div class="registros overflow-auto" id="agrega-registros"></div>
                </div>
            </div>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center" id="pagination"></ul>
            </nav>
        </div>

        <div class="card recetaMedica" style="display:none;">
            <div class="card-header text-white bg-info mb-3" style="text-align: center">
                Receta Médica
            </div>
            <div class="card-body">
                <div>
                    <form id="form_receta" autocomplete="off">
                        <input type="hidden" id="receta_pacientes_id" name="receta_pacientes_id" class="form-control" />
                        <input type="hidden" id="receta_colaboradorId" name="receta_colaboradorId" class="form-control" />
                        <input type="hidden" id="receta_servicioId" name="receta_servicioId" class="form-control" />
                        <input type="hidden" id="receta_pacienteNombre" name="receta_pacienteNombre" class="form-control" />

                        <div class="form-group">
                            <span id="datos_paciente"></span>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Registrar
                            </button>
                        </div>
                                        
                        <div class="form-group" id="grupo_paciente_receta">
                            <select class="form-control selectpicker" id="receta_select_pacientes_id" name="receta_select_pacientes_id" data-size="7" data-live-search="true" title="Seleccione un paciente">
                                <!-- Las opciones se llenarán dinámicamente con JavaScript -->
                            </select>
                        </div>
                        <div class="form-group">
                            <table class="table table-bordered" id="tablaReceta">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Descripción</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" id="agregarFila">
                                <i class="fas fa-plus-circle"></i> Agregar Fila
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include("templates/atencionMedicaHorizontal.php"); ?>
        <?php include("templates/factura.php"); ?>
        <?php include("templates/footer.php"); ?>
        <?php include("templates/footer_facturas.php"); ?>
    </div>

    <!-- add javascripts -->
    <?php 
		include "script.php"; 
		
		include "../js/main.php"; 
		include "../js/invoice.php"; 	
		include "../js/myjava_atencion_medica.php";		
		include "../js/select.php"; 	
		include "../js/functions.php"; 
		include "../js/myjava_cambiar_pass.php"; 		
	?>

</body>
</html>