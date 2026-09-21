<?php
/**
 * Konfigurasi umum aplikasi.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL otomatis terdeteksi (aman untuk subfolder hosting/localhost/reverse proxy seperti Railway)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (!empty($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
$protocol = $isHttps ? 'https://' : 'http://';

$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
// Jika request datang dari dalam folder admin/ atau api/, naik satu level
$baseDir = preg_replace('#/(admin|api)$#', '', $scriptDir);
define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . $baseDir);

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2 MB
define('ALLOWED_UPLOAD_TYPES', ['jpg', 'jpeg', 'png', 'pdf']);

define('APP_NAME', 'Unit Layanan Terpadu - Universitas Negeri Manado');

date_default_timezone_set('Asia/Makassar'); // WITA
