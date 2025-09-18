<?php
require_once __DIR__ . '/../../includes/init.php';
require_any_role(['admin', 'inventarios']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('modules/archivos/index.php');
}
$token = $_POST['csrf_token'] ?? '';
if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
    flash('error', 'CSRF inválido.');
    redirect('modules/archivos/index.php');
}

$currentRelDir = trim($_POST['dir'] ?? '', "/\\");
$folder = clean_filename($_POST['folder'] ?? '');

$targetBase = safe_join(UPLOADS_PATH, $currentRelDir);
if ($targetBase === false) {
    flash('error', 'Ruta inválida.');
    redirect('modules/archivos/index.php');
}

if ($folder === '') {
    flash('error', 'Nombre de carpeta inválido.');
    redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
}

$newDir = $targetBase . DIRECTORY_SEPARATOR . $folder;
if (is_dir($newDir)) {
    flash('error', 'La carpeta ya existe.');
} else {
    if (@mkdir($newDir, 0775, false)) {
        flash('success', 'Carpeta creada: ' . $folder);
    } else {
        flash('error', 'No se pudo crear la carpeta.');
    }
}

redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
