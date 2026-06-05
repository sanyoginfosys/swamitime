<?php
// Detect industry slug from URL
$industrySlug = '';
if (isset($_GET['slug'])) {
    $industrySlug = $_GET['slug'];
} else {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $requestUri = strtok($requestUri, '?');
    $requestUri = rtrim($requestUri, '/');
    if (preg_match('#/industries/([a-zA-Z0-9\-]+)$#', $requestUri, $matches)) {
        $industrySlug = $matches[1];
    }
}

// Try DB first
$industryData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM industries WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$industrySlug]);
    $industryData = $stmt->fetch();
} catch (Exception $e) {
    $industryData = null;
}

// Static fallback for all 7 industries
$fallbackData = [
    'retail' => [
        'title' => 'Retail Workforce Management',
        'subtitle' => 'Managing shift-based workforces across multiple locations with fluctuating customer demand, seasonal peaks, and tight labour cost control.',
        'intro' => 'Retail businesses face unique workforce management challenges — from managing staff across multiple store locations to aligning staffing levels with customer footfall patterns. Whether you operate a single store or a national chain, effective workforce management is essential to controlling costs while maintaining great customer service.',
        'challenges' => [
            'Managing rotas across multiple store locations with different opening hours and demand patterns',
            'Balancing staffing levels against unpredictable customer footfall and seasonal fluctuations',
            'Controlling overtime and premium pay costs while maintaining service levels',
            'Managing high volumes of part-time and flexible workers with varying availability',
            'Ensuring compliance with working time regulations across a large, distributed workforce',
            'Onboarding and training seasonal staff quickly and efficiently',
            'Reducing time spent on manual scheduling and timecard administration',
        ],
        'howWeHelp' => 'We help retail organisations implement and optimise workforce management systems and processes that give managers better visibility and control. From configuring scheduling rules that reflect your store operations to building reports that track labour cost as a percentage of sales, we provide practical support that delivers measurable improvements. Our experience includes working with multi-site retailers to standardise processes while accommodating local variations where needed.',
        'relevantServices' => [
            ['title' => 'UKG Workforce Management Support', 'slug' => 'ukg-workforce-management-support', 'description' => 'System configuration and support for retail time and attendance, scheduling, and reporting.'],
            ['title' => 'Workforce Management Consulting', 'slug' => 'workforce-management-consulting', 'description' => 'Process review and improvement for multi-site retail operations.'],
            ['title' => 'Reporting & Data Support', 'slug' => 'reporting-data-support', 'description' => 'Labour cost reporting, sales-per-labour-hour analysis, and store performance dashboards.'],
        ],
    ],
    'hospitality' => [
        'title' => 'Hospitality Workforce Management',
        'subtitle' => 'Balancing service levels with labour costs in hotels, restaurants, and leisure businesses where every shift matters.',
        'intro' => 'Hospitality businesses operate in a fast-paced environment where staffing levels directly impact customer experience. From hotels and restaurants to leisure venues and event spaces, managing a hospitality workforce requires flexibility, precision, and the ability to adapt quickly to changing demand.',
        'challenges' => [
            'Managing split shifts, late finishes, and early starts across front and back of house',
            'Adjusting staffing levels for seasonal demand, events, and unpredictable walk-in traffic',
            'Controlling labour costs while maintaining service quality and customer satisfaction',
            'Managing high staff turnover and onboarding new team members efficiently',
            'Tracking tips, service charges, and additional payments alongside standard hours',
            'Complying with hospitality-specific working time and rest break regulations',
            'Coordinating schedules across multiple departments — kitchen, front of house, housekeeping, and management',
        ],
        'howWeHelp' => 'We support hospitality businesses with workforce management processes and systems that balance cost control with operational flexibility. Our work covers scheduling optimisation, time and attendance accuracy, labour cost reporting, and manager training. We understand the realities of hospitality operations — including last-minute changes, variable demand, and the importance of getting the right people in the right place at the right time.',
        'relevantServices' => [
            ['title' => 'Scheduling Support', 'slug' => 'ukg-workforce-management-support', 'description' => 'Shift pattern design and scheduling optimisation for hospitality operations.'],
            ['title' => 'Training & User Support', 'slug' => 'training-user-support', 'description' => 'Practical training for hospitality managers on workforce management tools and processes.'],
            ['title' => 'Managed Support Services', 'slug' => 'managed-support-services', 'description' => 'Ongoing system support for multi-site hospitality businesses.'],
        ],
    ],
    'logistics-distribution' => [
        'title' => 'Logistics & Distribution Workforce Management',
        'subtitle' => 'Managing warehouse and transport workforces with complex shift patterns, variable demand, and precise labour tracking.',
        'intro' => 'Logistics and distribution operations depend on efficient workforce management to meet delivery commitments, control costs, and maintain safety standards. With complex shift patterns, variable workloads, and a mix of permanent and agency staff, getting workforce management right is essential to operational performance.',
        'challenges' => [
            'Managing complex rotating shift patterns across 24/7 warehouse and distribution operations',
            'Tracking labour costs against order volumes, picking rates, and delivery schedules',
            'Coordinating permanent staff and agency workers across multiple shift patterns',
            'Ensuring compliance with drivers\' hours regulations, working time rules, and health and safety requirements',
            'Managing peak periods such as Black Friday, Christmas, and seasonal promotions',
            'Integrating time and attendance data with warehouse management and logistics systems',
            'Monitoring overtime trends and managing fatigue risk across shift-based workforces',
        ],
        'howWeHelp' => 'We bring practical workforce management expertise to logistics and distribution operations. Our support covers shift pattern design, time and attendance accuracy, labour cost analysis, and integration between workforce management and operational systems. We help logistics businesses gain better visibility of labour costs, improve schedule adherence, and ensure that workforce data flows accurately into payroll.',
        'relevantServices' => [
            ['title' => 'Implementation & Configuration', 'slug' => 'implementation-configuration-support', 'description' => 'System setup and configuration for logistics workforce management environments.'],
            ['title' => 'Reporting & Data Support', 'slug' => 'reporting-data-support', 'description' => 'Labour productivity reporting, cost-per-unit analysis, and operational dashboards.'],
            ['title' => 'IT & Digital Solutions', 'slug' => 'it-digital-solutions', 'description' => 'System integration and business automation for logistics operations.'],
        ],
    ],
    'manufacturing' => [
        'title' => 'Manufacturing Workforce Management',
        'subtitle' => 'Supporting production environments with shift-based rotas, overtime management, and labour cost visibility.',
        'intro' => 'Manufacturing operations rely on efficient workforce deployment to meet production targets, control costs, and maintain quality standards. With complex shift rotations, skills-based scheduling, and tight integration with production planning, workforce management in manufacturing requires both precision and flexibility.',
        'challenges' => [
            'Managing complex shift rotations including days, nights, and continental patterns',
            'Scheduling based on skills, certifications, and machine-specific competencies',
            'Tracking labour costs against production output, units produced, and efficiency targets',
            'Managing overtime, shift premiums, and complex pay rules across different grades and roles',
            'Ensuring health and safety compliance including rest breaks and working hour limits',
            'Coordinating with production planning systems for forward-looking labour requirements',
            'Managing absence and its impact on production line staffing and output',
        ],
        'howWeHelp' => 'We support manufacturing organisations with workforce management systems and processes that align with production realities. Our work includes configuring complex pay rules and shift patterns, building production-focused labour reports, and helping managers make data-driven staffing decisions. We understand the manufacturing environment — including the need for skills-based scheduling, the importance of accurate time capture, and the cost pressures that drive every decision.',
        'relevantServices' => [
            ['title' => 'UKG Workforce Management Support', 'slug' => 'ukg-workforce-management-support', 'description' => 'Configuration and support for manufacturing time and attendance, scheduling, and pay rules.'],
            ['title' => 'Workforce Management Consulting', 'slug' => 'workforce-management-consulting', 'description' => 'Process review and operational improvement for manufacturing environments.'],
            ['title' => 'Training & User Support', 'slug' => 'training-user-support', 'description' => 'Role-based training for manufacturing managers, supervisors, and shop-floor staff.'],
        ],
    ],
    'healthcare-care-services' => [
        'title' => 'Healthcare & Care Services Workforce Management',
        'subtitle' => 'Rostering clinical and care staff across shifts, managing compliance requirements, and maintaining service continuity.',
        'intro' => 'Healthcare and care services operate in a uniquely demanding environment where staffing decisions directly affect patient and resident wellbeing. Effective workforce management in this sector must balance clinical requirements, regulatory compliance, staff wellbeing, and tight budgets — all while maintaining 24/7 service coverage.',
        'challenges' => [
            'Rostering clinical and care staff across 24/7 shift patterns with appropriate skill mix',
            'Ensuring compliance with regulatory requirements including safe staffing levels and working time regulations',
            'Managing complex pay rules including unsocial hours, bank holidays, and enhanced rates',
            'Tracking mandatory training, certifications, and professional registrations alongside scheduling',
            'Managing bank and agency staff alongside permanent employees to fill shift gaps',
            'Supporting staff wellbeing through fair scheduling and manageable workloads',
            'Maintaining accurate time records for payroll, invoicing, and regulatory reporting',
        ],
        'howWeHelp' => 'We provide workforce management support that understands the unique demands of healthcare and care services. Our work covers rostering optimisation, compliance-focused configuration, reporting for regulatory requirements, and practical training for managers and administrators. We help care providers improve scheduling fairness, reduce reliance on agency staff, and ensure that workforce data supports both operational and regulatory needs.',
        'relevantServices' => [
            ['title' => 'UKG Workforce Management Support', 'slug' => 'ukg-workforce-management-support', 'description' => 'System support for healthcare scheduling, time capture, and compliance reporting.'],
            ['title' => 'Managed Support Services', 'slug' => 'managed-support-services', 'description' => 'Ongoing system management and proactive support for care providers.'],
            ['title' => 'Reporting & Data Support', 'slug' => 'reporting-data-support', 'description' => 'Compliance reporting, staffing level analysis, and workforce dashboards.'],
        ],
    ],
    'professional-services' => [
        'title' => 'Professional Services Workforce Management',
        'subtitle' => 'Managing professional workforces with flexible working, client billing, and project-based time tracking.',
        'intro' => 'Professional services firms — including consultancies, law firms, accounting practices, and agencies — face distinct workforce management challenges. With project-based work, client billing requirements, flexible working arrangements, and a need to balance utilisation with employee satisfaction, effective time and resource management is critical to profitability.',
        'challenges' => [
            'Accurate time tracking for client billing, project costing, and utilisation analysis',
            'Managing flexible and hybrid working arrangements while maintaining team collaboration',
            'Balancing employee utilisation targets with wellbeing and sustainable workloads',
            'Tracking absence, holiday, and leave across a professional workforce with varied working patterns',
            'Integrating time data with project management, finance, and billing systems',
            'Providing visibility of resource availability for project planning and pipeline management',
            'Supporting professional development and training alongside billable client work',
        ],
        'howWeHelp' => 'We help professional services firms implement workforce management processes and systems that support accurate time tracking, resource visibility, and efficient operations. Our work covers time capture configuration, utilisation reporting, absence management, and integration with project management and finance systems. We understand the professional services context — including the importance of billable time accuracy and the need to support flexible working patterns.',
        'relevantServices' => [
            ['title' => 'Workforce Management Consulting', 'slug' => 'workforce-management-consulting', 'description' => 'Process review and improvement for professional services time and resource management.'],
            ['title' => 'IT & Digital Solutions', 'slug' => 'it-digital-solutions', 'description' => 'System integration, automation, and digital workflow improvement for professional firms.'],
            ['title' => 'Web Development', 'slug' => 'web-development', 'description' => 'Client portals, professional websites, and digital platforms for service firms.'],
        ],
    ],
    'small-medium-businesses' => [
        'title' => 'Small & Medium Business Workforce Management',
        'subtitle' => 'Scalable workforce management solutions for growing businesses that need professional processes without enterprise complexity.',
        'intro' => 'Small and medium businesses often manage their workforce with spreadsheets, paper timesheets, or basic HR systems that cannot keep up as the business grows. We help SMEs implement practical, proportionate workforce management solutions that professionalise their operations without unnecessary cost or complexity.',
        'challenges' => [
            'Moving from manual timesheets and spreadsheets to structured workforce management',
            'Implementing consistent absence management and holiday booking processes',
            'Gaining visibility of labour costs as the workforce grows and becomes more complex',
            'Managing scheduling for teams that may include full-time, part-time, and flexible workers',
            'Ensuring payroll receives accurate time data without manual reconciliation',
            'Complying with employment regulations, working time rules, and record-keeping requirements',
            'Selecting and implementing the right workforce management system for current and future needs',
        ],
        'howWeHelp' => 'We work with SMEs to implement workforce management solutions that are right-sized for their business. This might mean helping you get more from an existing system, supporting a move to a new platform, or improving processes around your current tools. Our approach is practical, cost-conscious, and focused on delivering real value — not selling you more than you need. We also provide training and ongoing support to ensure your team can manage effectively as the business grows.',
        'relevantServices' => [
            ['title' => 'Workforce Management Consulting', 'slug' => 'workforce-management-consulting', 'description' => 'Practical advice on workforce processes, system selection, and implementation planning.'],
            ['title' => 'Training & User Support', 'slug' => 'training-user-support', 'description' => 'Hands-on training for managers and employees on workforce management tools and processes.'],
            ['title' => 'IT & Digital Solutions', 'slug' => 'it-digital-solutions', 'description' => 'Technology guidance, automation, and digital tools to support SME growth.'],
        ],
    ],
];

$industry = $industryData ?: ($fallbackData[$industrySlug] ?? null);

if (!$industry) {
    http_response_code(404);
    echo '<div class="page-header"><div class="container"><h1>Industry Not Found</h1><p>The industry page you are looking for does not exist.</p></div></div>';
    echo '<div class="content-section"><div class="container"><p><a href="/industries" class="btn-teal">View All Industries</a></p></div></div>';
    return;
}
?>

<div class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($industry['title']); ?></h1>
        <p><?php echo htmlspecialchars($industry['subtitle']); ?></p>
    </div>
</div>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/industries">Industries</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($industry['title']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <p class="lead"><?php echo htmlspecialchars($industry['intro']); ?></p>

                <h2>Common Workforce Challenges</h2>
                <ul>
                    <?php foreach ($industry['challenges'] as $challenge): ?>
                    <li><?php echo htmlspecialchars($challenge); ?></li>
                    <?php endforeach; ?>
                </ul>

                <h2>How SWAMITIME Helps</h2>
                <p><?php echo htmlspecialchars($industry['howWeHelp']); ?></p>
            </div>

            <div class="col-lg-4">
                <div class="highlight-box mb-4">
                    <h4 class="mb-3"><i class="bi bi-gear-fill text-primary me-2"></i>Relevant Services</h4>
                    <?php foreach ($industry['relevantServices'] as $service): ?>
                    <div class="mb-3">
                        <a href="/<?php echo htmlspecialchars($service['slug']); ?>" class="fw-bold text-primary text-decoration-none">
                            <?php echo htmlspecialchars($service['title']); ?> <i class="bi bi-arrow-right ms-1 small"></i>
                        </a>
                        <p class="small text-muted mb-0 mt-1"><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="info-box">
                    <p><i class="bi bi-info-circle-fill me-2"></i>Every organisation is different. Contact us to discuss your specific industry challenges and how we can help.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Discuss Your <?php echo htmlspecialchars(explode(' ', $industry['title'])[0]); ?> Workforce Requirements</h2>
        <p>Book a free consultation to talk through your challenges and explore how our expertise can support your organisation.</p>
        <a href="/contact-us" class="btn-white btn-lg">
            <i class="bi bi-calendar-check"></i> Book a Free Consultation
        </a>
    </div>
</section>
