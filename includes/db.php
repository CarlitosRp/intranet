<?php
// includes/db.php
// Manejo de la conexión a MySQL (procedimental con mysqli).

// 1) Configuración de conexión (ajústala a tu entorno XAMPP).
$db_host = 'localhost';   // Servidor de BD
$db_user = 'root';        // Usuario por defecto en XAMPP
$db_pass = '';            // Contraseña (vacía en XAMPP por defecto)
$db_name = 'intranet';    // Nombre de la base de datos (la crearemos después)

// 2) Función de conexión
function db_connect()
{
    global $db_host, $db_user, $db_pass, $db_name;

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if (!$conn) {
        die('Error de conexión a MySQL: ' . mysqli_connect_error());
    }

    // Opcional: forzar UTF-8 para acentos y eñes
    mysqli_set_charset($conn, 'utf8mb4');

    return $conn;
}
