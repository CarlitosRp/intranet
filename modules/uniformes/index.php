<?php
require_once __DIR__ . '/../../includes/init.php';
require_any_role(['admin', 'inventarios']); // exige rol admin o inventarios
include __DIR__ . '/../../templates/header.php';
?>

<h2>Módulo de Uniformes</h2>
<p>Bienvenido al módulo. Aquí listaremos y gestionaremos uniformes (pendiente de implementar).</p>

<?php include __DIR__ . '/../../templates/footer.php'; ?>