<?php
require_once __DIR__ . '/../../../includes/init.php';
require_any_role(['admin', 'inventarios']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('modules/uniformes/entradas/index.php');
}
$token = $_POST['csrf_token'] ?? '';
if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
    flash('error', 'CSRF inválido.');
    redirect('modules/uniformes/entradas/index.php');
}

$fecha = trim($_POST['fecha'] ?? '');
$proveedor = trim($_POST['proveedor'] ?? '');
$factura = trim($_POST['factura'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');
$id_vars = $_POST['id_variante'] ?? [];
$cants  = $_POST['cantidad'] ?? [];

if (!$fecha || !$proveedor || !$factura) {
    flash('error', 'Completa Fecha, Proveedor y Factura.');
    redirect('modules/uniformes/entradas/nueva.php');
}
if (empty($id_vars) || empty($cants) || count($id_vars) !== count($cants)) {
    flash('error', 'Debes capturar al menos un renglón válido.');
    redirect('modules/uniformes/entradas/nueva.php');
}

// Normalizar detalle y validar cantidades
$detalle = [];
for ($i = 0; $i < count($id_vars); $i++) {
    $vid = (int)$id_vars[$i];
    $qty = (int)$cants[$i];
    if ($vid > 0 && $qty > 0) {
        $detalle[] = ['id_variante' => $vid, 'cantidad' => $qty];
    }
}
if (empty($detalle)) {
    flash('error', 'Las cantidades deben ser enteros positivos.');
    redirect('modules/uniformes/entradas/nueva.php');
}

$conn = db_connect();
mysqli_begin_transaction($conn);

try {
    // Insert cabecera
    $stmt = mysqli_prepare($conn, "INSERT INTO entradas (fecha, proveedor, factura, observaciones, creado_por) VALUES (?,?,?,?,?)");
    $creado_por = $_SESSION['user']['name'] ?? 'sistema';
    mysqli_stmt_bind_param($stmt, "sssss", $fecha, $proveedor, $factura, $observaciones, $creado_por);
    mysqli_stmt_execute($stmt);
    $id_entrada = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Validar variantes existentes y guardar detalle
    $stmtV = mysqli_prepare($conn, "SELECT id_variante FROM item_variantes WHERE id_variante = ? AND activo = 1");
    $stmtD = mysqli_prepare($conn, "INSERT INTO entradas_detalle (id_entrada, id_variante, cantidad) VALUES (?,?,?)");

    foreach ($detalle as $line) {
        $vid = $line['id_variante'];
        $qty = $line['cantidad'];
        mysqli_stmt_bind_param($stmtV, "i", $vid);
        mysqli_stmt_execute($stmtV);
        $vr = mysqli_stmt_get_result($stmtV);
        if (!mysqli_fetch_assoc($vr)) throw new Exception('Variante inválida: ' . $vid);

        mysqli_stmt_bind_param($stmtD, "iii", $id_entrada, $vid, $qty);
        mysqli_stmt_execute($stmtD);
    }
    mysqli_stmt_close($stmtV);
    mysqli_stmt_close($stmtD);

    mysqli_commit($conn);
    mysqli_close($conn);

    flash('success', 'Entrada guardada (#' . $id_entrada . ')');
    redirect('modules/uniformes/entradas/ver.php?id=' . $id_entrada);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    mysqli_close($conn);
    flash('error', 'No se pudo guardar: ' . $e->getMessage());
    redirect('modules/uniformes/entradas/nueva.php');
}
