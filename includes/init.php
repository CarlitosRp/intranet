<?php
// includes/init.php
// Arranque central: carga config, enciende sesión, helpers y utilidades básicas.

// 1) Cargar configuración global
require_once __DIR__ . '/../config/config.php';

// 2) Iniciar la sesión (una sola vez)
if (session_status() === PHP_SESSION_NONE) {
    // Nombre de cookie legible y único para tu app
    session_name('intranet_session');
    session_start();

    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');
}

// 3) Cargar funciones de apoyo (helpers)
require_once __DIR__ . '/helpers.php';

// 4) Crear un token CSRF sencillo (lo usaremos en formularios más adelante)
if (empty($_SESSION['csrf_token'])) {
    // 32 bytes aleatorios -> 64 caracteres hex
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 5) (Opcional) Ajustes de zona horaria y errores ya vienen de config.php.
//    Aquí sólo podríamos añadir logs u opciones adicionales si hiciera falta.
require_once __DIR__ . '/auth.php';
