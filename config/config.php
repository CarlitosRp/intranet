<?php
// config/config.php
// Configuración global y rutas del proyecto (modo desarrollo).

// 1) Nombre del sitio
define('SITE_NAME', 'Intranet');

// 2) Rutas base
define('ROOT_PATH', 'C:\\xampp\\htdocs\\intranet');  // físico
define('BASE_URL',  'http://localhost/intranet');    // web

// 3) Rutas de assets (dependen de BASE_URL)
define('ASSETS_URL', BASE_URL . '/assets');
define('CSS_URL',    ASSETS_URL . '/css');
define('JS_URL',     ASSETS_URL . '/js');

// Rutas para uploads
define('UPLOADS_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads');
define('UPLOADS_URL',  BASE_URL  . '/uploads');


// 4) Zona horaria
date_default_timezone_set('America/Hermosillo');

// 5) Modo desarrollo
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
