<?php

/**
 * SWAMITIME SOLUTIONS LTD - Security Class
 *
 * @package SwamiTime
 * @since   1.0.0
 */

/**
 * Centralised security utilities for the application.
 *
 * Provides CSRF protection, XSS sanitisation, rate limiting,
 * honeypot spam detection, secure HTTP headers, and input validation.
 */
class Security
{
    /* ---------------------------------------------------------------------- */
    /*                            CSRF Protection                             */
    /* ---------------------------------------------------------------------- */

    /**
     * Output a hidden form field containing the CSRF token.
     *
     * Generates a fresh token if one does not already exist in the session.
     *
     * @return string HTML hidden input element.
     */
    public static function csrf_field(): string
    {
        $token = self::getOrCreateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validate the CSRF token from a POST request.
     *
     * Terminates the script with a 403 response when the token is missing or invalid.
     *
     * @return void
     */
    public static function validate_csrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $token = $_POST['csrf_token'] ?? '';

        if (empty($token) || !self::verifyToken($token)) {
            http_response_code(403);
            die('CSRF token validation failed. Please refresh the page and try again.');
        }
    }

    /**
     * Retrieve the current CSRF token or generate a new one.
     *
     * @return string
     */
    private static function getOrCreateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token']      = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Verify a token using timing-safe comparison.
     *
     * @param  string $token
     * @return bool
     */
    private static function verifyToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /* ---------------------------------------------------------------------- */
    /*                            XSS Protection                              */
    /* ---------------------------------------------------------------------- */

    /**
     * Clean a value of potentially dangerous content.
     *
     * @param  mixed $data
     * @return mixed
     */
    public static function xss_clean(mixed $data): mixed
    {
        if (is_string($data)) {
            return htmlspecialchars(strip_tags($data), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return $data;
    }

    /**
     * Recursively clean an array of potentially dangerous content.
     *
     * @param  array $array
     * @return array
     */
    public static function xss_clean_array(array $array): array
    {
        $cleaned = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $cleaned[$key] = self::xss_clean_array($value);
            } else {
                $cleaned[$key] = self::xss_clean($value);
            }
        }
        return $cleaned;
    }

    /* ---------------------------------------------------------------------- */
    /*                           Rate Limiting                                */
    /* ---------------------------------------------------------------------- */

    /**
     * Enforce rate limiting on a given action key.
     *
     * Uses the session to track attempts. When the limit is exceeded the
     * script is terminated with a 429 response.
     *
     * @param  string $key          Unique identifier for the action (e.g. 'login', 'contact').
     * @param  int    $max_attempts Maximum allowed attempts within the decay window.
     * @param  int    $decay        Decay window in seconds (default 300 = 5 minutes).
     * @return void
     */
    public static function rate_limit(string $key, int $max_attempts = 5, int $decay = 300): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sessionKey = 'rate_limit_' . $key;

        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [
                'attempts'    => 0,
                'first_attempt' => time(),
            ];
        }

        $record = &$_SESSION[$sessionKey];

        if (time() - $record['first_attempt'] > $decay) {
            $record['attempts']      = 0;
            $record['first_attempt'] = time();
        }

        $record['attempts']++;

        if ($record['attempts'] > $max_attempts) {
            $retryAfter = $decay - (time() - $record['first_attempt']);
            http_response_code(429);
            header('Retry-After: ' . max(0, $retryAfter));
            die('Too many attempts. Please try again in ' . max(0, $retryAfter) . ' seconds.');
        }
    }

    /* ---------------------------------------------------------------------- */
    /*                         Honeypot Protection                            */
    /* ---------------------------------------------------------------------- */

    /**
     * Output a hidden honeypot field that bots will fill but humans won't.
     *
     * @return string HTML input element hidden via CSS.
     */
    public static function honeypot_field(): string
    {
        return '<div style="position:absolute;left:-9999px;" aria-hidden="true">'
            . '<label for="website_url">Leave this empty</label>'
            . '<input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">'
            . '</div>';
    }

    /**
     * Validate that the honeypot field was left empty (indicating a human).
     *
     * @return bool True if human (field empty), false if bot (field filled).
     */
    public static function validate_honeypot(): bool
    {
        $value = $_POST['website_url'] ?? '';
        return $value === '';
    }

    /* ---------------------------------------------------------------------- */
    /*                           Secure Headers                               */
    /* ---------------------------------------------------------------------- */

    /**
     * Set security-related HTTP response headers.
     *
     * @return void
     */
    public static function set_security_headers(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-Permitted-Cross-Domain-Policies: none');

        if (!headers_sent()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    /* ---------------------------------------------------------------------- */
    /*                          Input Validation                              */
    /* ---------------------------------------------------------------------- */

    /**
     * Validate an email address.
     *
     * @param  string $email
     * @return bool
     */
    public static function validate_email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate a phone number (supports international and local formats).
     *
     * @param  string $phone
     * @return bool
     */
    public static function validate_phone(string $phone): bool
    {
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        return (bool) preg_match('/^\+?[1-9]\d{6,14}$/', $cleaned);
    }

    /**
     * Validate that required fields are present and non-empty in the given data.
     *
     * @param  string[] $fields Array of required field names.
     * @param  array    $data   The input data to check (e.g. $_POST).
     * @return string[]         Array of missing field names.
     */
    public static function validate_required(array $fields, array $data): array
    {
        $missing = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                $missing[] = $field;
            }
        }

        return $missing;
    }
}
