<?php
require_once __DIR__ . '/../../../includes/init.php';
require_any_role(['admin', 'inventarios']);

$conn = db_connect();
// Traemos todas las variantes con su artículo (para el <select>)
$sql = "SELECT v.id_variante, e.codigo, e.descripcion, e.modelo, v.talla
        FROM item_variantes v
        JOIN equipo e ON e.id_equipo = v.id_equipo
        WHERE e.activo = 1 AND v.activo = 1
        ORDER BY e.codigo ASC, v.talla ASC";
$res = mysqli_query($conn, $sql);
$variants = [];
while ($r = mysqli_fetch_assoc($res)) $variants[] = $r;
mysqli_close($conn);

include __DIR__ . '/../../../templates/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Nueva entrada</h2>
    <a class="btn btn-outline-secondary btn-sm" href="<?php echo url('modules/uniformes/entradas/index.php'); ?>">Volver</a>
</div>

<form method="post" action="<?php echo url('modules/uniformes/entradas/nueva_post.php'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">

    <div class="card mb-3">
        <div class="card-header">Cabecera</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Proveedor</label>
                    <input type="text" name="proveedor" class="form-control" maxlength="120" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Factura</label>
                    <input type="text" name="factura" class="form-control" maxlength="60" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Observaciones</label>
                    <input type="text" name="observaciones" class="form-control" maxlength="255">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Detalle (artículos)</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">+ Agregar renglón</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm align-middle" id="detalleTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:220px;">SKU</th>
                            <th>Descripción</th>
                            <th style="width:140px;">Modelo</th>
                            <th style="width:100px;">Talla</th>
                            <th style="width:120px;" class="text-end">Cantidad</th>
                            <th style="width:90px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- fila inicial -->
                    </tbody>
                </table>
            </div>
            <small class="text-muted">Escribe/elige el SKU; se autocompleta lo demás. Agrega todas las tallas necesarias.</small>
        </div>
    </div>

    <div class="text-end">
        <button class="btn btn-success">Guardar entrada</button>
    </div>
</form>

<script>
    let VARS = []; // catálogo de variantes {id_variante, sku, descripcion, modelo, talla}

    async function loadVariants() {
        const url = "<?php echo url('modules/uniformes/entradas/_variants_api.php'); ?>";
        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        });
        VARS = await res.json();
    }

    function buildDatalist() {
        const dl = document.createElement('datalist');
        dl.id = 'skuList';
        VARS.forEach(v => {
            const opt = document.createElement('option');
            opt.value = v.sku; // valor que se ve/elige
            opt.label = v.talla ? `${v.descripcion} · ${v.modelo || ''} · Talla ${v.talla}`.trim() : v.descripcion;
            dl.appendChild(opt);
        });
        document.body.appendChild(dl);
    }

    function addRow() {
        const tbody = document.querySelector('#detalleTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
    <td>
      <input type="text" class="form-control sku-input" list="skuList" placeholder="Escribe o elige SKU">
      <input type="hidden" name="id_variante[]" class="id-var">
    </td>
    <td><input type="text" class="form-control form-control-plaintext desc" readonly></td>
    <td><input type="text" class="form-control form-control-plaintext modelo" readonly></td>
    <td><input type="text" class="form-control form-control-plaintext talla" readonly></td>
    <td class="text-end">
      <input type="number" name="cantidad[]" class="form-control text-end qty" min="1" step="1" required>
    </td>
    <td class="text-end">
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="delRow(this)">Eliminar</button>
    </td>
  `;
        tbody.appendChild(tr);

        // listener para autocompletar al elegir/escribir SKU
        const skuInput = tr.querySelector('.sku-input');
        skuInput.addEventListener('change', () => fillBySku(tr, skuInput.value));
    }

    function delRow(btn) {
        const tr = btn.closest('tr');
        const tbody = tr.parentNode;
        if (tbody.rows.length > 0) tr.remove();
    }

    function fillBySku(tr, sku) {
        // Hallar la primera coincidencia de ese SKU (si hay varias tallas, el usuario deberá diferenciar; aquí tomamos la primera)
        const match = VARS.find(v => v.sku.trim().toUpperCase() === (sku || '').trim().toUpperCase());
        const idVar = tr.querySelector('.id-var');
        const desc = tr.querySelector('.desc');
        const modelo = tr.querySelector('.modelo');
        const talla = tr.querySelector('.talla');

        if (match) {
            idVar.value = match.id_variante;
            desc.value = match.descripcion || '';
            modelo.value = match.modelo || '';
            talla.value = match.talla || '';
        } else {
            // limpiar si no coincide
            idVar.value = '';
            desc.value = modelo.value = talla.value = '';
        }
    }

    // init
    (async () => {
        await loadVariants();
        buildDatalist();
        addRow(); // una fila inicial
    })();
</script>
<?php include __DIR__ . '/../../../templates/footer.php'; ?>