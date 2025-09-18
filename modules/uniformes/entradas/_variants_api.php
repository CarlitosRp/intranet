<?php
require_once __DIR__ . '/../../../includes/init.php';
require_any_role(['admin', 'inventarios']);

$conn = db_connect();
$sql = "SELECT v.id_variante,
               e.codigo   AS sku,
               e.descripcion,
               e.modelo,
               v.talla
        FROM item_variantes v
        JOIN equipo e ON e.id_equipo = v.id_equipo
        WHERE e.activo = 1 AND v.activo = 1
        ORDER BY e.codigo ASC, v.talla ASC";
$res = mysqli_query($conn, $sql);

$rows = [];
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
mysqli_close($conn);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);
