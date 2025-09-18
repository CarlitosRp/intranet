<?php
require_once __DIR__ . '/../../includes/init.php';
require_any_role(['admin', 'inventarios']);

$token = $_GET['csrf_token'] ?? '';
if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
    flash('error', 'CSRF inválido.');
    redirect('modules/archivos/index.php');
}

$currentRelDir = trim($_GET['dir'] ?? '', "/\\");
$file = clean_filename($_GET['file'] ?? '');

$targetDir = safe_join(UPLOADS_PATH, $currentRelDir);
if ($targetDir === false || $file === '') {
    flash('error', 'Parámetros inválidos.');
    redirect('modules/archivos/index.php');
}

$full = $targetDir . DIRECTORY_SEPARATOR . $file;
if (!is_file($full)) {
    flash('error', 'Archivo no encontrado.');
} else {
    if (@unlink($full)) {
        flash('success', 'Archivo eliminado: ' . $file);
    } else {
        flash('error', 'No se pudo eliminar el archivo.');
    }
}

redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
