<?php
require_once __DIR__ . '/../../includes/init.php';
require_any_role(['admin', 'inventarios']);

$token = $_GET['csrf_token'] ?? '';
if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
    flash('error', 'CSRF inválido.');
    redirect('modules/archivos/index.php');
}

$currentRelDir = trim($_GET['dir'] ?? '', "/\\");
$folder        = clean_filename($_GET['folder'] ?? '');

$base = safe_join(UPLOADS_PATH, $currentRelDir);
if ($base === false || $folder === '') {
    flash('error', 'Parámetros inválidos.');
    redirect('modules/archivos/index.php');
}

$target = $base . DIRECTORY_SEPARATOR . $folder;
if (!is_dir($target)) {
    flash('error', 'La carpeta no existe.');
    redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
}

if (!is_dir_empty($target)) {
    flash('error', 'La carpeta no está vacía. (Por seguridad, solo eliminamos carpetas vacías.)');
    redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
}

if (@rmdir($target)) {
    flash('success', 'Carpeta eliminada: ' . $folder);
} else {
    flash('error', 'No se pudo eliminar la carpeta.');
}

redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
