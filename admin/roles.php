<?php
require_once __DIR__ . '/../includes/init.php';
require_role('admin');
include __DIR__ . '/../templates/header.php';
?>
<h2>Administración de Roles</h2>
<p>Solo admin. Aquí podremos mantener la tabla de roles.</p>
<?php include __DIR__ . '/../templates/footer.php'; ?>