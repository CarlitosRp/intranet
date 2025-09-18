<?php
require_once __DIR__ . '/includes/init.php';

// Borrar datos de sesión
$_SESSION = [];

// Borrar cookie de sesión (si existe)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destruir sesión
session_destroy();

// Nueva sesión “limpia” para flashes
session_start();
flash('success', 'Sesión cerrada correctamente.');

// Redirigir al login
redirect('login.php');
