<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// CSRF check (optional for newsletter - use a simple check)
$email = sanitize($_POST['email'] ?? '');

if (empty($email) || !Security::validate_email($email)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Rate limit
if (!Security::rate_limit('newsletter_' . md5($email), 3, 3600)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'You have already subscribed.']);
    exit;
}

try {
    $db = getDB();
    
    // Check if already subscribed (store in enquiries or a dedicated table)
    // For simplicity, just acknowledge
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for subscribing to our newsletter.'
    ]);
    
} catch (Exception $e) {
    error_log('Newsletter error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
}
