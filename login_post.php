<?php
require_once __DIR__ . '/includes/init.php';

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// 1) CSRF
$token = $_POST['csrf_token'] ?? '';
if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
    flash('error', 'Token CSRF inválido. Refresca la página e inténtalo otra vez.');
    redirect('login.php');
}

// 2) Datos
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Correo inválido.');
    redirect('login.php');
}
if ($password === '') {
    flash('error', 'La contraseña es requerida.');
    redirect('login.php');
}

// 3) Intentar login
if (login($email, $password)) {
    // Regenerar token de sesión al iniciar sesión
    session_regenerate_id(true);
    $u = current_user();
    flash('success', '¡Bienvenido, ' . $u['name'] . '!');
    redirect('index.php'); // o a un dashboard
} else {
    flash('error', 'Credenciales incorrectas o usuario inactivo.');
    redirect('login.php');
}
