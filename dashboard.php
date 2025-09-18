<?php
require_once __DIR__ . '/includes/init.php';
require_login(); // si no hay sesión, te manda a login.php
include __DIR__ . '/templates/header.php';
?>

<h2>Panel</h2>
<p>Solo usuarios autenticados pueden ver esto.</p>

<?php include __DIR__ . '/templates/footer.php'; ?>