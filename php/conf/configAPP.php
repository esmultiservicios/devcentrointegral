<?php

// Redirigir a HTTPS si no está en HTTPS
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirectURL);
    exit;
}

// Obtener el protocolo (http o https)
$protocol = 'https://';  // Forzar siempre HTTPS

// Obtener el nombre del servidor
$serverName = $_SERVER['SERVER_NAME'];

// Obtener el puerto si no es el puerto estándar
$port = ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') ? ':' . $_SERVER['SERVER_PORT'] : '';

// Obtener la ruta base
$basePath = $serverName == 'localhost' ? '/devcentrointegral/' : '/';

// Construir la URL base
$baseURL = $protocol . $serverName . $port . $basePath;

// Definir la constante SERVERURL
define('SERVERURL', $baseURL);

// Otras constantes
define('SERVEREMPRESA', 'CAMI');
define('SERVER', 'localhost');
define('DB', 'clinicarehn_centrointegral');
define('USER', 'clinicarehn_clinicare');
define('PASS', 'Clin1c@r32022#');

define('SERVER_MAIN', 'localhost');
define('DB_MAIN', 'clinicarehn_clinicare');
define('USER_MAIN', 'clinicarehn_clinicare');
define('PASS_MAIN', 'Clin1c@r32022#');

define('METHOD', 'AES-256-CBC');
define('SECRET_KEY', '$DP_@2020');
define('SECRET_IV', '10172');
define('SISTEMA_PRUEBA', 'NO');  // SI o NO
