<?php
declare(strict_types=1);

/**
 * Application bootstrap: session, constants and shared helper functions.
 * Every entry point (public/index.php and public/api/*.php) loads this file.
 */

require __DIR__ . '/Support/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_strict_mode', '1');
    session_start();
}

define('APP_ROOT', dirname(__DIR__));
define('APP_NAME', 'PharmaTrack');

/**
 * Work out the public base URL. Works when the project is served from
 * http://localhost/pharmacy-stock-alert/public as well as from a vhost root.
 */
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$dir    = rtrim(str_replace('\\', '/', dirname($script)), '/');
if (str_ends_with($dir, '/api')) {
    $dir = substr($dir, 0, -4);
}
define('BASE_URL', $dir === '/' ? '' : $dir);

/**
 * Escape any value before printing it into HTML (XSS protection).
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function text_length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }

    $length = preg_match_all('/./us', $value);

    return $length === false ? strlen($value) : $length;
}

/**
 * Build an internal route URL.
 */
function url(string $route = 'dashboard', array $params = []): string
{
    return BASE_URL . '/index.php?' . http_build_query(['r' => $route] + $params);
}

/**
 * Build an API URL.
 */
function api_url(string $file): string
{
    return BASE_URL . '/api/' . ltrim($file, '/');
}

function redirect(string $location): never
{
    header('Location: ' . $location, true, 303);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function post_string(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}
