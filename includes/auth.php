<?php
// includes/auth.php
// Funciones básicas de autenticación (login/logout) y helpers de rol.

require_once __DIR__ . '/db.php';

/**
 * Intentar login con email y password.
 * Retorna true/false y coloca datos mínimos en $_SESSION['user'] si es válido.
 */
function login(string $email, string $password): bool
{
    $conn = db_connect();

    $sql = "SELECT u.id, u.name, u.email, u.password_hash, u.department, u.role_id, u.is_active, r.name AS role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.email = ?
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    if (!$user) {
        return false; // email no existe
    }
    if ((int)$user['is_active'] !== 1) {
        return false; // usuario desactivado
    }
    if (!password_verify($password, $user['password_hash'])) {
        return false; // password incorrecto
    }

    // Guardar datos mínimos en sesión (no guardes el hash)
    $_SESSION['user'] = [
        'id'         => (int)$user['id'],
        'name'       => $user['name'],
        'email'      => $user['email'],
        'department' => $user['department'],
        'role_id'    => (int)$user['role_id'],
        'role'       => $user['role_name'],
    ];
    return true;
}

/** Cerrar sesión */
function logout(): void
{
    unset($_SESSION['user']);
    session_regenerate_id(true);
}

/** ¿Hay usuario logueado? */
function is_logged_in(): bool
{
    return !empty($_SESSION['user']['id']);
}

/** Obtener usuario actual (o null) */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/** Verificar rol exacto (por nombre) */
function has_role(string $roleName): bool
{
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $roleName;
}

/** Verificar si tiene cualquiera de los roles indicados */
function has_any_role(array $roles): bool
{
    if (empty($_SESSION['user']['role'])) return false;
    return in_array($_SESSION['user']['role'], $roles, true);
}

/** Proteger página: redirigir si no logueado */
function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Debes iniciar sesión.');
        redirect('login.php'); // la vista la haremos en el siguiente paso
    }
}

/** Requiere rol exacto (por nombre) y redirige si no cumple */
function require_role(string $roleName): void
{
    if (!is_logged_in() || !has_role($roleName)) {
        flash('error', 'No tienes permisos para acceder a esta sección.');
        redirect('index.php');
    }
}

/** Requiere cualquiera de los roles dados */
function require_any_role(array $roles): void
{
    if (!is_logged_in() || !has_any_role($roles)) {
        flash('error', 'No tienes permisos para acceder a esta sección.');
        redirect('index.php');
    }
}
