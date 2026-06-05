<?php

/**
 * SWAMITIME SOLUTIONS LTD - Helper Functions
 *
 * @package SwamiTime
 * @since   1.0.0
 */

if (!defined('ABSPATH') && !defined('SWAMITIME_LOADED')) {
    define('SWAMITIME_LOADED', true);
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';

/* -------------------------------------------------------------------------- */
/*                               Sanitization                                 */
/* -------------------------------------------------------------------------- */

/**
 * Sanitize a single input value.
 *
 * @param  mixed $data The input data to sanitize.
 * @return mixed       Sanitized string, or original value if not a string.
 */
function sanitize(mixed $data): mixed
{
    if (is_string($data)) {
        return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

/**
 * Recursively sanitize an array of inputs.
 *
 * @param  array $array The input array to sanitize.
 * @return array        Sanitized array.
 */
function sanitize_array(array $array): array
{
    $sanitized = [];
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $sanitized[$key] = sanitize_array($value);
        } else {
            $sanitized[$key] = sanitize($value);
        }
    }
    return $sanitized;
}

/* -------------------------------------------------------------------------- */
/*                              Slug Generation                               */
/* -------------------------------------------------------------------------- */

/**
 * Generate a URL-safe slug from a string.
 *
 * @param  string $string The string to convert.
 * @return string         The generated slug.
 */
function create_slug(string $string): string
{
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

/* -------------------------------------------------------------------------- */
/*                             Date Formatting                                */
/* -------------------------------------------------------------------------- */

/**
 * Format a date string into a human-readable format.
 *
 * @param  string $date   The date string.
 * @param  string $format PHP date format string.
 * @return string         Formatted date.
 */
function format_date(string $date, string $format = 'd M Y'): string
{
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    return date($format, $timestamp);
}

/**
 * Return a human-readable "time ago" string.
 *
 * @param  string $datetime A datetime string.
 * @return string           e.g. "2 hours ago".
 */
function time_ago(string $datetime): string
{
    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
        return $datetime;
    }

    $diff = time() - $timestamp;

    if ($diff < 1) {
        return 'just now';
    }

    $intervals = [
        31536000 => 'year',
        2592000  => 'month',
        604800   => 'week',
        86400    => 'day',
        3600     => 'hour',
        60       => 'minute',
        1        => 'second',
    ];

    foreach ($intervals as $seconds => $label) {
        $count = (int) ($diff / $seconds);
        if ($count > 0) {
            return $count . ' ' . $label . ($count !== 1 ? 's' : '') . ' ago';
        }
    }

    return 'just now';
}

/* -------------------------------------------------------------------------- */
/*                             String Helpers                                 */
/* -------------------------------------------------------------------------- */

/**
 * Truncate a string to a given length without breaking words.
 *
 * @param  string $string The string to truncate.
 * @param  int    $length Maximum character length.
 * @param  string $append String to append if truncated.
 * @return string         Truncated string.
 */
function truncate(string $string, int $length = 150, string $append = '...'): string
{
    $string = strip_tags($string);

    if (mb_strlen($string) <= $length) {
        return $string;
    }

    $truncated = mb_substr($string, 0, $length);
    $lastSpace = mb_strrpos($truncated, ' ');

    if ($lastSpace !== false) {
        $truncated = mb_substr($truncated, 0, $lastSpace);
    }

    return $truncated . $append;
}

/**
 * Generate an excerpt from content.
 *
 * @param  string $content The content string.
 * @param  int    $length  Maximum character length.
 * @return string          Excerpt text.
 */
function excerpt(string $content, int $length = 200): string
{
    return truncate($content, $length, '...');
}

/* -------------------------------------------------------------------------- */
/*                               CSRF Tokens                                  */
/* -------------------------------------------------------------------------- */

/**
 * Generate a CSRF token and store it in the session.
 *
 * @return string The generated token.
 */
function generate_csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();

    return $token;
}

/**
 * Verify a CSRF token against the one stored in the session.
 *
 * @param  string $token The token to verify.
 * @return bool          True if valid, false otherwise.
 */
function verify_csrf_token(string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    $valid = hash_equals($_SESSION['csrf_token'], $token);

    if ($valid) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
    }

    return $valid;
}

/* -------------------------------------------------------------------------- */
/*                             Flash Messages                                 */
/* -------------------------------------------------------------------------- */

/**
 * Store a flash message in the session.
 *
 * @param string $type    Message type (success, error, warning, info).
 * @param string $message The message text.
 */
function set_flash(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['flash_messages'][] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * Retrieve and clear all flash messages from the session.
 *
 * @return array Array of flash message arrays with 'type' and 'message' keys.
 */
function get_flash(): array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);

    return $messages;
}

/* -------------------------------------------------------------------------- */
/*                               Redirection                                  */
/* -------------------------------------------------------------------------- */

/**
 * Redirect to a given URL and terminate execution.
 *
 * @param string $url        The destination URL.
 * @param int    $statusCode HTTP status code (default 302).
 */
function redirect(string $url, int $statusCode = 302): never
{
    if (!headers_sent()) {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }
    echo '<script>window.location.href=' . json_encode($url, JSON_UNESCAPED_SLASHES) . ';</script>';
    exit;
}

/* -------------------------------------------------------------------------- */
/*                               URL Helpers                                  */
/* -------------------------------------------------------------------------- */

/**
 * Get the current full URL.
 *
 * @return string The current URL.
 */
function get_current_url(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? 'https' : 'http';
    return $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
}

/**
 * Check if the current page matches a given slug.
 *
 * @param  string $slug The page slug to check.
 * @return bool         True if it matches.
 */
function is_active_page(string $slug): bool
{
    $current = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $current === trim($slug, '/');
}

/* -------------------------------------------------------------------------- */
/*                             Pagination                                     */
/* -------------------------------------------------------------------------- */

/**
 * Calculate pagination values.
 *
 * @param  int $total        Total number of items.
 * @param  int $per_page     Items per page.
 * @param  int $current_page Current page number (1-indexed).
 * @return array{offset: int, total_pages: int, per_page: int, current_page: int, has_previous: bool, has_next: bool}
 */
function paginate(int $total, int $per_page, int $current_page): array
{
    $total_pages  = max(1, (int) ceil($total / $per_page));
    $current_page = max(1, min($current_page, $total_pages));
    $offset       = ($current_page - 1) * $per_page;

    return [
        'offset'       => $offset,
        'total_pages'  => $total_pages,
        'per_page'     => $per_page,
        'current_page' => $current_page,
        'has_previous' => $current_page > 1,
        'has_next'     => $current_page < $total_pages,
    ];
}

/* -------------------------------------------------------------------------- */
/*                             File Upload                                    */
/* -------------------------------------------------------------------------- */

/**
 * Handle file upload with validation.
 *
 * @param  array  $file          The $_FILES array element.
 * @param  string $directory     Target directory relative to project root.
 * @param  array  $allowed_types Allowed file extensions.
 * @param  int    $max_size      Maximum file size in bytes (default 10 MB).
 * @return array{success: bool, path?: string, filename?: string, error?: string}
 */
function upload_file(
    array $file,
    string $directory,
    array $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'],
    int $max_size = 10485760
): array {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the server limit.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the form limit.',
            UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        return [
            'success' => false,
            'error'   => $errorMessages[$file['error'] ?? UPLOAD_ERR_NO_FILE] ?? 'Unknown upload error.',
        ];
    }

    if ($file['size'] > $max_size) {
        return [
            'success' => false,
            'error'   => 'File size exceeds the maximum allowed size of ' . ($max_size / 1048576) . ' MB.',
        ];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_types, true)) {
        return [
            'success' => false,
            'error'   => 'File type not allowed. Allowed types: ' . implode(', ', $allowed_types) . '.',
        ];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    if (isset($allowedMimes[$extension]) && $mime !== $allowedMimes[$extension]) {
        return [
            'success' => false,
            'error'   => 'Invalid file content. The file does not match its extension.',
        ];
    }

    $targetDir = __DIR__ . '/../' . trim($directory, '/');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $filepath = $targetDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => false,
            'error'   => 'Failed to move uploaded file.',
        ];
    }

    return [
        'success'  => true,
        'path'     => trim($directory, '/') . '/' . $filename,
        'filename' => $filename,
    ];
}

/* -------------------------------------------------------------------------- */
/*                             Image Helpers                                  */
/* -------------------------------------------------------------------------- */

/**
 * Get the full URL for an image path.
 *
 * @param  string|null $path Relative path to the image.
 * @return string            Full image URL or placeholder.
 */
function get_image_url(?string $path): string
{
    if (empty($path)) {
        return BASE_URL . '/assets/images/placeholder.jpg';
    }

    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return $path;
    }

    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Delete a file from the server.
 *
 * @param  string $path Relative path to the file.
 * @return bool         True on success, false on failure.
 */
function delete_file(string $path): bool
{
    $filepath = __DIR__ . '/../' . ltrim($path, '/');
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    return false;
}
