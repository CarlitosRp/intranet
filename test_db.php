<?php
require_once __DIR__ . '/includes/db.php';

// Intentar conectar
$conn = db_connect();

echo "<h2>Conexión exitosa a la base de datos ✅</h2>";

mysqli_close($conn);
