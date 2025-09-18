<?php
require_once __DIR__ . '/../../../includes/init.php';
require_any_role(['admin', 'inventarios']);

$conn = db_connect();
$sql = "SELECT e.id_entrada, e.fecha, e.proveedor, e.factura, e.creado_por,
               COUNT(d.id_detalle_entrada) AS renglones,
               COALESCE(SUM(d.cantidad),0) AS piezas
        FROM entradas e
        LEFT JOIN entradas_detalle d ON d.id_entrada = e.id_entrada
        GROUP BY e.id_entrada
        ORDER BY e.id_entrada DESC
        LIMIT 50";
$res = mysqli_query($conn, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
mysqli_close($conn);

include __DIR__ . '/../../../templates/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Entradas</h2>
    <a class="btn btn-primary btn-sm" href="<?php echo url('modules/uniformes/entradas/nueva.php'); ?>">+ Nueva entrada</a>
</div>

<div class="card">
    <div class="card-header">Últimas 50</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:90px;">ID</th>
                        <th style="width:120px;">Fecha</th>
                        <th>Proveedor</th>
                        <th>Factura</th>
                        <th class="text-end" style="width:90px;">Renglones</th>
                        <th class="text-end" style="width:90px;">Piezas</th>
                        <th class="text-end" style="width:150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted p-3">Sin registros</td>
                        </tr>
                        <?php else: foreach ($rows as $e): ?>
                            <tr>
                                <td>#<?php echo (int)$e['id_entrada']; ?></td>
                                <td><?php echo h($e['fecha']); ?></td>
                                <td><?php echo h($e['proveedor']); ?></td>
                                <td><?php echo h($e['factura']); ?></td>
                                <td class="text-end"><?php echo (int)$e['renglones']; ?></td>
                                <td class="text-end"><?php echo (int)$e['piezas']; ?></td>
                                <td class="text-end">
                                    <a class="btn btn-outline-secondary btn-sm me-1" href="<?php
                                                                                            echo url('modules/uniformes/entradas/ver.php?id=' . (int)$e['id_entrada']);
                                                                                            ?>">Ver</a>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../../templates/footer.php'; ?>