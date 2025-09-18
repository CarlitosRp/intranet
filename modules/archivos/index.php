<?php
require_once __DIR__ . '/../../includes/init.php';
require_any_role(['admin', 'inventarios']);

$currentRelDir = trim($_GET['dir'] ?? '', "/\\"); // ruta relativa dentro de uploads
$basePath = UPLOADS_PATH;

// Resolver ruta física segura
$targetPath = safe_join($basePath, $currentRelDir);
if ($targetPath === false) {
    flash('error', 'Ruta inválida.');
    redirect('modules/archivos/index.php');
}

// Asegurar que existe la carpeta base/actual
if (!is_dir($targetPath)) {
    @mkdir($targetPath, 0775, true);
}

// Cargar contenido del directorio
$items = [];
$handle = opendir($targetPath);
if ($handle) {
    while (($entry = readdir($handle)) !== false) {
        if ($entry === '.' || $entry === '..') continue;
        $full = $targetPath . DIRECTORY_SEPARATOR . $entry;
        $items[] = [
            'name' => $entry,
            'is_dir' => is_dir($full),
            'size' => is_file($full) ? filesize($full) : 0,
            'mtime' => filemtime($full) ?: time(),
        ];
    }
    closedir($handle);
}
// Ordenar: carpetas primero, luego archivos por nombre
usort($items, function ($a, $b) {
    if ($a['is_dir'] && !$b['is_dir']) return -1;
    if (!$a['is_dir'] && $b['is_dir']) return 1;
    return strcasecmp($a['name'], $b['name']);
});

// Construir breadcrumbs
$crumbs = [];
$accum = '';
if ($currentRelDir !== '') {
    foreach (explode('/', str_replace('\\', '/', $currentRelDir)) as $part) {
        $accum = $accum === '' ? $part : ($accum . '/' . $part);
        $crumbs[] = ['label' => $part, 'dir' => $accum];
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<h2 class="h4 mb-3">Gestión de Archivos</h2>
<p class="text-muted">Carpeta actual: <code>/uploads/<?php echo h($currentRelDir ?: ''); ?></code></p>

<!-- Breadcrumbs -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo url('modules/archivos/index.php'); ?>">uploads</a></li>
        <?php foreach ($crumbs as $c): ?>
            <li class="breadcrumb-item">
                <a href="<?php echo url('modules/archivos/index.php?dir=' . urlencode($c['dir'])); ?>">
                    <?php echo h($c['label']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>

<div class="row g-3">
    <!-- Crear carpeta -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Crear carpeta</div>
            <div class="card-body">
                <form action="<?php echo url('modules/archivos/mkdir_post.php'); ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="dir" value="<?php echo h($currentRelDir); ?>">
                    <div class="mb-2">
                        <label class="form-label">Nombre de la carpeta</label>
                        <input class="form-control" type="text" name="folder" required>
                    </div>
                    <button class="btn btn-primary btn-sm" type="submit">Crear</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Subir archivo -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Subir archivo</div>
            <div class="card-body">
                <form action="<?php echo url('modules/archivos/upload_post.php'); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="dir" value="<?php echo h($currentRelDir); ?>">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <input class="form-control" type="file" name="file" required>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-success w-100" type="submit">Subir</button>
                        </div>
                    </div>
                    <small class="text-muted">Extensiones permitidas: pdf, jpg, png, docx, xlsx, txt (máx. 10 MB).</small>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Listado -->
<div class="card mt-3">
    <div class="card-header">Contenido</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th class="text-center" style="width:120px;">Tipo</th>
                        <th class="text-end" style="width:120px;">Tamaño</th>
                        <th class="text-end" style="width:160px;">Modificado</th>
                        <th class="text-end" style="width:220px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted p-3">Sin elementos</td>
                        </tr>
                        <?php else: foreach ($items as $it): ?>
                            <tr>
                                <td>
                                    <?php if ($it['is_dir']): ?>
                                        <a href="<?php
                                                    $next = $currentRelDir ? ($currentRelDir . '/' . $it['name']) : $it['name'];
                                                    echo url('modules/archivos/index.php?dir=' . urlencode($next));
                                                    ?>">
                                            📁 <?php echo h($it['name']); ?>
                                        </a>
                                    <?php else: ?>
                                        📄 <?php echo h($it['name']); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $it['is_dir'] ? 'Carpeta' : 'Archivo'; ?>
                                </td>
                                <td class="text-end">
                                    <?php echo $it['is_dir'] ? '-' : h(human_bytes((int)$it['size'])); ?>
                                </td>
                                <td class="text-end">
                                    <?php echo date('Y-m-d H:i', (int)$it['mtime']); ?>
                                </td>
                                <td class="text-end">
                                    <?php if (!$it['is_dir']): ?>
                                        <a class="btn btn-outline-secondary btn-sm" href="<?php
                                                                                            echo url('modules/archivos/view.php?dir=' . urlencode($currentRelDir) . '&file=' . urlencode($it['name']));
                                                                                            ?>">Ver</a>
                                        <a class="btn btn-outline-primary btn-sm" href="<?php
                                                                                        echo url('modules/archivos/download.php?dir=' . urlencode($currentRelDir) . '&file=' . urlencode($it['name']));
                                                                                        ?>">Descargar</a>
                                        <a class="btn btn-outline-danger btn-sm" href="<?php
                                                                                        echo url('modules/archivos/delete.php?dir=' . urlencode($currentRelDir) . '&file=' . urlencode($it['name']) . '&csrf_token=' . urlencode($_SESSION['csrf_token']));
                                                                                        ?>" onclick="return confirm('¿Eliminar este archivo?');">Eliminar</a>
                                    <?php else: ?>
                                        <?php
                                        $dirFull = $targetPath . DIRECTORY_SEPARATOR . $it['name'];
                                        if (is_dir_empty($dirFull)):
                                        ?>
                                            <a class="btn btn-outline-danger btn-sm" href="<?php
                                                                                            echo url('modules/archivos/delete_folder.php?dir=' . urlencode($currentRelDir) . '&folder=' . urlencode($it['name']) . '&csrf_token=' . urlencode($_SESSION['csrf_token']));
                                                                                            ?>" onclick="return confirm('¿Eliminar esta carpeta vacía?');">Eliminar carpeta</a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>