</div><!-- /.main-content -->

<footer class="site-footer">
    <div class="footer-main">
        <div class="container">
            <div class="row g-4 g-lg-5">

                <!-- Column 1: Company -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-brand mb-3">
                        <?php if (!empty($siteLogo)): ?>
                        <img src="<?php echo BASE_URL . '/' . ltrim($siteLogo, '/'); ?>" alt="SWAMITIME SOLUTIONS LTD" style="height:32px;filter:brightness(10);">
                        <?php else: ?>
                        <strong style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.3rem; letter-spacing: 1px; color: #ffffff;">SWAMITIME</strong>
                        <small class="d-block" style="font-size: 0.6rem; letter-spacing: 3px; opacity: 0.7; color: #ffffff;">SOLUTIONS LTD</small>
                        <?php endif; ?>
                    </div>
                    <p class="footer-desc">
                        Expert workforce management and digital solutions consultancy. UKG implementation partners delivering operational excellence across industries.
                    </p>
                    <div class="footer-social mt-3">
                        <a href="https://linkedin.com/company/swamitime" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a href="https://twitter.com/swamitime" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Twitter / X">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                    </div>
                </div>

                <?php
                // Fetch dynamic footer links grouped by section
                $footerSections = [];
                try {
                    $db = getDB();
                    $fLinks = $db->query("SELECT * FROM footer_links WHERE status = 'active' ORDER BY section, sort_order ASC")->fetchAll();
                    // Group by section
                    foreach ($fLinks as $link) {
                        $sec = $link['section'] ?? 'quick_links';
                        $footerSections[$sec][] = $link;
                    }
                } catch (Exception $e) {}
                // Column mapping: section key => column width, display name
                $footerColumns = [
                    'quick_links' => ['width' => 'col-lg-2 col-md-6', 'label' => 'Quick Links'],
                    'services'    => ['width' => 'col-lg-3 col-md-6', 'label' => 'Our Services'],
                    'ukg'         => ['width' => 'col-lg-2 col-md-6', 'label' => 'UKG Support'],
                    'legal'       => ['width' => 'col-lg-2 col-md-6', 'label' => 'Legal'],
                ];
                // Render only sections that have links
                foreach ($footerColumns as $secKey => $colMeta):
                    if (empty($footerSections[$secKey])) continue;
                ?>
                <div class="<?php echo $colMeta['width']; ?>">
                    <h6 class="footer-heading"><?php echo $colMeta['label']; ?></h6>
                    <ul class="footer-links">
                        <?php foreach ($footerSections[$secKey] as $link): ?>
                        <li><a href="<?php echo htmlspecialchars($link['url'] ?? '#'); ?>"><?php echo htmlspecialchars($link['title'] ?? ''); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>

                <!-- Column 5: Contact + Newsletter -->
                <div class="col-lg-2 col-md-6">
                    <?php
                    $footerEmail   = '';
                    $footerPhone   = '';
                    $footerAddress = '';
                    try {
                        $db = getDB();
                        $rows = $db->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('site_email','site_phone','site_address')")->fetchAll();
                        foreach ($rows as $r) {
                            if ($r['setting_key'] === 'site_email')   $footerEmail   = $r['setting_value'];
                            if ($r['setting_key'] === 'site_phone')   $footerPhone   = $r['setting_value'];
                            if ($r['setting_key'] === 'site_address') $footerAddress = $r['setting_value'];
                        }
                    } catch (Exception $e) {}
                    $hasContact = $footerEmail || $footerPhone || $footerAddress;
                    if ($hasContact):
                    ?>
                    <h6 class="footer-heading">Get in Touch</h6>
                    <ul class="footer-contact">
                        <?php if ($footerEmail): ?>
                        <li>
                            <i class="bi bi-envelope-fill"></i>
                            <a href="mailto:<?php echo htmlspecialchars($footerEmail); ?>"><?php echo htmlspecialchars($footerEmail); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if ($footerPhone): ?>
                        <li>
                            <i class="bi bi-telephone-fill"></i>
                            <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^\d+]/', '', $footerPhone)); ?>"><?php echo htmlspecialchars($footerPhone); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if ($footerAddress): ?>
                        <li>
                            <i class="bi bi-geo-alt-fill"></i>
                            <span><?php echo htmlspecialchars($footerAddress); ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                    <h6 class="footer-heading mt-3">Newsletter</h6>
                    <form class="newsletter-form mt-2" action="/subscribe" method="post">
                        <div class="input-group input-group-sm">
                            <input type="email" name="email" class="form-control" placeholder="Your email" required aria-label="Email for newsletter">
                            <button class="btn btn-teal-light" type="submit"><i class="bi bi-send-fill"></i></button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <p class="mb-1 mb-lg-0">
                        &copy; 2026 SWAMITIME SOLUTIONS LTD. All rights reserved.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <ul class="footer-legal">
                        <li><a href="/privacy-policy">Privacy Policy</a></li>
                        <li><a href="/terms-conditions">Terms & Conditions</a></li>
                        <li><a href="/cookie-policy">Cookie Policy</a></li>
                        <li><a href="<?php echo site_url('gdpr-compliance'); ?>">GDPR Compliance</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-disclaimer mt-3">
                <p>
                    The information provided on this website is for general informational purposes only. While we strive to keep content accurate and up to date, SWAMITIME SOLUTIONS LTD makes no representations or warranties of any kind, express or implied, about the completeness, accuracy, reliability, suitability, or availability of the information, products, services, or related graphics contained on the website. UKG and all UKG product names are trademarks of UKG Inc. SWAMITIME SOLUTIONS LTD is an independent consultancy and is not affiliated with, endorsed by, or sponsored by UKG Inc.
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
    .site-footer {
        background: #004E53;
        color: rgba(255, 255, 255, 0.85);
        font-family: 'Inter', sans-serif;
    }

    .footer-main {
        padding: 4rem 0 2.5rem;
    }

    .footer-brand {
        line-height: 1.2;
    }

    .footer-desc {
        font-size: 0.875rem;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0;
    }

    .footer-heading {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #ffffff;
        margin-bottom: 0.75rem;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.4rem;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.65);
        text-decoration: none;
        font-size: 0.85rem;
        transition: color 0.2s ease, padding-left 0.2s ease;
        display: inline-block;
    }

    .footer-links a:hover {
        color: #ffffff;
        padding-left: 4px;
    }

    .footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-contact li {
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        color: rgba(255, 255, 255, 0.75);
    }

    .footer-contact li i {
        font-size: 0.95rem;
        color: #0DB5B8;
        flex-shrink: 0;
        margin-top: 0.15rem;
    }

    .footer-contact a {
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer-contact a:hover {
        color: #ffffff;
    }

    .footer-social {
        display: flex;
        gap: 0.75rem;
    }

    .social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        font-size: 1.1rem;
        text-decoration: none;
        transition: background 0.3s ease, transform 0.3s ease;
    }

    .social-link:hover {
        background: #0DB5B8;
        color: #ffffff;
        transform: translateY(-2px);
    }

    .newsletter-form .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-size: 0.8rem;
        border-radius: 50px 0 0 50px;
        padding-left: 1rem;
    }

    .newsletter-form .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .newsletter-form .form-control:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #0DB5B8;
        box-shadow: none;
        color: #ffffff;
    }

    .btn-teal-light {
        background: #0DB5B8;
        border: 1px solid #0DB5B8;
        color: #ffffff;
        border-radius: 0 50px 50px 0;
        padding: 0.25rem 0.85rem;
        font-size: 0.85rem;
        transition: background 0.3s ease;
    }

    .btn-teal-light:hover {
        background: #0a9a9d;
        color: #ffffff;
    }

    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1.25rem 0 1.5rem;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.55);
    }

    .footer-bottom p {
        margin: 0;
    }

    .footer-legal {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        gap: 1.25rem;
        justify-content: flex-end;
    }

    .footer-legal li {
        display: inline;
    }

    .footer-legal a {
        color: rgba(255, 255, 255, 0.55);
        text-decoration: none;
        font-size: 0.8rem;
        transition: color 0.2s ease;
    }

    .footer-legal a:hover {
        color: #0DB5B8;
    }

    .footer-disclaimer {
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        padding-top: 0.75rem;
    }

    .footer-disclaimer p {
        font-size: 0.7rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.35);
        margin: 0;
    }

    @media (max-width: 991.98px) {
        .footer-legal {
            justify-content: flex-start;
            margin-top: 0.5rem;
            flex-wrap: wrap;
            gap: 0.75rem 1.25rem;
        }
    }
</style>

<script>
    // Hide preloader
    (function(){var p=document.getElementById('preloader');if(!p)return;function h(){setTimeout(function(){p.classList.add('fade-out')},200)}if(document.readyState==='complete')h();else window.addEventListener('load',h)})();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
<script src="/assets/js/main.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 800,
            once: true
        });
    });
</script>

</body>
</html>
