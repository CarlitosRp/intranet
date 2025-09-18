<?php
require_once __DIR__ . '/../../includes/init.php';
require_any_role(['admin', 'inventarios']);

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
    redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
}

// Forzar descarga controlada
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($full) . '"');
header('Content-Length: ' . filesize($full));
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
readfile($full);
exit;
