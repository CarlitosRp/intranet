<?php
require_once __DIR__ . '/includes/init.php';
include __DIR__ . '/templates/header.php';
?>

<?php if (is_logged_in()): ?>
    <div class="row g-3">
        <div class="col-12">
            <h2 class="h4 mb-3">Panel rápido</h2>
            <p class="text-muted">Accesos directos a tus módulos.</p>
        </div>

        <div class="col-md-4">
            <div class="card-lite">
                <h3 class="h5">Panel</h3>
                <p>Resumen y accesos solo para usuarios autenticados.</p>
                <a class="btn btn-sm btn-outline-primary" href="<?php echo url('dashboard.php'); ?>">Ir al panel</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-lite">
                <h3 class="h5">Uniformes</h3>
                <p>Gestiona el módulo de Uniformes (placeholder por ahora).</p>
                <a class="btn btn-sm btn-primary" href="<?php echo url('modules/uniformes/index.php'); ?>">Abrir módulo</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-lite">
                <h3 class="h5">Sesión</h3>
                <p>Cierra tu sesión de forma segura.</p>
                <a class="btn btn-sm btn-outline-danger" href="<?php echo url('logout.php'); ?>">Cerrar sesión</a>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card-lite">
                <h2 class="h4">Bienvenido a la Intranet</h2>
                <p class="text-muted">Inicia sesión para acceder a los módulos.</p>
                <a class="btn btn-primary" href="<?php echo url('login.php'); ?>">Ingresar</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/templates/footer.php'; ?>