<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';
Auth::logout();
set_flash('success', 'You have been logged out successfully.');
redirect(BASE_URL . '/admin/login.php');