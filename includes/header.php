<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- DNS prefetch + preconnect for CDN resources -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>

    <?php
    $current_slug = isset($request_uri) ? trim($request_uri, '/') : 'home';
    if (class_exists('SEO')) {
        SEO::head($current_slug);
    }

    // Favicon: check DB first, fall back to inline SVG
    $siteFavicon = '';
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'favicon_path' LIMIT 1");
        $stmt->execute();
        $siteFavicon = $stmt->fetchColumn() ?: '';
    } catch (Exception $e) {}
    $faviconPath = $siteFavicon ? (BASE_URL . '/' . ltrim($siteFavicon, '/')) : '';
    ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" media="print" onload="this.media='all';this.onload=null;">
    <link href="/assets/css/style.css" rel="stylesheet">

    <?php if ($faviconPath): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo $faviconPath; ?>">
    <?php else: ?>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%23004E53'/%3E%3Ctext x='16' y='22' font-family='Arial' font-weight='bold' font-size='18' fill='white' text-anchor='middle'%3EST%3C/text%3E%3C/svg%3E">
    <?php endif; ?>

    <style>
        :root {
            --teal-dark: #004E53;
            --teal-mid: #078E91;
            --teal-light: #0DB5B8;
            --teal-surface: #E6F4F4;
            --heading-font: 'Plus Jakarta Sans', sans-serif;
            --body-font: 'Inter', sans-serif;
        }

        body {
            font-family: var(--body-font);
            color: #1a1a1a;
            padding-top: 72px;
            overflow-x: hidden;
        }

        img { max-width: 100%; height: auto; }
        table { width: 100%; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        @media (max-width: 991px) {
            body { padding-top: 62px; }
        }
        @media (max-width: 767px) {
            body { padding-top: 58px; }
        }

        h1, h2, h3, h4, h5, h6,
        .navbar-brand,
        .btn,
        .nav-link {
            font-family: var(--heading-font);
        }

        .navbar-teal {
            background: #ffffff;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        }

        .navbar-teal .navbar-brand {
            font-weight: 800;
            font-size: 1.4rem;
            letter-spacing: 1.5px;
            color: #004E53;
            line-height: 1.15;
        }

        .navbar-teal .navbar-brand small {
            display: block;
            font-size: 0.55rem;
            font-weight: 500;
            letter-spacing: 4px;
            opacity: 0.7;
        }

        .navbar-teal .nav-link {
            color: #1a1a1a;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 0.85rem;
            transition: color 0.2s ease;
        }

        .navbar-teal .nav-link:hover,
        .navbar-teal .nav-link:focus,
        .navbar-teal .nav-link.active {
            color: #078E91;
        }

        .navbar-teal .nav-link.active {
            font-weight: 700;
        }

        .dropdown-menu-teal {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 78, 83, 0.15);
            padding: 0.75rem 0;
            margin-top: 0.5rem;
            min-width: 300px;
        }

        .dropdown-menu-teal .dropdown-item {
            font-family: var(--body-font);
            font-size: 0.875rem;
            padding: 0.6rem 1.25rem;
            color: #1a1a1a;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .dropdown-menu-teal .dropdown-item:hover,
        .dropdown-menu-teal .dropdown-item:focus {
            background: var(--teal-surface);
            color: var(--teal-dark);
        }

        .dropdown-menu-teal .dropdown-header {
            font-family: var(--heading-font);
            font-weight: 700;
            font-size: 0.7rem;
            letter-spacing: 1.5px;
            color: var(--teal-mid);
            text-transform: uppercase;
            padding: 0.4rem 1.25rem;
            margin-top: 0.25rem;
        }

        .btn-cta-light {
            background: #078E91;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 0.55rem 1.35rem;
            border-radius: 50px;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-cta-light:hover {
            background: #004E53;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 78, 83, 0.25);
        }

        .navbar-toggler {
            border: none;
            padding: 0.25rem;
            color: #078E91;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23078E91' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .offcanvas-teal {
            background: linear-gradient(180deg, #004E53 0%, #078E91 100%);
            color: #ffffff;
        }

        .offcanvas-teal .offcanvas-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .offcanvas-teal .offcanvas-title {
            font-family: var(--heading-font);
            font-weight: 800;
            letter-spacing: 1.5px;
        }

        .offcanvas-teal .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .offcanvas-teal .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-family: var(--heading-font);
            font-weight: 600;
            font-size: 1rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: background 0.2s ease;
        }

        .offcanvas-teal .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .offcanvas-teal .mobile-sub-link {
            font-family: var(--body-font);
            font-weight: 400;
            font-size: 0.85rem;
            padding: 0.5rem 1.5rem;
        }

        .offcanvas-teal .mobile-cta {
            margin: 1rem;
            display: block;
            text-align: center;
            background: #ffffff;
            color: var(--teal-dark);
            font-family: var(--heading-font);
            font-weight: 700;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .offcanvas-teal .mobile-cta:hover {
            background: var(--teal-light);
            color: #ffffff;
        }
    </style>
</head>
<body>

<!-- Pre-loader -->
<div id="preloader">
    <div class="preloader-spinner"></div>
</div>
<style>
#preloader{position:fixed;top:0;left:0;width:100%;height:100%;background:#ffffff;z-index:99999;display:flex;align-items:center;justify-content:center;transition:opacity 0.4s ease,visibility 0.4s ease;}
#preloader.fade-out{opacity:0;visibility:hidden;}
.preloader-spinner{width:44px;height:44px;border:3px solid #e0e0e0;border-top-color:#078E91;border-radius:50%;animation:spin 0.7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg)}}
</style>

<?php include_once __DIR__ . '/cookie-consent.php'; ?>

<?php
$siteLogo = '';
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'logo_path' LIMIT 1");
    $stmt->execute();
    $siteLogo = $stmt->fetchColumn() ?: '';
} catch (Exception $e) {}
// Fallback to default files if none uploaded from backend
if (empty($siteLogo) && file_exists(BASE_PATH . '/assets/images/logo.png')) {
    $siteLogo = 'assets/images/logo.png';
}

// -----------------------------------------------------------
// Fetch dynamic menu from database
// -----------------------------------------------------------
$menuItems = [];
$menuTree  = [];
try {
    $allMenu = $db->query("SELECT * FROM menu_items WHERE location IN ('header','both') AND status = 'active' ORDER BY sort_order ASC")->fetchAll();
    $menuItems = $allMenu;
    // Build tree: top-level items with their children
    foreach ($allMenu as $item) {
        if (empty($item['parent_id'])) {
            $menuTree[$item['id']] = $item;
        }
    }
    // Attach children to parents
    foreach ($allMenu as $item) {
        if (!empty($item['parent_id']) && isset($menuTree[$item['parent_id']])) {
            $menuTree[$item['parent_id']]['children'][] = $item;
        }
    }
} catch (Exception $e) {}

// Helper to render a menu item's active class
function menuActive(string $url, string $current): string {
    if ($url === '/' && ($current === '/' || $current === '/home')) return 'active';
    if ($url !== '/' && str_starts_with($current, $url)) return 'active';
    return '';
}
?>

<nav class="navbar navbar-expand-lg navbar-teal fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/">
            <?php if ($siteLogo): ?>
            <img src="<?php echo BASE_URL . '/' . ltrim($siteLogo, '/'); ?>" alt="SWAMITIME SOLUTIONS LTD" style="height:70px;">
            <?php else: ?>
            SWAMITIME
            <small>SOLUTIONS LTD</small>
            <?php endif; ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <?php foreach ($menuTree as $item):
                    $hasChildren = !empty($item['children']);
                    $itemUrl = htmlspecialchars($item['url'] ?? '#');
                    $itemTitle = htmlspecialchars($item['title'] ?? '');
                    $isActive = menuActive($item['url'] ?? '', $request_uri);
                    if ($hasChildren): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle<?php echo $isActive ? ' active' : ''; ?>" href="<?php echo $itemUrl; ?>" id="menuDropdown<?php echo $item['id']; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $itemTitle; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-teal" aria-labelledby="menuDropdown<?php echo $item['id']; ?>">
                        <?php foreach ($item['children'] as $child): ?>
                        <li><a class="dropdown-item" href="<?php echo htmlspecialchars($child['url'] ?? '#'); ?>"><?php echo htmlspecialchars($child['title'] ?? ''); ?></a></li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item fw-bold" href="<?php echo $itemUrl; ?>">View All &rarr;</a></li>
                    </ul>
                </li>
                    <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link<?php echo $isActive ? ' active' : ''; ?>" href="<?php echo $itemUrl; ?>"><?php echo $itemTitle; ?></a>
                </li>
                    <?php endif;
                endforeach; ?>
                <li class="nav-item ms-lg-2">
                    <a href="/contact-us" class="btn btn-cta-light">Book a Free Consultation</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-end offcanvas-teal" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileNavLabel"><?php if ($siteLogo): ?><img src="<?php echo BASE_URL . '/' . ltrim($siteLogo, '/'); ?>" alt="SWAMITIME" style="height:28px;"><?php else: ?>SWAMITIME<?php endif; ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav flex-column">
                <?php foreach ($menuTree as $item):
                    $hasChildren = !empty($item['children']);
                    $itemUrl = htmlspecialchars($item['url'] ?? '#');
                    $itemTitle = htmlspecialchars($item['title'] ?? '');
                    $isActive = menuActive($item['url'] ?? '', $request_uri);
                ?>
            <li class="nav-item">
                <a class="nav-link<?php echo $isActive ? ' active' : ''; ?>" href="<?php echo $itemUrl; ?>"><?php echo $itemTitle; ?></a>
                <?php if ($hasChildren): ?>
                <ul class="nav flex-column ms-3">
                    <?php foreach ($item['children'] as $child): ?>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="<?php echo htmlspecialchars($child['url'] ?? '#'); ?>"><?php echo htmlspecialchars($child['title'] ?? ''); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </li>
                <?php endforeach; ?>
        </ul>
        <a href="/contact-us" class="mobile-cta">Book a Free Consultation</a>
    </div>
</div>

<div class="main-content">
