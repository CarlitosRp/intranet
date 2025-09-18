<?php
// includes/helpers.php
// Funciones simples y reutilizables en toda la app.

/**
 * Escapa HTML para imprimir seguro en vistas.
 */
function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Construye URL absolutas a partir de BASE_URL.
 *   url('assets/css/style.css') -> http://localhost/intranet/assets/css/style.css
 */
function url(string $path = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $path ? ($base . '/' . $path) : $base;
}

/**
 * Redirige y termina la ejecución.
 */
function redirect(string $path = ''): void
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Mensajes flash en sesión.
 *   flash('ok', 'Guardado');  // set
 *   echo flash('ok');         // get (y elimina)
 */
function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

/**
 * Debug rápido (solo en desarrollo).
 */
function dd(mixed $var): void
{
    echo '<pre style="background:#111;color:#0f0;padding:10px;border-radius:6px;">';
    var_dump($var);
    echo '</pre>';
    exit;
}

/**
 * Devuelve 'active' si la URL actual contiene el fragmento dado (para navbar)
 * Ej: nav_active('modules/uniformes') -> 'active' si estamos en ese módulo.
 */
function nav_active(string $needle): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return (strpos($uri, $needle) !== false) ? 'active' : '';
}

/**
 * Limpia nombre de archivo/carpeta (sin rutas, sin caracteres peligrosos).
 */
function clean_filename(string $name): string
{
    // quitar rutas y normalizar
    $name = trim($name);
    $name = str_replace(['\\', '/'], '-', $name);
    // solo letras, números, espacios, guiones, guion_bajo, punto
    $name = preg_replace('/[^A-Za-z0-9 _.-]/', '', $name) ?? '';
    // evita nombres vacíos o reservados
    return $name !== '' ? $name : 'archivo';
}

/**
 * Une base + subruta (tipo breadcrumb) y valida que quede dentro de la base.
 * Retorna ruta física si es válida; si no, false.
 */
function safe_join(string $base, string $subpath)
{
    $base = rtrim($base, DIRECTORY_SEPARATOR);
    $target = $base;
    if ($subpath !== '') {
        // normalizar separadores
        $subpath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $subpath);
        $target = $base . DIRECTORY_SEPARATOR . $subpath;
    }
    // Resolver paths reales
    $realBase = realpath($base);
    if ($realBase === false) return false;

    // Si aún no existe el target (por ejemplo, carpeta nueva), lo normalizamos manual
    $realTarget = realpath($target);
    if ($realTarget === false) {
        // construir sin .. ni .
        $parts = [];
        foreach (explode(DIRECTORY_SEPARATOR, $target) as $part) {
            if ($part === '' || $part === '.') continue;
            if ($part === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }
        $realTarget = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
        // reconstruir con drive en Windows si aplica
        if (preg_match('/^[A-Z]:/i', $realBase)) {
            $drive = substr($realBase, 0, 2); // C:
            $realTarget = $drive . $realTarget;
        }
    }
    // Validar que esté dentro de la base
    return (strpos($realTarget, $realBase) === 0) ? $realTarget : false;
}

/**
 * Tamaño legible (ej. 1.2 MB)
 */
function human_bytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 1) . ' ' . $units[$i];
}

/**
 * Detecta MIME real usando finfo (más confiable que extensión).
 */
function detect_mime(string $filepath): string
{
    if (!is_file($filepath)) return 'application/octet-stream';
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $filepath) : false;
    if ($finfo) finfo_close($finfo);
    return $mime ?: 'application/octet-stream';
}

/**
 * Lista blanca de extensiones permitidas y equivalentes MIME.
 */
function allowed_extensions(): array
{
    return [
        'pdf'  => ['application/pdf'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'txt'  => ['text/plain'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
    ];
}

/**
 * ¿Se puede previsualizar inline?
 */
function is_previewable_mime(string $mime): bool
{
    return str_starts_with($mime, 'image/') || $mime === 'application/pdf' || $mime === 'text/plain';
}

/**
 * ¿Carpeta vacía?
 */
function is_dir_empty(string $dir): bool
{
    if (!is_dir($dir)) return false;
    $h = opendir($dir);
    if (!$h) return false;
    while (($e = readdir($h)) !== false) {
        if ($e === '.' || $e === '..') continue;
        closedir($h);
        return false;
    }
    closedir($h);
    return true;
}
