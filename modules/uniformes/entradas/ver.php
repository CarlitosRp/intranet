<?php
require_once __DIR__ . '/../../../includes/init.php';
require_any_role(['admin', 'inventarios']);

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect('modules/uniformes/entradas/index.php');

$conn = db_connect();
// Cabecera
$stmt = mysqli_prepare($conn, "SELECT id_entrada, fecha, proveedor, factura, observaciones, creado_por FROM entradas WHERE id_entrada = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hdr = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);
if (!$hdr) {
    mysqli_close($conn);
    flash('error', 'Entrada no encontrada.');
    redirect('modules/uniformes/entradas/index.php');
}

// Detalle
$sql = "SELECT d.id_detalle_entrada, d.cantidad,
               e.codigo, e.descripcion, e.modelo, v.talla
        FROM entradas_detalle d
        JOIN item_variantes v ON v.id_variante = d.id_variante
        JOIN equipo e ON e.id_equipo = v.id_equipo
        WHERE d.id_entrada = ?
        ORDER BY e.codigo, v.talla";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$rows = [];
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
mysqli_stmt_close($stmt);
mysqli_close($conn);

include __DIR__ . '/../../../templates/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Entrada #<?php echo (int)$hdr['id_entrada']; ?></h2>
    <a class="btn btn-outline-secondary btn-sm" href="<?php echo url('modules/uniformes/entradas/index.php'); ?>">Volver</a>
</div>

<div class="card mb-3">
    <div class="card-header">Cabecera</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><strong>Fecha:</strong> <?php echo h($hdr['fecha']); ?></div>
            <div class="col-md-4"><strong>Proveedor:</strong> <?php echo h($hdr['proveedor']); ?></div>
            <div class="col-md-3"><strong>Factura:</strong> <?php echo h($hdr['factura']); ?></div>
            <div class="col-md-12"><strong>Observaciones:</strong> <?php echo h($hdr['observaciones']); ?></div>
            <div class="col-md-12"><small class="text-muted">Creado por: <?php echo h($hdr['creado_por']); ?></small></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Detalle</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Artículo</th>
                        <th style="width:130px;">Modelo</th>
                        <th style="width:90px;">Talla</th>
                        <th class="text-end" style="width:120px;">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted p-3">Sin renglones</td>
                        </tr>
                        <?php else: foreach ($rows as $d): ?>
                            <tr>
                                <td><code><?php echo h($d['codigo']); ?></code> — <?php echo h($d['descripcion']); ?></td>
                                <td><?php echo h($d['modelo']); ?></td>
                                <td><?php echo h($d['talla']); ?></td>
                                <td class="text-end"><?php echo (int)$d['cantidad']; ?></td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>