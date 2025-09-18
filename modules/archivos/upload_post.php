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
$targetDir = safe_join(UPLOADS_PATH, $currentRelDir);
if ($targetDir === false) {
    flash('error', 'Ruta inválida.');
    redirect('modules/archivos/index.php');
}

$maxSize = 10 * 1024 * 1024;
$extMap  = allowed_extensions();

if (empty($_FILES['file']['name'])) {
    flash('error', 'Selecciona un archivo.');
    redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
}

$origName = $_FILES['file']['name'];
$tmpPath  = $_FILES['file']['tmp_name'];
$size     = (int)$_FILES['file']['size'];
$err      = (int)$_FILES['file']['error'];

if ($err !== UPLOAD_ERR_OK) {
    flash('error', 'Error de subida (código ' . $err . ').');
    redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
}
if ($size > $maxSize) {
    flash('error', 'Archivo supera 10 MB.');
    redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
}

$clean = clean_filename($origName);
$ext   = strtolower(pathinfo($clean, PATHINFO_EXTENSION));

if (!isset($extMap[$ext])) {
    flash('error', 'Extensión no permitida.');
    redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
}

// Validar MIME real del archivo temporal
$mime = detect_mime($tmpPath);
$allowedMimes = $extMap[$ext];
if (!in_array($mime, $allowedMimes, true)) {
    flash('error', 'El tipo de archivo no coincide con la extensión declarada.');
    redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
}

$dest = $targetDir . DIRECTORY_SEPARATOR . $clean;
$i = 1;
$baseNoExt = pathinfo($clean, PATHINFO_FILENAME);
while (file_exists($dest)) {
    $alt = $baseNoExt . '_' . $i . '.' . $ext;
    $dest = $targetDir . DIRECTORY_SEPARATOR . $alt;
    $i++;
}

if (@move_uploaded_file($tmpPath, $dest)) {
    flash('success', 'Archivo subido: ' . basename($dest));
} else {
    flash('error', 'No se pudo mover el archivo.');
}

redirect('modules/archivos/index.php?dir=' . urlencode($currentRelDir));
