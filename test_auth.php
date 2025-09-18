<?php
require_once __DIR__ . '/includes/init.php';

$email = 'admin@intranet.local';
$pass  = 'Admin1234';

if (login($email, $pass)) {
    echo "<p>Login OK ✅</p>";
    echo "<pre>";
    print_r(current_user());
    echo "</pre>";
    logout();
    echo "<p>Logout OK ✅</p>";
} else {
    echo "<p>Login falló ❌</p>";
}
