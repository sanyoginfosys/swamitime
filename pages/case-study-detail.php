<?php
$slug = $_GET['slug'] ?? '';
if (empty($slug)) { http_response_code(404); include __DIR__ . '/404.php'; return; }

$caseStudy = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM case_studies WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $caseStudy = $stmt->fetch();
} catch (Exception $e) {
    $caseStudy = null;
}

if (empty($caseStudy)) {
    $fallbackStudies = [
        'workforce-time-tracking-optimisation' => [
            'title' => 'Workforce Time Tracking Optimisation',
            'industry' => 'Professional Services',
            'icon' => 'bi-stopwatch',
            'challenge' => 'A mid-sized professional services firm with over 400 employees across four UK offices was struggling with inconsistent time tracking practices. Manual timesheets led to payroll errors, compliance gaps under the Working Time Regulations, and difficulty reconciling billable hours against client projects. The HR and finance teams spent an estimated 15 hours per week manually correcting and verifying timesheet data, and employees expressed frustration with the cumbersome process.',
            'solution' => 'SWAMITIME conducted a thorough audit of the existing time tracking workflows, employee scheduling patterns, and payroll integration points. We then designed and implemented a configured UKG workforce management solution tailored to the firm&rsquo;s multi-location structure. Key deliverables included:<br><br><ul><li>Automated time capture with configurable approval workflows</li><li>Integration with the existing payroll system to eliminate double-entry</li><li>Custom dashboards for real-time visibility of attendance, overtime, and leave balances</li><li>Role-based access controls ensuring managers could only view their respective teams</li><li>Comprehensive training programme delivered to HR, line managers, and end users</li></ul>',
            'result' => '<ul><li><strong>85% reduction</strong> in payroll processing time</li><li><strong>100% compliance</strong> with Working Time Regulations achieved</li><li><strong>HR admin time</strong> reduced from 15 hours to under 2 hours per week</li><li><strong>Employee satisfaction</strong> with time tracking process improved significantly, measured through post-implementation survey</li><li><strong>Billable hour accuracy</strong> improved by 12%, directly impacting revenue recognition</li></ul>'
        ],
        'scheduling-process-improvement' => [
            'title' => 'Scheduling Process Improvement',
            'industry' => 'Logistics & Distribution',
            'icon' => 'bi-calendar-check',
            'challenge' => 'A national logistics and distribution company operating multiple warehouses and a fleet of drivers faced significant scheduling inefficiencies. Shift patterns were managed across disparate spreadsheets managed by individual warehouse supervisors, leading to inconsistent shift allocation, frequent overtime spikes, and employee dissatisfaction due to perceived unfairness in shift distribution. Compliance with drivers&rsquo; hours regulations added another layer of complexity.',
            'solution' => 'We deployed a centralised workforce scheduling solution built on UKG Pro WFM, consolidating all scheduling activity into a single platform. Our approach included:<br><br><ul><li>Mapping and standardising scheduling rules across all sites, accounting for local variations and union agreements</li><li>Configuring automated shift generation based on forecast demand, employee availability, skills, and regulatory constraints</li><li>Implementing a fair shift allocation algorithm with employee preference capture</li><li>Building integrations with the existing HR system and time clocks</li><li>Delivering train-the-trainer sessions for supervisors and a mobile-friendly self-service portal for employees</li></ul>',
            'result' => '<ul><li><strong>30% reduction</strong> in overtime costs within the first quarter</li><li><strong>40% reduction</strong> in schedule-related employee grievances</li><li><strong>90% reduction</strong> in time spent creating and managing schedules across warehouse supervisors</li><li><strong>Full compliance</strong> with drivers&rsquo; hours regulations achieved through automated constraint checking</li><li><strong>Employee self-service adoption</strong> reached 85% within two months of launch</li></ul>'
        ],
        'reporting-visibility-enhancement' => [
            'title' => 'Reporting & Visibility Enhancement',
            'industry' => 'Healthcare & Care Services',
            'icon' => 'bi-bar-chart-line',
            'challenge' => 'A healthcare and care services provider managing multiple care homes and community support teams lacked consolidated visibility of their workforce. Staffing data was siloed across different systems and spreadsheets, making it near impossible to get a real-time view of staff deployment, attendance patterns, agency spend, and compliance status. Senior leadership could not make data-driven decisions about staffing levels, and the CQC inspection preparation was consistently stressful due to fragmented records.',
            'solution' => 'SWAMITIME designed and delivered a comprehensive reporting and analytics solution that aggregated data from the client&rsquo;s UKG Dimensions system, HR platform, and agency booking system into a unified reporting layer. Our work included:<br><br><ul><li>Building a centralised data warehouse that consolidated workforce data from multiple sources</li><li>Designing and deploying a suite of custom Power BI dashboards tailored to different stakeholder needs (C-suite, operational managers, compliance team, HR)</li><li>Automating CQC-ready compliance reports including staff-to-resident ratios, training compliance, and DBS check status tracking</li><li>Creating predictive staffing models that forecast resourcing needs based on historical occupancy and acuity data</li><li>Establishing automated scheduled report distribution via email for key stakeholders</li></ul>',
            'result' => '<ul><li><strong>100% visibility</strong> of workforce across all sites achieved within a single dashboard</li><li><strong>Agency spend reduced by 22%</strong> through better visibility of internal staff availability</li><li><strong>CQC inspection preparation time</strong> reduced from 3 weeks to 2 days</li><li><strong>Decision-making cycle</strong> for workforce planning shortened from monthly to weekly</li><li><strong>Staff compliance reporting</strong> now fully automated, saving an estimated 20 hours of admin time per month</li></ul>'
        ]
    ];

    if (isset($fallbackStudies[$slug])) {
        $caseStudy = $fallbackStudies[$slug];
        $caseStudy['client'] = 'Confidential Client';
    } else {
        http_response_code(404);
        include __DIR__ . '/404.php';
        return;
    }
}

$title = $caseStudy['title'] ?? 'Case Study';
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <span class="d-inline-block px-3 py-1 rounded-pill mb-3" style="background: rgba(255,255,255,0.15); font-size: 0.85rem; font-weight: 600;">
            <?php echo htmlspecialchars($caseStudy['industry'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <h1><?php echo htmlspecialchars($caseStudy['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p style="opacity: 0.85;">Client: <?php echo htmlspecialchars($caseStudy['client'] ?? 'Confidential Client', ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/case-studies">Case Studies</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($caseStudy['title'], ENT_QUOTES, 'UTF-8'); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Challenge Section -->
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-2">
                <div class="metric-icon mx-auto mb-3" style="width: 72px; height: 72px;">
                    <i class="<?php echo htmlspecialchars($caseStudy['icon'] ?? 'fa-solid fa-briefcase', ENT_QUOTES, 'UTF-8'); ?>" style="font-size: 32px;"></i>
                </div>
            </div>
            <div class="col-lg-10">
                <h2 class="section-title mb-3">The <span>Challenge</span></h2>
                <div style="color: var(--muted-text); line-height: 1.75; font-size: 1.05rem;">
                    <?php echo $caseStudy['challenge'] ?? ''; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Solution Section -->
<section class="section bg-light">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-2">
                <div class="metric-icon mx-auto mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-gear-fill" style="font-size: 32px;"></i>
                </div>
            </div>
            <div class="col-lg-10">
                <h2 class="section-title mb-3">Our <span>Solution</span></h2>
                <div style="color: var(--muted-text); line-height: 1.75; font-size: 1.05rem;">
                    <?php echo $caseStudy['solution'] ?? ''; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Results Section -->
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-2">
                <div class="metric-icon mx-auto mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-trophy-fill" style="font-size: 32px;"></i>
                </div>
            </div>
            <div class="col-lg-10">
                <h2 class="section-title mb-3">The <span>Results</span></h2>
                <div style="color: var(--muted-text); line-height: 1.75; font-size: 1.05rem;">
                    <?php echo $caseStudy['result'] ?? ''; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Interested in Similar Results?</h2>
        <p>Every organisation is different, but the principles that drive successful workforce management outcomes are consistent. Let us understand your unique context and help you achieve measurable improvements.</p>
        <a href="/contact-us" class="btn-white">Discuss Your Requirements <i class="bi bi-arrow-right"></i></a>
        <div class="cta-trust">Free initial consultation &bull; Confidential &bull; No obligation</div>
    </div>
</section>
