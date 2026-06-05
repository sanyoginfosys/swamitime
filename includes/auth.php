<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/security.php';

class Auth
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_id']) && (int) $_SESSION['admin_id'] > 0;
    }

    public static function user(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM admins WHERE id = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$_SESSION['admin_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/admin/';
            header('Location: /admin/login.php');
            exit;
        }
    }

    public static function requireSuperAdmin(): void
    {
        self::requireLogin();

        if (!self::isSuperAdmin()) {
            http_response_code(403);
            exit('Access denied. Super admin privileges required.');
        }
    }

    public static function login(string $username, string $password): bool
    {
        $db = self::db();

        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['admin_id']       = (int) $admin['id'];
            $_SESSION['admin_role']     = $admin['role'];
            $_SESSION['admin_username'] = $admin['username'];

            self::updateLastLogin((int) $admin['id']);

            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        $_SESSION = [];

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'domain'   => $params['domain'],
                'secure'   => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => 'Lax',
            ]
        );

        session_destroy();
    }

    public static function needsPasswordChange(): bool
    {
        $user = self::user();

        if (!$user) {
            return false;
        }

        return $user['last_login'] === null;
    }

    public static function changePassword(int $userId, string $newPassword): bool
    {
        $db   = self::db();
        $hash = self::hashPassword($newPassword);

        $stmt = $db->prepare('UPDATE admins SET password_hash = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$hash, $userId]);

        return $stmt->rowCount() > 0;
    }

    public static function hasRole(string $role): bool
    {
        return ($_SESSION['admin_role'] ?? '') === $role;
    }

    public static function isSuperAdmin(): bool
    {
        return self::hasRole('super_admin');
    }

    public static function isAdmin(): bool
    {
        $role = $_SESSION['admin_role'] ?? '';

        return in_array($role, ['super_admin', 'admin'], true);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function updateLastLogin(int $userId): void
    {
        $db = self::db();
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare('UPDATE admins SET last_login = ? WHERE id = ?');
        $stmt->execute([$now, $userId]);
    }

    private static function db(): PDO
    {
        global $db;

        if ($db instanceof PDO) {
            return $db;
        }

        if (function_exists('getDB')) {
            return getDB();
        }

        throw new RuntimeException('Database connection not available. Ensure config.php defines $db or a getDB() function.');
    }
}

if (class_exists('Security') && method_exists('Security', 'set_security_headers')) {
    Security::set_security_headers();
}
