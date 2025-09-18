<?php
require_once __DIR__ . '/includes/init.php';

// Si ya está logueado, mándalo al inicio
if (is_logged_in()) {
    redirect('index.php');
}
include __DIR__ . '/templates/header.php';
?>

<h2>Iniciar sesión</h2>
<p>Usa tu correo y contraseña.</p>

<form action="<?php echo url('login_post.php'); ?>" method="post" style="max-width:420px;">
    <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">

    <div style="margin-bottom:10px;">
        <label for="email">Correo</label><br>
        <input type="email" id="email" name="email" required style="width:100%;padding:8px;">
    </div>

    <div style="margin-bottom:10px;">
        <label for="password">Contraseña</label><br>
        <input type="password" id="password" name="password" required style="width:100%;padding:8px;">
    </div>

    <button type="submit" style="padding:8px 16px;">Ingresar</button>
</form>

<?php include __DIR__ . '/templates/footer.php'; ?>