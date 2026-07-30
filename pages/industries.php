<?php
$slug = 'industries';
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}

$industries = [];
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM industries WHERE status = 'published' ORDER BY sort_order ASC");
    $stmt->execute();
    $industries = $stmt->fetchAll();
} catch (Exception $e) {
    $industries = [];
}

// Static fallback
if (empty($industries)) {
    $industries = [
        ['id' => 1, 'title' => 'Retail', 'icon' => 'bi-shop', 'slug' => 'retail', 'description' => 'Managing shift-based workforces across multiple locations with fluctuating demand, seasonal peaks, and tight labour cost control.'],
        ['id' => 2, 'title' => 'Hospitality', 'icon' => 'bi-cup-hot', 'slug' => 'hospitality', 'description' => 'Balancing service levels with labour costs in hotels, restaurants, and leisure businesses where every shift matters.'],
        ['id' => 3, 'title' => 'Logistics & Distribution', 'icon' => 'bi-truck', 'slug' => 'logistics-distribution', 'description' => 'Managing warehouse and transport workforces with complex shift patterns, variable demand, and precise labour tracking.'],
        ['id' => 4, 'title' => 'Manufacturing', 'icon' => 'bi-gear-wide-connected', 'slug' => 'manufacturing', 'description' => 'Supporting production environments with shift-based rotas, overtime management, and labour cost visibility.'],
        ['id' => 5, 'title' => 'Healthcare & Care Services', 'icon' => 'bi-heart-pulse', 'slug' => 'healthcare-care-services', 'description' => 'Rostering clinical and care staff across shifts, managing compliance requirements, and maintaining service continuity.'],
        ['id' => 6, 'title' => 'Professional Services', 'icon' => 'bi-briefcase', 'slug' => 'professional-services', 'description' => 'Managing professional workforces with flexible working, client billing, and project-based time tracking.'],
        ['id' => 7, 'title' => 'Small & Medium Businesses', 'icon' => 'bi-building', 'slug' => 'small-medium-businesses', 'description' => 'Scalable workforce management solutions for growing businesses that need professional processes without enterprise complexity.'],
    ];
}
?>

<div class="page-header">
    <div class="container">
        <h1>Industries We Serve</h1>
        <p>Workforce management expertise across sectors — understanding the unique challenges of your industry</p>
    </div>
</div>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Industries</li>
            </ol>
        </nav>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <p class="lead">Every industry has its own workforce management challenges — from seasonal demand patterns and complex shift rotations to regulatory compliance and multi-site operations. We understand these nuances and tailor our approach to the specific needs of your sector. Whatever your industry, we bring practical, hands-on experience to help you manage your workforce more effectively.</p>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($industries as $industry): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($industry['id'] % 3) * 100; ?>">
                <div class="industry-card">
                    <div class="icon-circle">
                        <i class="<?php echo htmlspecialchars($industry['icon']); ?>"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($industry['title']); ?></h4>
                    <p><?php echo htmlspecialchars($industry['description']); ?></p>
                    <a href="/industries/<?php echo htmlspecialchars($industry['slug']); ?>" class="industry-link">View Solutions <i class="bi bi-arrow-right"></i></a>
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
                <h2 class="section-title mb-3">Cross-Sector Expertise</h2>
                <p>While every industry has its specific challenges, many workforce management principles apply across sectors. Our experience working with organisations of different sizes and types gives us a broad perspective that helps us identify solutions that have worked elsewhere and adapt them to your context.</p>
                <p>We do not believe in one-size-fits-all approaches. Every engagement starts with understanding your specific operational context — your shift patterns, demand cycles, workforce demographics, technology landscape, and business priorities. From there, we develop recommendations and solutions that fit your world, not a generic template.</p>
                <p>Our cross-sector experience means we can bring insights from one industry to another where relevant — for example, applying retail scheduling approaches to hospitality, or manufacturing labour cost tracking to logistics operations.</p>
            </div>
            <div class="col-lg-5">
                <div class="highlight-box">
                    <h4 class="mb-3"><i class="bi bi-lightbulb text-primary me-2"></i>Why Industry Knowledge Matters</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i>We understand your operational pressures</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i>We speak your language — not just tech jargon</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i>We know the compliance requirements that affect you</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i>We bring relevant examples and benchmarks</li>
                        <li><i class="bi bi-check-lg text-primary me-2"></i>We recommend what works in practice, not just in theory</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Not Sure Which Solutions Fit Your Industry?</h2>
        <p>Book a free consultation and we will discuss your specific challenges and recommend the most effective approach for your organisation.</p>
        <a href="/contact-us" class="btn-white btn-lg">
            <i class="bi bi-calendar-check"></i> Book a Free Consultation
        </a>
    </div>
</section>
