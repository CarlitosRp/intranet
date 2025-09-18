<?php
// templates/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo h(SITE_NAME); ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?php echo url(); ?>">Intranet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="navMain" class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo nav_active('/intranet/'); ?>" href="<?php echo url(); ?>">Inicio</a>
                    </li>

                    <?php if (is_logged_in()): ?>

                        <!-- Acceso para admin o almacén -->
                        <?php if (has_any_role(['admin', 'inventarios'])): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo nav_active('modules/uniformes'); ?>" href="<?php echo url('modules/uniformes/index.php'); ?>">Uniformes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo nav_active('modules/archivos'); ?>" href="<?php echo url('modules/archivos/index.php'); ?>">Archivos</a>
                            </li>
                        <?php endif; ?>

                        <!-- Acceso para admin o rrhh -->
                        <?php if (has_any_role(['admin', 'rrhh'])): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo nav_active('modules/rrhh'); ?>" href="<?php echo url('modules/rrhh/index.php'); ?>">RRHH</a>
                            </li>
                        <?php endif; ?>

                        <!-- Menú solo admin -->
                        <?php if (has_role('admin')): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle <?php echo nav_active('admin/'); ?>" href="#" role="button" data-bs-toggle="dropdown">Admin</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo url('admin/users.php'); ?>">Usuarios</a></li>
                                    <li><a class="dropdown-item" href="<?php echo url('admin/roles.php'); ?>">Roles</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item">
                            <span class="navbar-text me-2 small text-light">
                                <?php echo h($_SESSION['user']['role']); ?>:
                                <strong><?php echo h($_SESSION['user']['name']); ?></strong>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm" href="<?php echo url('logout.php'); ?>">Salir</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm" href="<?php echo url('login.php'); ?>">Ingresar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <?php
        $ok  = flash('success');
        $err = flash('error');
        if ($ok)  echo '<div class="alert alert-success">' . $ok . '</div>';
        if ($err) echo '<div class="alert alert-danger">' . $err . '</div>';
        ?>