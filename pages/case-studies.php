<?php
$slug = 'case-studies';
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
$title = $pageData['title'] ?? 'Case Studies';

$caseStudies = [];
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM case_studies WHERE status = 'published' ORDER BY created_at DESC");
    $caseStudies = $stmt->fetchAll();
} catch (Exception $e) {
    $caseStudies = [];
}

if (empty($caseStudies)) {
    $caseStudies = [
        [
            'title' => 'Workforce Time Tracking Optimisation',
            'slug' => 'workforce-time-tracking-optimisation',
            'industry' => 'Professional Services',
            'excerpt' => 'How we helped a professional services firm streamline their time tracking processes, reduce payroll errors, and improve compliance across a multi-location workforce.',
            'icon' => 'bi-stopwatch'
        ],
        [
            'title' => 'Scheduling Process Improvement',
            'slug' => 'scheduling-process-improvement',
            'industry' => 'Logistics & Distribution',
            'excerpt' => 'Transforming shift scheduling for a national logistics provider, reducing overtime costs and improving employee satisfaction through fair, automated rostering.',
            'icon' => 'bi-calendar-check'
        ],
        [
            'title' => 'Reporting & Visibility Enhancement',
            'slug' => 'reporting-visibility-enhancement',
            'industry' => 'Healthcare & Care Services',
            'excerpt' => 'Delivering real-time workforce visibility and custom reporting dashboards for a healthcare organisation managing complex staffing requirements across multiple sites.',
            'icon' => 'bi-bar-chart-line'
        ]
    ];
}
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Case Studies</h1>
        <p>Real-world examples of how we have helped organisations improve their workforce management operations, reduce costs, and achieve operational excellence.</p>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Case Studies</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Case Studies Grid -->
<section class="section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($caseStudies as $index => $case): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="case-study-card">
                    <div class="case-study-img">
                        <i class="bi <?php echo htmlspecialchars($case['icon'] ?? 'bi-briefcase', ENT_QUOTES, 'UTF-8'); ?>" style="font-size: 3rem; position: relative; z-index: 1; opacity: 0.9;"></i>
                    </div>
                    <div class="case-study-body">
                        <span class="industry-tag"><?php echo htmlspecialchars($case['industry'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3><?php echo htmlspecialchars($case['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($case['excerpt'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <div style="margin-top: auto;">
                            <span style="font-size: 0.8rem; color: var(--muted-text); display: block; margin-bottom: 6px;">
                                Client: Confidential Client
                            </span>
                            <a href="/case-studies/<?php echo htmlspecialchars($case['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="btn-link-teal">
                                Read More <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Want to Be Our Next Case Study?</h2>
        <p>Every successful engagement starts with a conversation. Let us discuss your workforce management challenges and how we can help you achieve similar results.</p>
        <a href="/contact-us" class="btn-white">Start Your Journey <i class="bi bi-arrow-right"></i></a>
        <div class="cta-trust">No commitment &bull; Confidential discussion &bull; Tailored approach</div>
    </div>
</section>
