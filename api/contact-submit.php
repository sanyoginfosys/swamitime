<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
if (!Security::validate_csrf()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.']);
    exit;
}

// Validate honeypot
if (!Security::validate_honeypot()) {
    // Silently accept but don't process (spam)
    echo json_encode(['success' => true, 'message' => 'Thank you for contacting SWAMITIME SOLUTIONS LTD. Your enquiry has been received and our team will respond shortly.']);
    exit;
}

// Rate limiting
if (!Security::rate_limit('contact_form', 5, 300)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many attempts. Please try again later.']);
    exit;
}

// Get and sanitize input
$full_name = sanitize($_POST['full_name'] ?? '');
$company_name = sanitize($_POST['company_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$service_required = sanitize($_POST['service_required'] ?? '');
$budget_range = sanitize($_POST['budget_range'] ?? '');
$preferred_contact = sanitize($_POST['preferred_contact'] ?? '');
$message = sanitize($_POST['message'] ?? '');
$gdpr_consent = isset($_POST['gdpr_consent']) ? 1 : 0;

// Validate required fields
$errors = Security::validate_required(
    ['full_name', 'email', 'message', 'gdpr_consent'],
    ['full_name' => $full_name, 'email' => $email, 'message' => $message, 'gdpr_consent' => $gdpr_consent]
);

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.', 'errors' => $errors]);
    exit;
}

// Validate email
if (!Security::validate_email($email)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Validate phone (if provided)
if (!empty($phone) && !Security::validate_phone($phone)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid phone number.']);
    exit;
}

try {
    $db = getDB();
    
    // Insert enquiry
    $stmt = $db->prepare("INSERT INTO enquiries (full_name, company_name, email, phone, service_required, budget_range, preferred_contact, message, gdpr_consent, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$full_name, $company_name, $email, $phone, $service_required, $budget_range, $preferred_contact, $message, $gdpr_consent, $_SERVER['REMOTE_ADDR']]);
    $enquiry_id = $db->lastInsertId();
    
    // Create CRM lead automatically
    $stmt = $db->prepare("INSERT INTO crm_leads (full_name, company_name, email, phone, service_interested, message, lead_source, lead_status, priority, enquiry_id, created_at) VALUES (?, ?, ?, ?, ?, ?, 'contact_form', 'new', 'medium', ?, NOW())");
    $stmt->execute([$full_name, $company_name, $email, $phone, $service_required, $message, $enquiry_id]);
    
    // Send email notification to admin
    $admin_email = '';
    try {
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'site_email' LIMIT 1");
        $stmt->execute();
        $admin_email = $stmt->fetchColumn() ?: '';
    } catch (Exception $e) {}
    if (empty($admin_email)) $admin_email = 'admin@swamitime.com';
    $site_name = defined('SITE_NAME') ? SITE_NAME : 'SWAMITIME SOLUTIONS LTD';
    
    $email_subject = "New Enquiry from $full_name - $site_name";
    $email_body = "
        <h2>New Contact Form Enquiry</h2>
        <p><strong>Name:</strong> $full_name</p>
        <p><strong>Company:</strong> $company_name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Service:</strong> $service_required</p>
        <p><strong>Budget:</strong> $budget_range</p>
        <p><strong>Contact Method:</strong> $preferred_contact</p>
        <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
        <hr>
        <p><small>Submitted via $site_name website</small></p>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $site_name <noreply@swamitime.com>\r\n";
    $headers .= "Reply-To: $email\r\n";
    
    @mail($admin_email, $email_subject, $email_body, $headers);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for contacting SWAMITIME SOLUTIONS LTD. Your enquiry has been received and our team will respond shortly.'
    ]);
    
} catch (Exception $e) {
    error_log('Contact form error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please check your details and try again.']);
}
