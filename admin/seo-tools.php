<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/auth.php';
Auth::requireLogin();
define('ADMIN_SEO_TOOLS', true);
require_once __DIR__ . '/includes/header.php';
$__target = __DIR__ . '/seo/index.php';
if (file_exists($__target)) { require $__target; }
require_once __DIR__ . '/includes/footer.php';