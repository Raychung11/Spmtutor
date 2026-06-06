<?php
/**
 * Shared helper functions: escaping, CSRF, flash, redirect, URLs, uploads.
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

/** Start a hardened session once. */
function boot_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('STAI_SESS');
    session_start();
}

/** HTML-escape for output. */
function e(?string $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build an app URL respecting BASE_PATH. */
function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return BASE_PATH . $path;
}

/** Redirect and stop. */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/** Read all input safely as a trimmed string. */
function input(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function input_int(string $key, int $default = 0): int
{
    $v = $_POST[$key] ?? $_GET[$key] ?? null;
    return is_numeric($v) ? (int) $v : $default;
}

// --- CSRF ---
function csrf_token(): string
{
    boot_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    boot_session();
    $sent = $_POST['_csrf'] ?? '';
    if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string) $sent)) {
        http_response_code(419);
        exit('Invalid or expired form token. Please go back and try again.');
    }
}

// --- Flash messages ---
function flash(string $type, string $message): void
{
    boot_session();
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    boot_session();
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/** Validate and store an uploaded image, returning the relative path. */
function store_upload(array $file, string $subdir = 'answers'): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ALLOWED_UPLOAD_MIME, true)) {
        return null;
    }
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        default      => 'bin',
    };
    $dir = UPLOAD_DIR . '/' . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $dir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }
    return 'uploads/' . $subdir . '/' . $name;
}

/** Convert pipe-delimited string to array (used for plan features etc.). */
function pipe_list(?string $s): array
{
    if (!$s) {
        return [];
    }
    return array_values(array_filter(array_map('trim', explode('|', $s))));
}
