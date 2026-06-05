<?php
$slug = 'services';
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}

$services = [];
if ($pageData) {
    // Pull services from DB if available
    try {
        $stmt = $db->prepare("SELECT * FROM services WHERE status = 'published' ORDER BY sort_order ASC");
        $stmt->execute();
        $services = $stmt->fetchAll();
    } catch (Exception $e) {
        $services = [];
    }
}

// Static fallback
if (empty($services)) {
    $services = [
        ['id' => 1, 'title' => 'UKG Workforce Management Support', 'icon' => 'bi-clock', 'slug' => 'ukg-workforce-management-support', 'description' => 'Expert support for UKG time & attendance, scheduling, and reporting. Keep your workforce operations running smoothly with dedicated assistance for managers and system administrators.'],
        ['id' => 2, 'title' => 'Workforce Management Consulting', 'icon' => 'bi-people', 'slug' => 'workforce-management-consulting', 'description' => 'Strategic workforce process review, operational improvement, and impartial system guidance to help you get the most from your workforce management investment.'],
        ['id' => 3, 'title' => 'Implementation & Configuration', 'icon' => 'bi-gear', 'slug' => 'implementation-configuration-support', 'description' => 'Hands-on setup support, configuration planning, and go-live preparation for workforce management systems. We help ensure your implementation runs to plan.'],
        ['id' => 4, 'title' => 'Training & User Support', 'icon' => 'bi-person-workspace', 'slug' => 'training-user-support', 'description' => 'Practical, role-based training for managers, employees, and system administrators. Build confidence and capability across your organisation with tailored learning.'],
        ['id' => 5, 'title' => 'Managed Support Services', 'icon' => 'bi-headset', 'slug' => 'managed-support-services', 'description' => 'Ongoing system support, regular health checks, issue investigation, and proactive maintenance to keep your workforce management solution in peak condition.'],
        ['id' => 6, 'title' => 'Reporting & Data Support', 'icon' => 'bi-bar-chart', 'slug' => 'reporting-data-support', 'description' => 'Attendance, labour cost, overtime, and scheduling reports tailored to your business needs. Turn raw data into actionable workforce intelligence.'],
        ['id' => 7, 'title' => 'IT Consulting', 'icon' => 'bi-laptop', 'slug' => 'it-digital-solutions', 'description' => 'Practical IT strategy, cloud guidance, and business automation advice to modernise your technology stack and improve operational efficiency.'],
        ['id' => 8, 'title' => 'Web Development', 'icon' => 'bi-code-slash', 'slug' => 'web-development', 'description' => 'Custom websites, business portals, and digital platforms built with modern technologies. Responsive, accessible, and designed to support your business goals.'],
        ['id' => 9, 'title' => 'SEO & Digital Marketing', 'icon' => 'bi-search', 'slug' => 'seo-digital-marketing', 'description' => 'Improve your search visibility, attract qualified leads, and grow your digital presence with data-driven SEO and marketing strategies tailored for B2B organisations.'],
    ];
}
?>

<div class="page-header">
    <div class="container">
        <h1>Workforce Management, UKG Support & Digital Business Solutions</h1>
        <p>Helping organisations across the UK improve workforce operations, technology, and digital presence with practical, independent expertise</p>
    </div>
</div>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </nav>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <p class="lead text-center mb-5">At SWAMITIME SOLUTIONS LTD, we provide a comprehensive range of workforce management and digital business services designed to support organisations of all sizes. Whether you need UKG system support, workforce process consulting, or help building your digital presence, our team brings practical, hands-on expertise to every engagement. We work alongside your teams to deliver solutions that fit your operations, your people, and your budget.</p>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($services as $service): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($service['id'] % 3) * 100; ?>">
                <div class="service-card">
                    <div class="service-card-icon">
                        <i class="bi <?php echo htmlspecialchars($service['icon']); ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                    <a href="/<?php echo htmlspecialchars($service['slug']); ?>" class="btn-link-teal">Learn More <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="content-section bg-light">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <h2 class="section-title mb-3">How We Work</h2>
                <p>Every engagement starts with understanding your unique challenges and goals. We take time to listen, assess your current setup, and recommend practical steps that deliver results without unnecessary complexity.</p>
                <p>Our approach is flexible — whether you need a short-term project, ongoing support, or a full programme of work, we adapt to fit your requirements. We believe in transferring knowledge to your teams so you build internal capability alongside any external support we provide.</p>
                <ul>
                    <li>Initial consultation to understand your requirements</li>
                    <li>Transparent scoping with clear timelines and deliverables</li>
                    <li>Hands-on delivery with regular progress updates</li>
                    <li>Knowledge transfer and documentation throughout</li>
                    <li>Post-engagement support to ensure lasting results</li>
                </ul>
            </div>
            <div class="col-lg-5">
                <div class="highlight-box">
                    <h4 class="mb-3"><i class="bi bi-check2-circle text-primary me-2"></i>Why Choose Us</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i>Independent, impartial advice</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i>Practical hands-on experience</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i>Flexible engagement models</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i>Knowledge transfer focus</li>
                        <li><i class="bi bi-check-lg text-primary me-2"></i>UK-based support team</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Need Help Choosing the Right Service?</h2>
        <p>Every organisation is different. Book a free consultation to discuss your challenges and we will recommend the most effective approach for your business.</p>
        <a href="/contact-us" class="btn-white btn-lg">
            <i class="bi bi-calendar-check"></i> Book a Free Consultation
        </a>
        <div class="cta-trust">
            <span><i class="bi bi-telephone-fill me-1"></i> Or call us directly to discuss your requirements</span>
        </div>
    </div>
</section>
