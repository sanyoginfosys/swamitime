<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/auth.php';
Auth::requireLogin();
require_once __DIR__ . '/includes/header.php';
$__target = __DIR__ . '/pages/enquiries.php';
if (file_exists($__target)) { require $__target; }
require_once __DIR__ . '/includes/footer.php';