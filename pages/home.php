<?php
// ============================================================
// SWAMITIME SOLUTIONS LTD - Homepage Template
// ============================================================

$db = getDB();
$hasDb = ($db !== null);

$hpSections = [];
try {
    $rows = $db->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'hp_section_%'")->fetchAll();
    foreach ($rows as $r) { $hpSections[$r['setting_key']] = $r['setting_value']; }
} catch (Exception $e) {}

function hp_enabled(string $section): bool
{
    global $hpSections;
    $key = 'hp_section_' . $section;
    return ($hpSections[$key] ?? '1') === '1';
}

// -----------------------------------------------------------
// 1. Fetch Trust Metrics
// -----------------------------------------------------------
$trustMetrics = [];
if ($hasDb) {
    try {
        $trustMetrics = $db->query("SELECT * FROM trust_metrics WHERE status = 'active' ORDER BY sort_order ASC LIMIT 4")->fetchAll();
    } catch (Exception $e) {
        $trustMetrics = [];
    }
}

// -----------------------------------------------------------
// 2. Fetch Services
// -----------------------------------------------------------
$services = [];
if ($hasDb) {
    try {
        $services = $db->query("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC LIMIT 9")->fetchAll();
    } catch (Exception $e) {
        $services = [];
    }
}

// -----------------------------------------------------------
// 3. Fetch Industries
// -----------------------------------------------------------
$industries = [];
if ($hasDb) {
    try {
        $industries = $db->query("SELECT * FROM industries WHERE status = 'active' ORDER BY sort_order ASC LIMIT 7")->fetchAll();
    } catch (Exception $e) {
        $industries = [];
    }
}

// -----------------------------------------------------------
// Industry extended descriptions for tooltip / hover detail
// -----------------------------------------------------------
$industryDetails = [
    'retail' => 'From high-street stores to nationwide chains, we help retail businesses optimise shift patterns, manage seasonal peaks, and integrate time & attendance with payroll for maximum efficiency.',
    'hospitality' => 'Hotels, restaurants, and leisure venues benefit from dynamic scheduling tools, absence management, and labour cost controls designed for the unique demands of hospitality.',
    'logistics-distribution' => 'Our logistics workforce solutions address 24/7 shift planning, driver compliance, warehouse staffing, and real-time visibility across multi-site distribution networks.',
    'manufacturing' => 'Production-line staffing, skills tracking, certification management, and health & safety compliance — we help manufacturers keep operations running safely and efficiently.',
    'healthcare-care-services' => 'CQC-compliant workforce solutions with clinical rota management, mandatory training tracking, agency spend control, and safe staffing alerts for healthcare providers.',
    'professional-services' => 'Billable hours tracking, resource utilisation dashboards, project profitability analysis, and flexible time capture for remote and hybrid professional services teams.',
    'small-medium-businesses' => 'Cloud-based workforce management with quick deployment, automated time tracking, built-in compliance tools, and flexible pricing models designed for growing SMEs.',
];

// -----------------------------------------------------------
// 4. Fetch Case Studies
// -----------------------------------------------------------
$caseStudies = [];
if ($hasDb) {
    try {
        $caseStudies = $db->query("SELECT * FROM case_studies WHERE status = 'published' ORDER BY sort_order ASC LIMIT 3")->fetchAll();
    } catch (Exception $e) {
        $caseStudies = [];
    }
}

// -----------------------------------------------------------
// 5. Fetch Blog Posts
// -----------------------------------------------------------
$blogPosts = [];
if ($hasDb) {
    try {
        $blogPosts = $db->query("SELECT bp.*, bc.name AS category_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id WHERE bp.status = 'published' ORDER BY bp.published_at DESC LIMIT 3")->fetchAll();
    } catch (Exception $e) {
        $blogPosts = [];
    }
}

// -----------------------------------------------------------
// 6. Fetch FAQs
// -----------------------------------------------------------
$faqs = [];
if ($hasDb) {
    try {
        $faqs = $db->query("SELECT * FROM faqs WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6")->fetchAll();
    } catch (Exception $e) {
        $faqs = [];
    }
}

// -----------------------------------------------------------
// 7. Fetch CTA Block
// -----------------------------------------------------------
$ctaBlock = null;
if ($hasDb) {
    try {
        $stmt = $db->prepare("SELECT * FROM cta_blocks WHERE page_location = 'home_bottom' AND status = 'active' LIMIT 1");
        $stmt->execute();
        $ctaBlock = $stmt->fetch();
    } catch (Exception $e) {
        $ctaBlock = null;
    }
}
?>

<!-- ============================================================ -->
<!-- SECTION 1: HERO -->
<!-- ============================================================ -->
<?php if (hp_enabled('hero')): ?>
<section class="hero-section ai-pattern">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7" data-aos="fade-up">
                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-award-fill"></i> UK-Based Consultancy</span>
                    <span class="hero-badge"><i class="bi bi-star-fill"></i> UKG Specialists</span>
                    <span class="hero-badge"><i class="bi bi-shield-check"></i> B2B Focused</span>
                    <span class="hero-badge"><i class="bi bi-lightbulb-fill"></i> Independent Advice</span>
                </div>
                <h1 class="hero-title">Workforce Technology <span class="highlight">Made Practical</span></h1>
                <p class="hero-subtitle">We help businesses across the UK improve workforce operations with independent, hands-on expertise in UKG systems, workforce management consulting, and digital solutions — without the jargon, complexity, or unnecessary upsell. From single-site SMEs to multi-location enterprises, we deliver practical improvements that make a real difference to your bottom line.</p>
                <div class="hero-buttons">
                    <a href="/contact-us" class="btn-primary-gradient">
                        <i class="bi bi-calendar-check"></i> Book a Free Consultation
                    </a>
                    <a href="/services" class="btn-outline-light-white">
                        <i class="bi bi-arrow-right-circle"></i> Explore Our Services
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="stat-number">10+</div>
                        <div class="stat-label">Years Industry Experience</div>
                    </div>
                    <div class="hero-stat">
                        <div class="stat-number">200+</div>
                        <div class="stat-label">Organisations Supported</div>
                    </div>
                    <div class="hero-stat">
                        <div class="stat-number">15,000+</div>
                        <div class="stat-label">End Users Empowered</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5" data-aos="fade-left" data-aos-delay="200">
                <div class="hero-dashboard">
                    <div class="dashboard-mockup float-animation">
                        <div class="dash-header">
                            <span class="dash-dot red"></span>
                            <span class="dash-dot yellow"></span>
                            <span class="dash-dot green"></span>
                            <span class="dash-title">Workforce Analytics Dashboard</span>
                        </div>
                        <div class="dash-body">
                            <div class="dash-grid">
                                <div class="dash-card">
                                    <div class="dash-value">98.7%</div>
                                    <div class="dash-label">Attendance Rate</div>
                                </div>
                                <div class="dash-card">
                                    <div class="dash-value">2.4h</div>
                                    <div class="dash-label">Avg. Response Time</div>
                                </div>
                                <div class="dash-card">
                                    <div class="dash-value">15,000+</div>
                                    <div class="dash-label">Active Users</div>
                                </div>
                            </div>
                            <div class="dash-chart">
                                <div class="dash-bar" style="height: 75%"></div>
                                <div class="dash-bar" style="height: 90%"></div>
                                <div class="dash-bar" style="height: 65%"></div>
                                <div class="dash-bar" style="height: 85%"></div>
                                <div class="dash-bar" style="height: 95%"></div>
                                <div class="dash-bar" style="height: 70%"></div>
                                <div class="dash-bar" style="height: 88%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="float-decor top-left">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="float-decor bottom-right">
                        <span>UKG</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 2: TRUST METRICS -->
<!-- ============================================================ -->
<?php if (hp_enabled('metrics') && !empty($trustMetrics)): ?>
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Proven Results</span>
            <h2 class="section-title">Delivering Measurable <span>Workforce Improvements</span></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Our clients consistently achieve smarter workforce processes, reduced administrative burden, and better operational visibility through our independent, hands-on approach.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($trustMetrics as $metric): ?>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($metric['sort_order'] ?? 0) * 100; ?>">
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="<?php echo htmlspecialchars($metric['icon'] ?? 'fa-solid fa-check-circle'); ?>"></i>
                    </div>
                    <div class="metric-number">
                        <?php echo htmlspecialchars($metric['value'] ?? '—'); ?>
                    </div>
                    <h4 class="mt-2 mb-1" style="font-size:1.05rem;font-weight:700;"><?php echo htmlspecialchars($metric['title'] ?? ''); ?></h4>
                    <p class="metric-label"><?php echo htmlspecialchars($metric['description'] ?? ''); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 3: SERVICE PILLARS -->
<!-- ============================================================ -->
<?php if (hp_enabled('services') && !empty($services)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">What We Do</span>
            <h2 class="section-title">Comprehensive <span>Business Solutions</span></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">From UKG workforce management support to web development and digital marketing, we offer a complete range of services designed to help your business operate more efficiently and grow sustainably.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($services as $index => $service): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                <div class="service-card<?php echo ($index === 0) ? ' featured' : ''; ?>">
                    <?php if ($index === 0): ?>
                    <span class="featured-badge">Most Popular</span>
                    <?php endif; ?>
                    <div class="service-card-icon">
                        <i class="<?php echo htmlspecialchars($service['icon'] ?? 'fa-solid fa-check-circle'); ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($service['title'] ?? ''); ?></h3>
                    <p><?php echo htmlspecialchars($service['short_description'] ?? ($service['description'] ?? '')); ?></p>
                    <a href="/<?php echo htmlspecialchars($service['slug'] ?? '#'); ?>" class="btn-link-teal">Learn More <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 4: WORKFORCE SOLUTION OVERVIEW -->
<!-- ============================================================ -->
<?php if (hp_enabled('workforce')): ?>
<section class="section bg-light">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 order-lg-2" data-aos="fade-left">
                <div class="section-header text-start">
                    <span class="section-label">Our Approach</span>
                    <h2 class="section-title">Workforce Solutions Built <span>Around Your Business</span></h2>
                    <div class="section-divider"></div>
                </div>
                <p class="mb-4">Every organisation is different. That&rsquo;s why we don&rsquo;t offer one-size-fits-all packages or push you towards the most expensive option. We take time to understand your current operations, workforce challenges, and business goals before recommending a practical path forward.</p>
                <p class="mb-4">Whether you need help fixing a broken scheduling process, implementing UKG Dimensions across multiple sites, building custom workforce reports, or training your managers to get more from your existing systems — we work alongside your team to deliver results that stick.</p>
                <p class="mb-4">We do not believe in black-box consulting where you pay for a report and never see the consultant again. We stay engaged throughout delivery, testing, training, and post-go-live support. Our goal is to make your team self-sufficient — but we remain available as your trusted advisor whenever you need us.</p>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="value-icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;background:var(--light-bg);">
                                <i class="bi bi-check2-circle text-primary fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-1" style="font-size:0.95rem;">Independent Advice</h5>
                                <p class="mb-0" style="font-size:0.88rem;color:var(--muted-text);">No vendor ties or sales quotas influencing our recommendations.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="value-icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;background:var(--light-bg);">
                                <i class="bi bi-check2-circle text-primary fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-1" style="font-size:0.95rem;">Hands-On Delivery</h5>
                                <p class="mb-0" style="font-size:0.88rem;color:var(--muted-text);">We configure, build, test, and train — not just produce slide decks.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="value-icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;background:var(--light-bg);">
                                <i class="bi bi-check2-circle text-primary fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-1" style="font-size:0.95rem;">Knowledge Transfer</h5>
                                <p class="mb-0" style="font-size:0.88rem;color:var(--muted-text);">We build your team&rsquo;s capability so you become self-sufficient.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="value-icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;background:var(--light-bg);">
                                <i class="bi bi-check2-circle text-primary fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-1" style="font-size:0.95rem;">Flexible Engagement</h5>
                                <p class="mb-0" style="font-size:0.88rem;color:var(--muted-text);">Project-based, retainer, or fully managed — we adapt to your needs.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="/about-us" class="btn-teal">About Our Company <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="col-lg-6 order-lg-1" data-aos="fade-right">
                <div class="position-relative">
                    <div class="dashboard-mockup">
                        <div class="dash-header">
                            <span class="dash-dot red"></span>
                            <span class="dash-dot yellow"></span>
                            <span class="dash-dot green"></span>
                            <span class="dash-title">Workforce Overview — Live View</span>
                        </div>
                        <div class="dash-body">
                            <div class="dash-grid mb-3">
                                <div class="dash-card">
                                    <div class="dash-value">1,247</div>
                                    <div class="dash-label">Scheduled Today</div>
                                </div>
                                <div class="dash-card">
                                    <div class="dash-value">96.3%</div>
                                    <div class="dash-label">On-Time Rate</div>
                                </div>
                                <div class="dash-card">
                                    <div class="dash-value">18</div>
                                    <div class="dash-label">Open Shifts</div>
                                </div>
                            </div>
                            <div class="dash-grid">
                                <div class="dash-card">
                                    <div class="dash-value">4.2%</div>
                                    <div class="dash-label">Absence Rate</div>
                                </div>
                                <div class="dash-card">
                                    <div class="dash-value">£24.8k</div>
                                    <div class="dash-label">Daily Labour Cost</div>
                                </div>
                                <div class="dash-card">
                                    <div class="dash-value">88%</div>
                                    <div class="dash-label">Schedule Efficiency</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="about-stats-card" style="position:absolute;bottom:-20px;right:-20px;background:var(--gradient);color:#fff;border-radius:var(--radius-lg);padding:24px 20px;text-align:center;box-shadow:var(--shadow-lg);min-width:140px;">
                        <div class="stat-number" style="font-size:1.8rem;font-weight:800;font-family:var(--font-heading);line-height:1;">98%</div>
                        <div class="stat-label" style="font-size:0.8rem;opacity:0.85;margin-top:4px;">Client Retention Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 5: AI-POWERED DIGITAL OPERATIONS -->
<!-- ============================================================ -->
<?php if (hp_enabled('ai')): ?>
<section class="section bg-dark">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Digital Operations</span>
            <h2 class="section-title">AI-Powered <span style="color:#0AB0B4;">Workforce Intelligence</span></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Modern workforce management goes beyond time tracking. We help you harness automation, predictive analytics, and intelligent scheduling to transform how your business operates — reducing costs, improving accuracy, and freeing your people to focus on what matters most.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="service-card" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1);">
                    <div class="service-card-icon" style="background:rgba(255,255,255,0.1);">
                        <i class="bi bi-cpu" style="color:#0AB0B4;"></i>
                    </div>
                    <h3 style="color:#fff;">Automated Scheduling</h3>
                    <p style="color:rgba(255,255,255,0.8);">Intelligent scheduling algorithms that match workforce supply to demand forecasts, reducing overstaffing and understaffing while respecting employee preferences and compliance rules. Our solutions integrate with your existing UKG or WFM platform to automate shift creation, manage leave requests, and handle last-minute changes without manual intervention.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1);">
                    <div class="service-card-icon" style="background:rgba(255,255,255,0.1);">
                        <i class="bi bi-graph-up-arrow" style="color:#0AB0B4;"></i>
                    </div>
                    <h3 style="color:#fff;">Predictive Analytics</h3>
                    <p style="color:rgba(255,255,255,0.8);">Anticipate attendance trends, absence patterns, and labour demand using historical data and machine learning models. Make proactive decisions instead of reactive fixes. Our analytics dashboards give you real-time visibility into key metrics like labour cost per hour, overtime trends, and staffing gaps — enabling data-driven workforce planning that saves money and improves service levels.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1);">
                    <div class="service-card-icon" style="background:rgba(255,255,255,0.1);">
                        <i class="bi bi-robot" style="color:#0AB0B4;"></i>
                    </div>
                    <h3 style="color:#fff;">Process Automation</h3>
                    <p style="color:rgba(255,255,255,0.8);">Eliminate repetitive manual tasks — timesheet approvals, absence notifications, compliance reporting, payroll data entry — freeing your HR and management teams to focus on strategic priorities. We configure automated workflows that reduce processing time by up to 85%, eliminate data entry errors, and ensure consistent compliance with working time regulations and industry standards.</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <p style="color:rgba(255,255,255,0.7);max-width:650px;margin:0 auto 20px;font-size:0.95rem;">Whether you are just starting your automation journey or looking to enhance existing capabilities, our team can assess your current setup and recommend practical, cost-effective steps to embed intelligent automation across your workforce operations.</p>
            <a href="/contact-us" class="btn-outline-light-white">Discuss Automation Opportunities <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 6: PROCESS TIMELINE -->
<!-- ============================================================ -->
<?php if (hp_enabled('process')): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">How We Work</span>
            <h2 class="section-title">Our Proven <span>Delivery Process</span></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Every engagement follows a structured, transparent methodology designed to deliver results efficiently while keeping you informed at every stage.</p>
        </div>
        <div class="timeline">
            <div class="timeline-progress" style="width:0%;" id="timelineProgress"></div>
            <div class="row">
                <div class="col timeline-step" data-aos="fade-up" data-aos-delay="0">
                    <div class="timeline-number">1</div>
                    <h4>Discover</h4>
                    <p>We start by understanding your business, current processes, pain points, and objectives through structured discovery workshops with key stakeholders across your organisation. We map your existing workflows, document requirements, and align on success criteria.</p>
                </div>
                <div class="col timeline-step" data-aos="fade-up" data-aos-delay="100">
                    <div class="timeline-number">2</div>
                    <h4>Analyse</h4>
                    <p>We assess your existing systems, data quality, and operational workflows to identify gaps, inefficiencies, and opportunities for improvement. This stage produces a clear gap analysis and a prioritised recommendation report.</p>
                </div>
                <div class="col timeline-step" data-aos="fade-up" data-aos-delay="200">
                    <div class="timeline-number">3</div>
                    <h4>Configure</h4>
                    <p>We design and configure the solution to match your requirements — setting up business rules, pay policies, scheduling patterns, integrations, and custom reports. Rigorous testing ensures everything works correctly before go-live.</p>
                </div>
                <div class="col timeline-step" data-aos="fade-up" data-aos-delay="300">
                    <div class="timeline-number">4</div>
                    <h4>Train</h4>
                    <p>We deliver tailored training for managers, HR teams, payroll staff, and end users — on-site, remotely, or via train-the-trainer programmes. We provide user guides, quick-reference materials, and floor-walking support during go-live.</p>
                </div>
                <div class="col timeline-step" data-aos="fade-up" data-aos-delay="400">
                    <div class="timeline-number">5</div>
                    <h4>Support</h4>
                    <p>We provide ongoing support, regular system health checks, and continuous optimisation to ensure lasting results. Our managed support services offer 24/7 coverage, proactive monitoring, and a single point of accountability for all workforce system needs.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 7: INDUSTRIES SERVED -->
<!-- ============================================================ -->
<?php if (hp_enabled('industries') && !empty($industries)): ?>
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Industries</span>
            <h2 class="section-title"><span>Industries</span> We Serve</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">We bring deep sector-specific knowledge to every engagement, understanding the unique workforce challenges and regulatory requirements of each industry we work with.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($industries as $index => $industry): ?>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 4) * 100; ?>">
                <div class="industry-card">
                    <div class="icon-circle">
                        <i class="<?php echo htmlspecialchars($industry['icon'] ?? 'fa-solid fa-building'); ?>"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($industry['title'] ?? ''); ?></h4>
                    <p><?php echo htmlspecialchars($industry['short_description'] ?? ''); ?></p>
                    <a href="/industries/<?php echo htmlspecialchars($industry['slug'] ?? '#'); ?>" class="industry-link">Learn More <i class="bi bi-chevron-right"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="/industries" class="btn-teal">View All Industries <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 8: CASE STUDY PREVIEW -->
<!-- ============================================================ -->
<?php if (hp_enabled('case_studies') && !empty($caseStudies)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Success Stories</span>
            <h2 class="section-title">Proven <span>Client Results</span></h2>
            <div class="section-divider"></div>
                <p class="section-subtitle">Explore how we have helped organisations across different industries transform their workforce management operations and achieve measurable business outcomes.</p>
                <p class="section-subtitle" style="font-size:0.9rem;max-width:600px;">All case studies represent real client engagements. Client identities are kept confidential in accordance with our service agreements, but the results and metrics presented are verified and accurate.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($caseStudies as $index => $caseStudy): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="case-study-card">
                    <div class="case-study-img">
                        <div class="case-logo"><?php echo htmlspecialchars(substr($caseStudy['industry'] ?? 'C', 0, 2)); ?></div>
                    </div>
                    <div class="case-study-body">
                        <span class="industry-tag"><?php echo htmlspecialchars($caseStudy['industry'] ?? ''); ?></span>
                        <h3><?php echo htmlspecialchars($caseStudy['title'] ?? ''); ?></h3>
                        <p><?php echo htmlspecialchars(truncate(strip_tags($caseStudy['challenge'] ?? ''), 120)); ?></p>
                        <div class="case-metrics">
                            <?php if (!empty($caseStudy['result'])): ?>
                            <div class="case-metric">
                                <div class="value" style="font-size:0.9rem;line-height:1.5;"><?php echo $caseStudy['result']; ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <a href="/case-studies/<?php echo htmlspecialchars($caseStudy['slug'] ?? '#'); ?>" class="btn-link-teal mt-3">Read Full Case Study <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="/case-studies" class="btn-teal">View All Case Studies <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 9: WHY CHOOSE SWAMITIME -->
<!-- ============================================================ -->
<?php if (hp_enabled('why_choose')): ?>
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Why Us</span>
            <h2 class="section-title">Why Choose <span>SWAMITIME</span></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">In a market crowded with generalist consultancies and vendor-tied resellers, we stand apart by offering genuinely independent, practical expertise that puts your business first.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="mt-3 mb-2" style="font-size:1.1rem;font-weight:700;">Independent &amp; Impartial</h4>
                    <p class="metric-label">We have no vendor partnerships, reseller agreements, or sales quotas. Every recommendation is based solely on what is right for <em>your</em> business — never influenced by commission or product targets.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h4 class="mt-3 mb-2" style="font-size:1.1rem;font-weight:700;">Deep Sector Expertise</h4>
                    <p class="metric-label">Our consultants have years of hands-on experience across retail, hospitality, logistics, manufacturing, healthcare, and professional services — so we understand your challenges from day one.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="bi bi-lightbulb-fill"></i>
                    </div>
                    <h4 class="mt-3 mb-2" style="font-size:1.1rem;font-weight:700;">Practical, Not Theoretical</h4>
                    <p class="metric-label">We focus on delivering working solutions, not just slide decks and strategy documents. We configure, build, test, train, and support — rolling up our sleeves to get the job done right.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <h4 class="mt-3 mb-2" style="font-size:1.1rem;font-weight:700;">Long-Term Partnership</h4>
                    <p class="metric-label">We invest in understanding your business deeply and remain available long after go-live. Most clients stay with us for years, not months, because we deliver consistent value.</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="350">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <div class="quote-icon"><i class="bi bi-quote"></i></div>
                    <p class="quote">SWAMITIME transformed our workforce management operations. Their UKG implementation was seamless, and the ongoing managed support has been exceptional. We have seen significant improvements in payroll accuracy and scheduling efficiency across all our stores.</p>
                    <div class="author">
                        <div class="author-avatar">C</div>
                        <div class="author-info">
                            <h5>Client A</h5>
                            <span>Director, UK Retail Company</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <div class="quote-icon"><i class="bi bi-quote"></i></div>
                    <p class="quote">The managed support service from SWAMITIME has been a game-changer for our business. System downtime is virtually eliminated, and their team responds to issues faster than we ever experienced with direct vendor support. Highly recommended.</p>
                    <div class="author">
                        <div class="author-avatar">C</div>
                        <div class="author-info">
                            <h5>Client B</h5>
                            <span>Operations Director, UK Logistics Provider</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="450">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <div class="quote-icon"><i class="bi bi-quote"></i></div>
                    <p class="quote">The workforce analytics solution SWAMITIME delivered has given us unprecedented visibility into our staffing costs and utilisation. We have reduced agency spend significantly and now have the data we need for strategic workforce planning.</p>
                    <div class="author">
                        <div class="author-avatar">C</div>
                        <div class="author-info">
                            <h5>Client C</h5>
                            <span>HR Director, UK Healthcare Group</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 10: BLOG PREVIEW -->
<!-- ============================================================ -->
<?php if (hp_enabled('blog') && !empty($blogPosts)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Insights</span>
            <h2 class="section-title">Latest <span>Thinking &amp; Insights</span></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Stay informed with our latest articles on workforce management, UKG best practices, digital transformation, and business technology trends.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($blogPosts as $index => $post): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="blog-card">
                    <div class="blog-card-img">
                        <span class="category-badge"><?php echo htmlspecialchars($post['category_name'] ?? 'General'); ?></span>
                        <div style="display:flex;align-items:center;justify-content:center;color:#fff;opacity:0.3;font-size:4rem;">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <span class="read-time"><?php echo isset($post['published_at']) ? format_date($post['published_at'], 'd M Y') : ''; ?></span>
                    </div>
                    <div class="blog-card-body">
                        <div class="date"><i class="bi bi-person"></i> <?php echo htmlspecialchars($post['author'] ?? 'SWAMITIME SOLUTIONS LTD'); ?></div>
                        <h3><a href="/blog/<?php echo htmlspecialchars($post['slug'] ?? '#'); ?>"><?php echo htmlspecialchars($post['title'] ?? ''); ?></a></h3>
                        <p><?php echo htmlspecialchars(truncate($post['excerpt'] ?? '', 130)); ?></p>
                        <a href="/blog/<?php echo htmlspecialchars($post['slug'] ?? '#'); ?>" class="btn-link-teal">Read Article <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="/blog" class="btn-teal">View All Articles <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 11: HIGH-CONVERSION CTA -->
<!-- ============================================================ -->
<?php if (hp_enabled('cta')): ?>
<section class="cta-section">
    <div class="container">
        <h2><?php echo htmlspecialchars($ctaBlock['title'] ?? 'Ready to Transform Your Workforce Management?'); ?></h2>
        <p><?php echo htmlspecialchars($ctaBlock['subtitle'] ?? 'Book a free consultation with our experts and discover how we can help optimise your workforce operations, reduce costs, and drive measurable business improvements.'); ?></p>
        <a href="<?php echo htmlspecialchars($ctaBlock['button_url'] ?? '/contact-us'); ?>" class="btn-white pulse-glow">
            <i class="bi bi-calendar-check"></i> <?php echo htmlspecialchars($ctaBlock['button_text'] ?? 'Book a Free Consultation'); ?>
        </a>
        <div class="cta-trust">
            <i class="bi bi-shield-lock"></i> No obligation &bull; Confidential discussion &bull; Tailored recommendations
        </div>
        <div class="cta-trust" style="margin-top:12px;font-size:0.8rem;">
            <i class="bi bi-telephone"></i> Prefer to call? Reach us during office hours for an informal chat about your requirements.
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SECTION 12: FAQ ACCORDION -->
<!-- ============================================================ -->
<?php if (hp_enabled('faq') && !empty($faqs)): ?>
<section class="section faq-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">FAQs</span>
            <h2 class="section-title">Frequently Asked <span>Questions</span></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Find quick answers to the most common questions about our workforce management and digital solutions services. Can&rsquo;t find what you&rsquo;re looking for? <a href="/contact-us">Contact us</a> directly.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="homepageFaq">
                    <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="faqCollapse<?php echo $index; ?>">
                                <i class="bi bi-question-circle text-primary me-2"></i> <?php echo htmlspecialchars($faq['question'] ?? ''); ?>
                            </button>
                        </h2>
                        <div id="faqCollapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#homepageFaq">
                            <div class="accordion-body">
                                <?php echo $faq['answer'] ?? ''; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <p class="text-muted">Still have questions? <a href="/contact-us" class="fw-semibold">Get in touch</a> and we&rsquo;ll be happy to help.</p>
                    <p class="text-muted mt-2" style="font-size:0.85rem;">Alternatively, email us directly at <a href="mailto:admin@swamitime.com"><strong>admin@swamitime.com</strong></a> or call our office during business hours. We typically respond to enquiries within one business day.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================ -->
<!-- Scroll-triggered timeline progress -->
<!-- ============================================================ -->
<script>
(function() {
    var timelineSection = document.querySelector('.timeline');
    var progressBar = document.getElementById('timelineProgress');
    if (!timelineSection || !progressBar) return;
    var animated = false;
    function handleScroll() {
        if (animated) return;
        var rect = timelineSection.getBoundingClientRect();
        var triggerPoint = window.innerHeight * 0.6;
        if (rect.top < triggerPoint) {
            animated = true;
            progressBar.style.width = '100%';
        }
    }
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
})();
</script>
