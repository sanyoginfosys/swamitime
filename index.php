<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/seo.php';

// Get the requested URI
$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = strtok($request_uri, '?'); // Remove query string
$request_uri = rtrim($request_uri, '/');
$request_uri = $request_uri ?: '/';

// Remove base path if in subdirectory
$base_path = parse_url(BASE_URL, PHP_URL_PATH);
if ($base_path && strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}
$request_uri = '/' . ltrim($request_uri, '/');

// Route mapping
$routes = [
    '/' => 'pages/home.php',
    '/home' => 'pages/home.php',
    '/about-us' => 'pages/about.php',
    '/services' => 'pages/services.php',
    '/ukg-workforce-management-support' => 'pages/ukg-support.php',
    '/workforce-management-consulting' => 'pages/workforce-consulting.php',
    '/implementation-configuration-support' => 'pages/implementation.php',
    '/training-user-support' => 'pages/training.php',
    '/managed-support-services' => 'pages/managed-support.php',
    '/reporting-data-support' => 'pages/reporting.php',
    '/it-digital-solutions' => 'pages/it-solutions.php',
    '/web-development' => 'pages/web-development.php',
    '/seo-digital-marketing' => 'pages/seo-marketing.php',
    '/industries' => 'pages/industries.php',
    '/industries/retail' => 'pages/industry-detail.php',
    '/industries/hospitality' => 'pages/industry-detail.php',
    '/industries/logistics-distribution' => 'pages/industry-detail.php',
    '/industries/manufacturing' => 'pages/industry-detail.php',
    '/industries/healthcare-care-services' => 'pages/industry-detail.php',
    '/industries/professional-services' => 'pages/industry-detail.php',
    '/industries/small-medium-businesses' => 'pages/industry-detail.php',
    '/case-studies' => 'pages/case-studies.php',
    '/case-studies/' => 'pages/case-study-detail.php',
    '/blog' => 'pages/blog.php',
    '/blog/' => 'pages/blog-post.php',
    '/contact-us' => 'pages/contact.php',
    '/privacy-policy' => 'pages/privacy.php',
    '/terms-conditions' => 'pages/terms.php',
    '/cookie-policy' => 'pages/cookie.php',
    '/gdpr-compliance' => 'pages/gdpr.php',
];

// Check for case study detail pages
if (preg_match('#^/case-studies/([a-zA-Z0-9\-]+)$#', $request_uri, $matches)) {
    $_GET['slug'] = $matches[1];
    $template = 'pages/case-study-detail.php';
}
// Check for blog post detail pages
elseif (preg_match('#^/blog/([a-zA-Z0-9\-]+)$#', $request_uri, $matches)) {
    $_GET['slug'] = $matches[1];
    $template = 'pages/blog-post.php';
}
// Check static routes
elseif (isset($routes[$request_uri])) {
    $template = $routes[$request_uri];
}
// Check if page exists in database by slug
else {
    $slug = trim($request_uri, '/');
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $page = $stmt->fetch();
    if ($page) {
        // Dynamic page - render using generic template
        $template = 'pages/dynamic.php';
        $GLOBALS['current_page'] = $page;
    } else {
        // 404
        http_response_code(404);
        $template = 'pages/404.php';
    }
}

// Set security headers
Security::set_security_headers();

// Load header
include_once __DIR__ . '/includes/header.php';

// Load the page template
if (file_exists(__DIR__ . '/' . $template)) {
    include_once __DIR__ . '/' . $template;
} else {
    include_once __DIR__ . '/pages/404.php';
}

// Load footer
include_once __DIR__ . '/includes/footer.php';
