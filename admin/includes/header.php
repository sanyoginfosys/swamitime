<?php

declare(strict_types=1);

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../../config.php';
}
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';

$adminUser = Auth::user();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$currentPath = rtrim($currentPath, '/') ?: '/';

function admin_nav_active(string $url): string
{
    global $currentPath;
    $adminBase = BASE_URL . '/admin';
    $fullUrl = $adminBase . $url;
    return ($currentPath === $fullUrl || strpos($currentPath, $fullUrl . '/') === 0) ? 'active' : '';
}

function admin_nav_open(string ...$urls): string
{
    global $currentPath;
    foreach ($urls as $url) {
        $fullUrl = BASE_URL . '/admin' . $url;
        if ($currentPath === $fullUrl || strpos($currentPath, $fullUrl . '/') === 0) {
            return 'show';
        }
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . ' — ' : ''; ?>Admin Panel — SWAMITIME SOLUTIONS LTD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/admin/assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">

<button class="sidebar-toggle d-lg-none" type="button" id="sidebarToggleMobile" aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
</button>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="sidebar-logo">
            <span>SWAMITIME</span>
            <small>SOLUTIONS LTD</small>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_active('/index.php'); ?>" href="<?php echo BASE_URL; ?>/admin/index.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_open('/pages.php', '/page-edit.php'); ?>" href="#pagesSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?php echo admin_nav_open('/pages.php', '/page-edit.php') ? 'true' : 'false'; ?>">
                    <i class="bi bi-file-text"></i> Pages
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="nav flex-column collapse <?php echo admin_nav_open('/pages.php', '/page-edit.php'); ?>" id="pagesSubmenu">
                    <li><a class="nav-link <?php echo admin_nav_active('/pages.php'); ?>" href="<?php echo BASE_URL; ?>/admin/pages.php">All Pages</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/page-edit.php'); ?>" href="<?php echo BASE_URL; ?>/admin/page-edit.php">Add New</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_open('/services.php', '/service-edit.php'); ?>" href="#servicesSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?php echo admin_nav_open('/services.php', '/service-edit.php') ? 'true' : 'false'; ?>">
                    <i class="bi bi-briefcase"></i> Services
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="nav flex-column collapse <?php echo admin_nav_open('/services.php', '/service-edit.php'); ?>" id="servicesSubmenu">
                    <li><a class="nav-link <?php echo admin_nav_active('/services.php'); ?>" href="<?php echo BASE_URL; ?>/admin/services.php">All Services</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/service-edit.php'); ?>" href="<?php echo BASE_URL; ?>/admin/service-edit.php">Add New</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_active('/industries.php'); ?>" href="<?php echo BASE_URL; ?>/admin/industries.php">
                    <i class="bi bi-buildings"></i> Industries
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_open('/case-studies.php', '/case-study-edit.php'); ?>" href="#caseStudiesSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?php echo admin_nav_open('/case-studies.php', '/case-study-edit.php') ? 'true' : 'false'; ?>">
                    <i class="bi bi-star"></i> Case Studies
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="nav flex-column collapse <?php echo admin_nav_open('/case-studies.php', '/case-study-edit.php'); ?>" id="caseStudiesSubmenu">
                    <li><a class="nav-link <?php echo admin_nav_active('/case-studies.php'); ?>" href="<?php echo BASE_URL; ?>/admin/case-studies.php">All Case Studies</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/case-study-edit.php'); ?>" href="<?php echo BASE_URL; ?>/admin/case-study-edit.php">Add New</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_open('/blog-posts.php', '/blog-categories.php', '/blog-post-edit.php'); ?>" href="#blogSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?php echo admin_nav_open('/blog-posts.php', '/blog-categories.php', '/blog-post-edit.php') ? 'true' : 'false'; ?>">
                    <i class="bi bi-journal"></i> Blog
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="nav flex-column collapse <?php echo admin_nav_open('/blog-posts.php', '/blog-categories.php', '/blog-post-edit.php'); ?>" id="blogSubmenu">
                    <li><a class="nav-link <?php echo admin_nav_active('/blog-posts.php'); ?>" href="<?php echo BASE_URL; ?>/admin/blog-posts.php">All Posts</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/blog-categories.php'); ?>" href="<?php echo BASE_URL; ?>/admin/blog-categories.php">Categories</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/blog-post-edit.php'); ?>" href="<?php echo BASE_URL; ?>/admin/blog-post-edit.php">Add New</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_active('/faqs.php'); ?>" href="<?php echo BASE_URL; ?>/admin/faqs.php">
                    <i class="bi bi-question-circle"></i> FAQs
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_active('/testimonials.php'); ?>" href="<?php echo BASE_URL; ?>/admin/testimonials.php">
                    <i class="bi bi-chat-quote"></i> Testimonials
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_open('/crm-leads.php', '/crm-lead-edit.php'); ?>" href="#crmSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?php echo admin_nav_open('/crm-leads.php', '/crm-lead-edit.php') ? 'true' : 'false'; ?>">
                    <i class="bi bi-people"></i> CRM
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="nav flex-column collapse <?php echo admin_nav_open('/crm-leads.php', '/crm-lead-edit.php'); ?>" id="crmSubmenu">
                    <li><a class="nav-link <?php echo admin_nav_active('/crm-leads.php'); ?>" href="<?php echo BASE_URL; ?>/admin/crm-leads.php">All Leads</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/crm-lead-edit.php'); ?>" href="<?php echo BASE_URL; ?>/admin/crm-lead-edit.php">Add Lead</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_active('/enquiries.php'); ?>" href="<?php echo BASE_URL; ?>/admin/enquiries.php">
                    <i class="bi bi-envelope"></i> Enquiries
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_active('/media.php'); ?>" href="<?php echo BASE_URL; ?>/admin/media.php">
                    <i class="bi bi-images"></i> Media Library
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_open('/seo-tools.php', '/seo-settings.php'); ?>" href="#seoSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?php echo admin_nav_open('/seo-tools.php', '/seo-settings.php') ? 'true' : 'false'; ?>">
                    <i class="bi bi-robot"></i> SEO &amp; AI
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="nav flex-column collapse <?php echo admin_nav_open('/seo-tools.php', '/seo-settings.php'); ?>" id="seoSubmenu">
                    <li><a class="nav-link <?php echo admin_nav_active('/seo-tools.php'); ?>" href="<?php echo BASE_URL; ?>/admin/seo-tools.php">AI SEO Tools</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/seo-settings.php'); ?>" href="<?php echo BASE_URL; ?>/admin/seo-settings.php">SEO Settings</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_active('/analytics.php'); ?>" href="<?php echo BASE_URL; ?>/admin/analytics.php">
                    <i class="bi bi-graph-up"></i> Analytics
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo admin_nav_open('/settings.php', '/users.php', '/menu.php', '/footer-settings.php'); ?>" href="#settingsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?php echo admin_nav_open('/settings.php', '/users.php', '/menu.php', '/footer-settings.php') ? 'true' : 'false'; ?>">
                    <i class="bi bi-gear"></i> Settings
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="nav flex-column collapse <?php echo admin_nav_open('/settings.php', '/users.php', '/menu.php', '/footer-settings.php'); ?>" id="settingsSubmenu">
                    <li><a class="nav-link <?php echo admin_nav_active('/settings.php'); ?>" href="<?php echo BASE_URL; ?>/admin/settings.php">Site Settings</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/users.php'); ?>" href="<?php echo BASE_URL; ?>/admin/users.php">Users</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/menu.php'); ?>" href="<?php echo BASE_URL; ?>/admin/menu.php">Menu</a></li>
                    <li><a class="nav-link <?php echo admin_nav_active('/footer-settings.php'); ?>" href="<?php echo BASE_URL; ?>/admin/footer-settings.php">Footer</a></li>
                </ul>
            </li>

            <li class="nav-item mt-3 border-top border-dark border-opacity-10 pt-2">
                <a class="nav-link text-danger" href="<?php echo BASE_URL; ?>/admin/logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <button class="sidebar-collapse-btn d-none d-lg-flex" type="button" id="sidebarCollapse" aria-label="Collapse sidebar" title="Collapse sidebar">
        <i class="bi bi-chevron-left"></i>
    </button>
</aside>

<header class="admin-topbar">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-dark p-0 d-lg-none" type="button" id="mobileSidebarToggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="form-control" placeholder="Search..." id="adminSearch">
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                        <i class="bi bi-bell fs-5"></i>
                        <?php
                        try {
                            $db = getDB();
                            $notifStmt = $db->query("SELECT COUNT(*) FROM enquiries WHERE status = 'new'");
                            $notifCount = (int) $notifStmt->fetchColumn();
                            if ($notifCount > 0):
                        ?>
                            <span class="notification-badge"><?php echo $notifCount; ?></span>
                        <?php endif; } catch (Exception $e) {} ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="width:320px;">
                        <h6 class="dropdown-header px-0">Notifications</h6>
                        <?php
                        try {
                            $db = getDB();
                            $notifs = $db->query("SELECT id, name, company, email, created_at FROM enquiries WHERE status = 'new' ORDER BY created_at DESC LIMIT 5")->fetchAll();
                            if ($notifs):
                                foreach ($notifs as $n):
                        ?>
                            <a class="dropdown-item px-0 py-2" href="<?php echo BASE_URL; ?>/admin/enquiries.php?id=<?php echo (int)$n['id']; ?>">
                                <div class="fw-semibold" style="font-size:0.85rem;"><?php echo htmlspecialchars($n['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($n['company'] ?: $n['email'], ENT_QUOTES, 'UTF-8'); ?> &middot; <?php echo time_ago($n['created_at']); ?></small>
                            </a>
                        <?php endforeach; else: ?>
                            <p class="text-muted mb-0" style="font-size:0.85rem;">No new notifications</p>
                        <?php endif; } catch (Exception $e) { ?>
                            <p class="text-muted mb-0" style="font-size:0.85rem;">Notifications unavailable</p>
                        <?php } ?>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-link text-dark d-flex align-items-center gap-2 p-0 text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($adminUser['name'] ?? $adminUser['username'] ?? 'A', 0, 2)); ?>
                        </div>
                        <span class="d-none d-md-inline fw-semibold" style="font-size:0.9rem;"><?php echo htmlspecialchars($adminUser['name'] ?? $adminUser['username'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></span>
                        <i class="bi bi-chevron-down d-none d-md-inline" style="font-size:0.7rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/admin/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="admin-overlay" id="sidebarOverlay"></div>

<main class="admin-content">
    <div class="container-fluid py-4">
