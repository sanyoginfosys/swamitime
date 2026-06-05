<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- DNS prefetch + preconnect for CDN resources -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>

    <?php
    $current_slug = isset($request_uri) ? trim($request_uri, '/') : 'home';
    if (class_exists('SEO')) {
        SEO::head($current_slug);
    }
    ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" media="print" onload="this.media='all';this.onload=null;">
    <link href="/assets/css/style.css" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">

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
        }

        h1, h2, h3, h4, h5, h6,
        .navbar-brand,
        .btn,
        .nav-link {
            font-family: var(--heading-font);
        }

        .navbar-teal {
            background: linear-gradient(135deg, #004E53 0%, #078E91 100%);
        }

        .navbar-teal .navbar-brand {
            font-weight: 800;
            font-size: 1.4rem;
            letter-spacing: 1.5px;
            color: #ffffff;
            line-height: 1.15;
        }

        .navbar-teal .navbar-brand small {
            display: block;
            font-size: 0.55rem;
            font-weight: 500;
            letter-spacing: 4px;
            opacity: 0.8;
        }

        .navbar-teal .nav-link {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 0.85rem;
            transition: color 0.2s ease;
        }

        .navbar-teal .nav-link:hover,
        .navbar-teal .nav-link:focus,
        .navbar-teal .nav-link.active {
            color: #ffffff;
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
            background: #ffffff;
            color: var(--teal-dark);
            font-weight: 700;
            font-size: 0.8rem;
            padding: 0.55rem 1.35rem;
            border-radius: 50px;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-cta-light:hover {
            background: var(--teal-light);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .navbar-toggler {
            border: none;
            padding: 0.25rem;
            color: #ffffff;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
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

<?php include_once __DIR__ . '/cookie-consent.php'; ?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-teal fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/">
            SWAMITIME
            <small>SOLUTIONS LTD</small>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link<?php echo ($request_uri === '/' || $request_uri === '/home') ? ' active' : ''; ?>" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo ($request_uri === '/about-us') ? ' active' : ''; ?>" href="/about-us">About Us</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle<?php echo in_array($request_uri, ['/services','/ukg-workforce-management-support','/workforce-management-consulting','/implementation-configuration-support','/training-user-support','/managed-support-services','/reporting-data-support','/it-digital-solutions','/web-development','/seo-digital-marketing']) ? ' active' : ''; ?>" href="/services" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Services
                    </a>
                    <ul class="dropdown-menu dropdown-menu-teal dropdown-menu-lg-end" aria-labelledby="servicesDropdown">
                        <li><span class="dropdown-header">Workforce Management</span></li>
                        <li><a class="dropdown-item" href="/ukg-workforce-management-support">UKG Workforce Management Support</a></li>
                        <li><a class="dropdown-item" href="/workforce-management-consulting">Workforce Management Consulting</a></li>
                        <li><a class="dropdown-item" href="/implementation-configuration-support">Implementation & Configuration Support</a></li>
                        <li><a class="dropdown-item" href="/training-user-support">Training & User Support</a></li>
                        <li><a class="dropdown-item" href="/managed-support-services">Managed Support Services</a></li>
                        <li><a class="dropdown-item" href="/reporting-data-support">Reporting & Data Support</a></li>
                        <li><span class="dropdown-header">Digital Solutions</span></li>
                        <li><a class="dropdown-item" href="/it-digital-solutions">IT & Digital Solutions</a></li>
                        <li><a class="dropdown-item" href="/web-development">Web Development</a></li>
                        <li><a class="dropdown-item" href="/seo-digital-marketing">SEO & Digital Marketing</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item fw-bold" href="/services">View All Services &rarr;</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo ($request_uri === '/ukg-workforce-management-support') ? ' active' : ''; ?>" href="/ukg-workforce-management-support">UKG Support</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle<?php echo (strpos($request_uri, '/industries') === 0) ? ' active' : ''; ?>" href="/industries" id="industriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Industries
                    </a>
                    <ul class="dropdown-menu dropdown-menu-teal" aria-labelledby="industriesDropdown">
                        <li><a class="dropdown-item" href="/industries/retail">Retail</a></li>
                        <li><a class="dropdown-item" href="/industries/hospitality">Hospitality</a></li>
                        <li><a class="dropdown-item" href="/industries/logistics-distribution">Logistics & Distribution</a></li>
                        <li><a class="dropdown-item" href="/industries/manufacturing">Manufacturing</a></li>
                        <li><a class="dropdown-item" href="/industries/healthcare-care-services">Healthcare & Care Services</a></li>
                        <li><a class="dropdown-item" href="/industries/professional-services">Professional Services</a></li>
                        <li><a class="dropdown-item" href="/industries/small-medium-businesses">Small & Medium Businesses</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item fw-bold" href="/industries">View All Industries &rarr;</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo (strpos($request_uri, '/case-studies') === 0) ? ' active' : ''; ?>" href="/case-studies">Case Studies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo (strpos($request_uri, '/blog') === 0) ? ' active' : ''; ?>" href="/blog">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo ($request_uri === '/contact-us') ? ' active' : ''; ?>" href="/contact-us">Contact Us</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="/contact-us" class="btn btn-cta-light">Book a Free Consultation</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-end offcanvas-teal" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileNavLabel">SWAMITIME</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link<?php echo ($request_uri === '/' || $request_uri === '/home') ? ' active' : ''; ?>" href="/">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php echo ($request_uri === '/about-us') ? ' active' : ''; ?>" href="/about-us">About Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php echo in_array($request_uri, ['/services','/ukg-workforce-management-support','/workforce-management-consulting','/implementation-configuration-support','/training-user-support','/managed-support-services','/reporting-data-support','/it-digital-solutions','/web-development','/seo-digital-marketing']) ? ' active' : ''; ?>" href="/services">Services</a>
                <ul class="nav flex-column ms-3">
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/ukg-workforce-management-support">UKG Workforce Management Support</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/workforce-management-consulting">Workforce Management Consulting</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/implementation-configuration-support">Implementation & Configuration</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/training-user-support">Training & User Support</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/managed-support-services">Managed Support Services</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/reporting-data-support">Reporting & Data Support</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/it-digital-solutions">IT & Digital Solutions</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/web-development">Web Development</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/seo-digital-marketing">SEO & Digital Marketing</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php echo ($request_uri === '/ukg-workforce-management-support') ? ' active' : ''; ?>" href="/ukg-workforce-management-support">UKG Support</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php echo (strpos($request_uri, '/industries') === 0) ? ' active' : ''; ?>" href="/industries">Industries</a>
                <ul class="nav flex-column ms-3">
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/industries/retail">Retail</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/industries/hospitality">Hospitality</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/industries/logistics-distribution">Logistics & Distribution</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/industries/manufacturing">Manufacturing</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/industries/healthcare-care-services">Healthcare & Care Services</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/industries/professional-services">Professional Services</a></li>
                    <li class="nav-item"><a class="nav-link mobile-sub-link" href="/industries/small-medium-businesses">Small & Medium Businesses</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php echo (strpos($request_uri, '/case-studies') === 0) ? ' active' : ''; ?>" href="/case-studies">Case Studies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php echo (strpos($request_uri, '/blog') === 0) ? ' active' : ''; ?>" href="/blog">Blog</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php echo ($request_uri === '/contact-us') ? ' active' : ''; ?>" href="/contact-us">Contact Us</a>
            </li>
        </ul>
        <a href="/contact-us" class="mobile-cta">Book a Free Consultation</a>
    </div>
</div>

<div class="main-content">
