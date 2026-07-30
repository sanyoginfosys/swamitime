<?php

declare(strict_types=1);

// ============================================================
// SWAMITIME SOLUTIONS LTD - Main Configuration
// ============================================================

// -------------------------------------------------------
// Base Constants
// -------------------------------------------------------

define('SITE_NAME', 'SWAMITIME SOLUTIONS LTD');

if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    // If running from admin/ subdirectory, base URL is site root
    if (str_ends_with($scriptDir, '/admin')) {
        $scriptDir = substr($scriptDir, 0, -6) ?: '/';
    }
    $baseUrl = rtrim($protocol . $host . ($scriptDir ?: ''), '/');
    define('SITE_URL', rtrim($protocol . $host, '/'));
    define('BASE_URL', $baseUrl);
} else {
    define('SITE_URL', 'http://localhost');
    define('BASE_URL', 'http://localhost');
}

// GZIP compression for faster page loads
if (!ob_start('ob_gzhandler')) { ob_start(); }

define('BASE_PATH', __DIR__);
define('ADMIN_EMAIL', 'admin@swamitime.com');

// -------------------------------------------------------
// Error Reporting
// -------------------------------------------------------
// ON for development. Set both to '0' and error_reporting(0) for production.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// -------------------------------------------------------
// Timezone
// -------------------------------------------------------

date_default_timezone_set('Europe/London');

// -------------------------------------------------------
// Database Constants
// -------------------------------------------------------

define('DB_HOST', 'localhost');
define('DB_NAME', 'swamitime');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// -------------------------------------------------------
// Security Constants
// -------------------------------------------------------

define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600);

// -------------------------------------------------------
// Upload Configuration
// -------------------------------------------------------

define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx']);

// -------------------------------------------------------
// Session Configuration
// -------------------------------------------------------

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }

    session_name('SWAMITIME_SESSION');
    session_start();
}

// -------------------------------------------------------
// Database Connection
// -------------------------------------------------------

require_once __DIR__ . '/includes/db.php';

// -------------------------------------------------------
// URL Helper Functions
// -------------------------------------------------------

function site_url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset_url(string $path = ''): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function admin_url(string $path = ''): string
{
    return BASE_URL . '/admin/' . ltrim($path, '/');
}
