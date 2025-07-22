<?php
session_start(); 
include('../php/funtions.php');

// Verificación de sesión
if(!isset($_SESSION['colaborador_id'])) {
   header('Location: login.php'); 
}    

// Configuración de página
$page_title = isset($page_title) ? $page_title : "Dashboard";
$_SESSION['menu'] = $page_title;

// Registro de acceso
$nombre_host = getRealIP();     
$fecha = date("Y-m-d H:i:s"); 
$comentario = mb_convert_case("Ingreso al Modulo de ".$page_title, MB_CASE_TITLE, "UTF-8");   

if(isset($_SESSION['colaborador_id']) && $_SESSION['colaborador_id'] != ""){
   historial_acceso($comentario, $nombre_host, $_SESSION['colaborador_id']);  
}

// Cerrar conexión
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema Hospitalario">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $page_title; ?> | <?php echo SERVEREMPRESA; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo SERVERURL; ?>img/logo_icono.png">
    
    <!-- Bootstrap CSS local -->
    <link href="<?php echo SERVERURL; ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome local -->
    <link rel="stylesheet" href="<?php echo SERVERURL; ?>fontawesome/css/all.min.css">
    
    <!-- DataTables CSS local -->
    <link href="<?php echo SERVERURL; ?>bootstrap/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Bootstrap Select CSS local -->
    <link href="<?php echo SERVERURL; ?>bootstrap/css/bootstrap-select.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo SERVERURL; ?>css/style.css" rel="stylesheet">
    <link href="<?php echo SERVERURL; ?>css/dashboard.css" rel="stylesheet">
    
    <!-- jQuery local -->
    <script src="<?php echo SERVERURL; ?>js/query/jquery-3.5.1.min.js"></script>
</head>
<body class="sb-nav-fixed">
    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    
    <!-- Top Navigation -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary">
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="<?php echo SERVERURL; ?>vistas/inicio.php">
            <img src="<?php echo SERVERURL; ?>img/cami_logo_menu.svg" alt="Logo" height="30">
        </a>
        
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Buscar..." aria-label="Buscar..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-light" type="button" id="btnNavbarSearch">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
        
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i> <?php echo $_SESSION['nombre']; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/perfil.php">Perfil</a></li>
                    <li><a class="dropdown-item" href="#" id="mostrar_cambiar_contraseña">Cambiar Contraseña</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="#" id="salir_sistema">Cerrar Sesión</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    
    <!-- Sidebar Navigation -->
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include("templates/sidebar.php"); ?>
        </div>
        
        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <!-- Breadcrumbs -->
                    <nav aria-label="breadcrumb" class="mt-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="inicio.php">Dashboard</a></li>
                            <?php if(isset($current_page)): ?>
                            <li class="breadcrumb-item active"><?php echo $current_page; ?></li>
                            <?php endif; ?>
                        </ol>
                    </nav>