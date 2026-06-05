<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/security.php';

if (Auth::isLoggedIn()) {
    $redirect = $_SESSION['redirect_after_login'] ?? (BASE_URL . '/admin/index.php');
    unset($_SESSION['redirect_after_login']);
    redirect($redirect);
}

$error = '';
$csrf_token = Security::csrf_field();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::rate_limit('admin_login', 5, 900);

    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($submittedToken)) {
        $error = 'Security validation failed. Please refresh the page and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $missing = Security::validate_required(['username', 'password'], $_POST);
        if (!empty($missing)) {
            $error = 'Please enter both username and password.';
        } elseif (Auth::login($username, $password)) {
            $redirect = $_SESSION['redirect_after_login'] ?? (BASE_URL . '/admin/index.php');
            unset($_SESSION['redirect_after_login']);
            redirect($redirect);
        } else {
            $error = 'Invalid username or password. Please try again.';
        }
    }

    $csrf_token = Security::csrf_field();
}

$timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 3600;
if (Auth::isLoggedIn() && isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    Auth::logout();
    $error = 'Your session has expired. Please log in again.';
}
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login — SWAMITIME SOLUTIONS LTD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@500;600;700;800&amp;display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/admin/assets/css/admin.css" rel="stylesheet">
    <style>
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #004E53 0%, #078E91 50%, #0DB5B8 100%);
            padding: 1.5rem;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            padding: 2.5rem 2rem;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .login-brand .brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: 1.5px;
            color: var(--teal-dark, #004E53);
            line-height: 1.15;
        }
        .login-brand .brand-sub {
            display: block;
            font-size: 0.6rem;
            font-weight: 500;
            letter-spacing: 4px;
            color: var(--teal-mid, #078E91);
            text-transform: uppercase;
        }
        .login-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            text-align: center;
            color: #1a1a1a;
            margin-bottom: 1.75rem;
        }
        .login-card .form-label {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 0.35rem;
        }
        .login-card .form-control {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            padding: 0.65rem 0.9rem;
            border-radius: 8px;
            border: 1px solid #D1D5DB;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .login-card .form-control:focus {
            border-color: var(--teal-mid, #078E91);
            box-shadow: 0 0 0 3px rgba(7, 142, 145, 0.12);
        }
        .input-group .btn-outline-secondary {
            border: 1px solid #D1D5DB;
            border-left: none;
            color: #6B7280;
            border-radius: 0 8px 8px 0;
        }
        .input-group .btn-outline-secondary:hover {
            background: #F3F4F6;
            color: #374151;
        }
        .input-group .form-control {
            border-radius: 8px 0 0 8px;
        }
        .form-check-input:checked {
            background-color: var(--teal-mid, #078E91);
            border-color: var(--teal-mid, #078E91);
        }
        .btn-login {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            background: linear-gradient(135deg, #004E53 0%, #078E91 100%);
            border: none;
            color: #ffffff;
            width: 100%;
            letter-spacing: 0.5px;
            transition: transform 0.15s ease, box-shadow 0.2s ease;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 78, 83, 0.35);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .alert {
            font-size: 0.85rem;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        .login-footer-text {
            text-align: center;
            font-size: 0.75rem;
            color: #9CA3AF;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-brand">
            <span class="brand-name">SWAMITIME</span>
            <span class="brand-sub">SOLUTIONS LTD</span>
        </div>
        <h1 class="login-heading">Admin Panel</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>

        <?php foreach (get_flash() as $msg): ?>
            <div class="alert alert-<?php echo htmlspecialchars($msg['type'], ENT_QUOTES, 'UTF-8'); ?> d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <span><?php echo htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endforeach; ?>

        <form action="<?php echo BASE_URL; ?>/admin/login.php" method="POST" novalidate>
            <?php echo $csrf_token; ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username or Email</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-right:none; background:transparent; border-radius:8px 0 0 8px;">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username or email" value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required autofocus autocomplete="username">
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-right:none; background:transparent; border-radius:8px 0 0 8px;">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1" aria-label="Toggle password visibility">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                    <label class="form-check-label" for="remember" style="font-size:0.85rem;">
                        Remember Me
                    </label>
                </div>
                <a href="<?php echo BASE_URL; ?>/admin/forgot-password.php" style="font-size:0.85rem; color:var(--teal-mid, #078E91); text-decoration:none;">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </button>
        </form>

        <p class="login-footer-text">&copy; <?php echo date('Y'); ?> SWAMITIME SOLUTIONS LTD. All rights reserved.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    var toggle = document.getElementById('togglePassword');
    var password = document.getElementById('password');
    var icon = toggle.querySelector('i');

    toggle.addEventListener('click', function() {
        var type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
})();
</script>

</body>
</html>
