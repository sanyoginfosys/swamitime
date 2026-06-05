<?php
$slug = $_GET['slug'] ?? '';
if (empty($slug)) { http_response_code(404); include __DIR__ . '/404.php'; return; }

$post = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id WHERE p.slug = ? AND p.status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
} catch (Exception $e) {
    $post = null;
}

if (empty($post)) {
    $fallbackPosts = [
        'workforce-management-systems-reduce-manual-admin' => [
            'title' => 'How Workforce Management Systems Help Businesses Reduce Manual Admin',
            'category_name' => 'Workforce Management',
            'category_slug' => 'workforce-management',
            'author_name' => 'SwamiTime Editorial Team',
            'published_at' => '2026-05-15',
            'read_time' => '6 min read',
            'excerpt' => 'Discover how modern workforce management platforms automate time-consuming administrative tasks, freeing your HR and operations teams to focus on strategic priorities.',
            'content' => '
<h2>Introduction</h2>
<p>For many organisations, the administrative burden of workforce management remains surprisingly manual. Spreadsheets, paper timesheets, email chains for leave approvals, and disconnected HR systems still dominate daily operations in businesses of all sizes. This administrative overhead does not just consume time &mdash; it introduces errors, creates compliance risks, and prevents HR and operations teams from focusing on strategic work that genuinely adds value.</p>
<p>Modern workforce management (WFM) systems offer a compelling alternative. By automating routine processes, centralising data, and providing self-service capabilities, these platforms can dramatically reduce the manual effort involved in managing a workforce. This article explores how WFM systems achieve this and what organisations should consider when evaluating these tools.</p>

<h2>The Hidden Cost of Manual Workforce Admin</h2>
<p>Before examining the solutions, it is worth quantifying the problem. Research consistently shows that HR teams and line managers spend a significant portion of their working week on administrative tasks related to workforce management. These include:</p>
<ul>
    <li><strong>Timesheet collection and verification:</strong> Chasing missing timesheets, checking for errors, and reconciling discrepancies</li>
    <li><strong>Leave and absence management:</strong> Processing holiday requests, tracking sick leave, and managing return-to-work procedures</li>
    <li><strong>Shift scheduling:</strong> Creating rotas, finding cover for absences, and managing shift swaps</li>
    <li><strong>Payroll preparation:</strong> Collating hours, overtime, and absence data for payroll processing</li>
    <li><strong>Compliance reporting:</strong> Compiling data for regulatory requirements such as Working Time Regulations</li>
</ul>
<p>The cumulative effect is substantial. For a mid-sized organisation of 500 employees, the time spent on these activities can easily exceed 40 hours per week across the HR and management teams. Beyond the direct time cost, manual processes inevitably result in errors &mdash; incorrect pay, missed compliance deadlines, and employee dissatisfaction.</p>

<h2>How WFM Systems Automate the Administrative Load</h2>
<p>A well-implemented workforce management system transforms each of these administrative touchpoints:</p>

<h3>1. Automated Time Capture</h3>
<p>Modern WFM platforms replace paper timesheets and spreadsheets with digital time capture &mdash; whether through web-based clocking, mobile apps, biometric terminals, or integration with access control systems. Time data flows directly into the system, eliminating manual data entry entirely. Configurable approval workflows ensure that exceptions are flagged automatically for manager review, rather than requiring managers to scrutinise every entry.</p>

<h3>2. Self-Service Leave Management</h3>
<p>Employee self-service portals allow staff to request leave, view their balances, and check team availability without involving HR or their line manager for routine enquiries. Managers receive automated notifications for approval, and the system updates leave balances, schedules, and payroll feeds in real time. The result is a process that takes seconds rather than hours.</p>

<h3>3. Intelligent Scheduling</h3>
<p>WFM scheduling modules use configurable rules &mdash; covering skills, availability, working time regulations, and business demand &mdash; to generate optimised schedules automatically. For organisations with complex shift patterns, this can reduce scheduling time by up to 80%. Employees can also view schedules, request swaps, and indicate availability through self-service, further reducing the administrative load on managers.</p>

<h3>4. Seamless Payroll Integration</h3>
<p>When time, attendance, and absence data are captured digitally in a single system, the feed into payroll becomes automated. This eliminates the need for manual reconciliation, reduces payroll errors, and ensures employees are paid correctly and on time. Many WFM platforms offer pre-built integrations with leading payroll systems, making this connection straightforward.</p>

<h3>5. Automated Compliance and Reporting</h3>
<p>WFM systems maintain a complete audit trail of all workforce data, making compliance reporting significantly easier. Configurable alerts can notify managers of potential breaches before they occur &mdash; for example, an employee approaching the 48-hour weekly working limit under the Working Time Regulations. Standard and custom reports can be scheduled for automatic distribution to stakeholders.</p>

<h2>What to Look for in a WFM System</h2>
<p>Not all WFM systems are created equal. When evaluating options, consider the following factors:</p>
<ul>
    <li><strong>Configurability:</strong> Can the system adapt to your specific policies, rules, and workflows, or does it force you to change your processes to fit the software?</li>
    <li><strong>Integration capability:</strong> Does it integrate with your existing HR, payroll, and ERP systems? Pre-built connectors reduce implementation time and risk.</li>
    <li><strong>User experience:</strong> Is the interface intuitive for both managers and employees? Poor usability leads to low adoption and undermines the productivity gains you are seeking.</li>
    <li><strong>Scalability:</strong> Can the system grow with your organisation across locations, employee numbers, and complexity?</li>
    <li><strong>Vendor independence:</strong> Are you tied to a single vendor ecosystem, or can you integrate best-of-breed solutions?</li>
</ul>

<h2>Conclusion</h2>
<p>Workforce management systems represent one of the most impactful technology investments an organisation can make in its HR and operations functions. By automating routine administrative tasks, they free up valuable time, reduce errors, improve compliance, and enable data-driven decision-making. The key to realising these benefits lies in selecting the right system for your specific context and implementing it with a clear focus on user adoption and process alignment.</p>
<p>At SWAMITIME SOLUTIONS LTD, we help organisations navigate this journey &mdash; from initial requirements gathering and vendor selection through to implementation, configuration, and ongoing support. If you are considering a WFM system or looking to get more from your existing investment, we would welcome a conversation.</p>
'
        ],
        'what-to-check-before-implementing-time-attendance' => [
            'title' => 'What to Check Before Implementing a Time and Attendance System',
            'category_name' => 'Time & Attendance',
            'category_slug' => 'time-attendance',
            'author_name' => 'SwamiTime Editorial Team',
            'published_at' => '2026-05-01',
            'read_time' => '5 min read',
            'excerpt' => 'A practical checklist for organisations evaluating time and attendance solutions, covering integration requirements, compliance considerations, and employee adoption.',
            'content' => '
<h2>Introduction</h2>
<p>Implementing a time and attendance system is a significant undertaking that affects every employee in your organisation. Done well, it streamlines operations, ensures accurate payroll, and strengthens compliance. Done poorly, it creates friction, undermines trust, and fails to deliver the expected return on investment. This article provides a practical checklist of factors to consider before you begin implementation.</p>

<h2>1. Define Your Requirements Clearly</h2>
<p>Begin by documenting exactly what you need the system to do. Engage stakeholders from HR, operations, payroll, IT, and line management to build a comprehensive picture. Key questions to address include:</p>
<ul>
    <li>What types of attendance patterns do you need to track? (standard hours, flexible working, shift work, overtime, TOIL)</li>
    <li>What are your current pain points with the existing process?</li>
    <li>Which compliance regulations apply to your organisation? (Working Time Regulations, National Minimum Wage, industry-specific requirements)</li>
    <li>How many locations and employees will the system need to cover?</li>
    <li>What existing systems does it need to integrate with? (HR, payroll, access control, ERP)</li>
</ul>

<h2>2. Evaluate Integration Requirements</h2>
<p>Integration is often the most underestimated aspect of a time and attendance implementation. The system needs to exchange data reliably with your payroll platform to ensure accurate payment. It may also need to connect with HR systems for employee records, access control for clocking data, and scheduling systems for roster comparison. Before selecting a system, map out all integration points and confirm that the necessary APIs or pre-built connectors exist. Speak to your IT team about data flow, security, and ongoing maintenance requirements.</p>

<h2>3. Consider Employee Adoption</h2>
<p>The most feature-rich time and attendance system is worthless if employees do not use it correctly &mdash; or at all. Adoption hinges on several factors:</p>
<ul>
    <li><strong>Ease of use:</strong> The clocking process, whether via terminal, mobile app, or web browser, must be intuitive and fast</li>
    <li><strong>Perceived fairness:</strong> Employees need to understand why the system is being introduced and how it benefits them (accurate pay, fair overtime allocation, easier leave management)</li>
    <li><strong>Communication:</strong> A clear communication plan explaining the what, why, and how of the new system is essential before, during, and after implementation</li>
    <li><strong>Training:</strong> Role-appropriate training ensures employees, managers, and administrators are confident using the system from day one</li>
</ul>

<h2>4. Verify Compliance Capabilities</h2>
<p>For UK-based organisations, the Working Time Regulations 1998 impose specific requirements around working hours, rest breaks, and night work. Your time and attendance system should be able to:</p>
<ul>
    <li>Track working hours against the 48-hour weekly average (including opt-out tracking where applicable)</li>
    <li>Monitor rest break compliance</li>
    <li>Flag potential breaches proactively through alerts and notifications</li>
    <li>Maintain auditable records for the required retention period</li>
    <li>Support National Minimum Wage compliance through accurate hours tracking</li>
</ul>

<h2>5. Plan Your Data Migration</h2>
<p>If you are transitioning from an existing system or from manual records, data migration requires careful planning. Decide what historical data needs to be migrated, how it will be cleansed and formatted, and how you will validate accuracy post-migration. Running parallel systems for a short period can help identify discrepancies before the new system becomes the single source of truth.</p>

<h2>6. Test Thoroughly Before Go-Live</h2>
<p>Rigorous testing is non-negotiable. Your test plan should cover:</p>
<ul>
    <li>Functional testing of all configured rules and workflows</li>
    <li>Integration testing with payroll and other connected systems</li>
    <li>User acceptance testing with a representative group of employees and managers</li>
    <li>Performance testing under peak load (e.g., all employees clocking in simultaneously)</li>
    <li>Exception handling &mdash; what happens when things go wrong?</li>
</ul>

<h2>Conclusion</h2>
<p>A well-planned time and attendance implementation can deliver significant operational benefits. By addressing these six areas upfront &mdash; requirements, integrations, adoption, compliance, data migration, and testing &mdash; you significantly increase the likelihood of a successful deployment. At SWAMITIME SOLUTIONS LTD, we have guided organisations of all sizes through this process and would be happy to discuss your specific requirements.</p>
'
        ],
        'better-scheduling-improves-workforce-productivity' => [
            'title' => 'How Better Scheduling Improves Workforce Productivity',
            'category_name' => 'Scheduling',
            'category_slug' => 'scheduling',
            'author_name' => 'SwamiTime Editorial Team',
            'published_at' => '2026-04-20',
            'read_time' => '7 min read',
            'excerpt' => 'Explore the direct link between effective scheduling practices and measurable improvements in productivity, employee engagement, and operational efficiency.',
            'content' => '
<h2>Introduction</h2>
<p>Scheduling is often treated as a purely operational task &mdash; making sure enough people are in the right place at the right time. But research and practical experience show that scheduling has a profound impact on workforce productivity, employee engagement, and ultimately, business performance. This article examines the connection and outlines practical steps organisations can take to improve their scheduling practices.</p>

<h2>The Productivity-Scheduling Link</h2>
<p>Productivity in a workforce context is not simply about having people present. It is about having the <em>right</em> people, with the <em>right</em> skills, available at the <em>right</em> time, and engaged in the <em>right</em> work. Effective scheduling achieves all four of these conditions simultaneously.</p>
<p>When scheduling is done well, several productivity drivers come into play:</p>
<ul>
    <li><strong>Reduced downtime:</strong> Overstaffing wastes labour costs; understaffing creates bottlenecks and service failures. Optimal scheduling matches capacity to demand.</li>
    <li><strong>Skills alignment:</strong> Assigning the right skilled employees to the right shifts improves output quality and reduces errors</li>
    <li><strong>Employee engagement:</strong> Fair, transparent scheduling that considers employee preferences improves morale and reduces absenteeism</li>
    <li><strong>Reduced manager time:</strong> Automated, rules-based scheduling frees managers from hours of manual roster creation, allowing them to focus on leading their teams</li>
</ul>

<h2>Common Scheduling Challenges</h2>
<p>Despite the clear benefits, many organisations struggle with scheduling. Common challenges include:</p>
<ul>
    <li><strong>Demand volatility:</strong> Fluctuating customer demand makes it difficult to predict staffing requirements accurately</li>
    <li><strong>Complex constraints:</strong> Working time regulations, union agreements, employee contracts, and skills requirements create a web of constraints that are difficult to manage manually</li>
    <li><strong>Employee preferences:</strong> Balancing business needs with employee preferences for specific shifts, days off, or working patterns</li>
    <li><strong>Last-minute changes:</strong> Absences, shift swaps, and unexpected demand spikes require rapid rescheduling</li>
    <li><strong>Perceived unfairness:</strong> Even unintentional patterns of unfavourable shift allocation can breed resentment and disengagement</li>
</ul>

<h2>How Technology Enables Better Scheduling</h2>
<p>Modern workforce management platforms address these challenges through a combination of automation, optimisation, and self-service capabilities:</p>

<h3>Demand Forecasting</h3>
<p>Advanced scheduling systems use historical data and configurable algorithms to predict staffing requirements for any given period. By analysing past patterns &mdash; footfall, transaction volumes, patient admissions, order volumes, or any other relevant metric &mdash; the system can generate accurate staffing forecasts that form the basis of the schedule.</p>

<h3>Rules-Based Schedule Generation</h3>
<p>Once demand is forecast, the system generates optimised schedules that satisfy all defined constraints: working time regulations, contract terms, skills requirements, union agreements, and budget limits. The schedule can be generated in minutes rather than the hours or days required for manual roster creation.</p>

<h3>Employee Self-Service</h3>
<p>Self-service portals allow employees to view their schedules, indicate availability preferences, request shift swaps, and bid for open shifts. This not only reduces the administrative burden on managers but also gives employees a sense of control and ownership over their working patterns &mdash; a key driver of engagement.</p>

<h3>Real-Time Adjustments</h3>
<p>When absences occur or demand changes unexpectedly, scheduling systems enable managers to identify available and qualified replacements quickly, make adjustments, and communicate changes to affected employees automatically. This agility prevents the productivity losses associated with understaffing.</p>

<h2>Measuring the Impact</h2>
<p>Organisations that invest in better scheduling typically see measurable improvements across several dimensions:</p>
<ul>
    <li><strong>Labour cost reduction</strong> of 2&ndash;5% through reduced overtime and improved capacity utilisation</li>
    <li><strong>Manager time savings</strong> of 50&ndash;80% in schedule creation and management</li>
    <li><strong>Employee satisfaction improvements</strong> driven by fairer, more transparent scheduling</li>
    <li><strong>Absenteeism reduction</strong> as employees feel more engaged with their working patterns</li>
    <li><strong>Compliance confidence</strong> through automated rules enforcement and audit trails</li>
</ul>

<h2>Conclusion</h2>
<p>Scheduling is far more than an administrative necessity. It is a strategic lever that directly influences workforce productivity, employee engagement, and operational performance. By combining clear policies, the right technology, and a commitment to fairness and transparency, organisations can transform scheduling from a source of frustration into a competitive advantage.</p>
<p>If you are looking to improve your organisation&rsquo;s scheduling practices, SWAMITIME SOLUTIONS LTD offers independent advice, system configuration, and implementation support. Contact us to discuss your requirements.</p>
'
        ],
        'why-reporting-matters-in-workforce-management' => [
            'title' => 'Why Reporting Matters in Workforce Management',
            'category_name' => 'HR Technology',
            'category_slug' => 'hr-technology',
            'author_name' => 'SwamiTime Editorial Team',
            'published_at' => '2026-04-10',
            'read_time' => '5 min read',
            'excerpt' => 'Understand how data-driven reporting transforms workforce decision-making, from compliance tracking to strategic workforce planning.',
            'content' => '
<h2>Introduction</h2>
<p>Workforce data is one of the most valuable assets an organisation possesses, yet it is frequently underutilised. Many businesses collect vast amounts of data through their time and attendance systems, HR platforms, and scheduling tools, but lack the reporting capability to transform that data into actionable insight. This article explores why reporting matters and how organisations can build a reporting framework that supports better decision-making.</p>

<h2>The Value of Workforce Reporting</h2>
<p>Effective workforce reporting serves multiple purposes across the organisation:</p>

<h3>Operational Visibility</h3>
<p>Real-time dashboards showing attendance, absence, overtime, and staffing levels give operational managers the visibility they need to make informed decisions. Rather than relying on gut feel or outdated spreadsheets, managers can see exactly what is happening across their teams and take corrective action where needed.</p>

<h3>Compliance Assurance</h3>
<p>Regulatory requirements such as the Working Time Regulations require organisations to monitor and record working hours, rest breaks, and night work. Automated reporting ensures that compliance data is readily available for internal audits and external inspections, reducing the stress and resource drain associated with manual compilation.</p>

<h3>Strategic Workforce Planning</h3>
<p>Historical workforce data reveals patterns that inform future planning. Analysis of absence trends, turnover rates, overtime patterns, and skills gaps enables HR and finance teams to model future workforce requirements with greater accuracy, supporting budgeting, recruitment, and training decisions.</p>

<h3>Cost Management</h3>
<p>Labour is typically one of the largest cost items for any organisation. Detailed reporting on labour costs by department, location, shift type, and overtime category enables finance teams to identify cost drivers and opportunities for optimisation.</p>

<h2>Building an Effective Reporting Framework</h2>
<p>Creating meaningful workforce reports requires more than just extracting data from a system. Consider the following principles:</p>

<h3>1. Define Clear Objectives</h3>
<p>Start by identifying the specific decisions that reports will support. A report designed for a line manager monitoring daily attendance is very different from one designed for a CFO analysing quarterly labour cost trends. Define the audience, purpose, and frequency for each report.</p>

<h3>2. Ensure Data Quality</h3>
<p>Reporting is only as good as the data that feeds it. Invest time in validating that your workforce management system is capturing accurate, complete, and timely data. Address data quality issues at source rather than attempting to fix them in the reporting layer.</p>

<h3>3. Choose the Right Tools</h3>
<p>Many workforce management platforms include built-in reporting capabilities. For more advanced requirements, dedicated business intelligence tools such as Power BI or Tableau can connect to workforce data sources and provide richer visualisation and analysis options.</p>

<h3>4. Automate Distribution</h3>
<p>Scheduled report distribution ensures that stakeholders receive the information they need without having to remember to log in and run reports. Automated delivery via email or shared dashboards embeds workforce data into the regular rhythm of the business.</p>

<h3>5. Review and Refine</h3>
<p>Reporting requirements evolve as the business changes. Regularly review your reporting suite with stakeholders to ensure it remains relevant and useful. Retire reports that are no longer needed and develop new ones that address emerging requirements.</p>

<h2>Conclusion</h2>
<p>Workforce reporting is not a &ldquo;nice to have&rdquo; &mdash; it is a fundamental capability that supports operational management, regulatory compliance, strategic planning, and cost control. Organisations that invest in building robust reporting frameworks gain a significant advantage in their ability to manage their workforce effectively and make informed decisions about their people.</p>
<p>SWAMITIME SOLUTIONS LTD helps organisations design and implement workforce reporting solutions that deliver practical, actionable insight. Contact us to learn more.</p>
'
        ],
        'hr-technology-supports-smarter-business-operations' => [
            'title' => 'How HR Technology Supports Smarter Business Operations',
            'category_name' => 'HR Technology',
            'category_slug' => 'hr-technology',
            'author_name' => 'SwamiTime Editorial Team',
            'published_at' => '2026-03-25',
            'read_time' => '6 min read',
            'excerpt' => 'An overview of how integrated HR technology platforms are reshaping business operations, enabling data-driven people management and operational excellence.',
            'content' => '
<h2>Introduction</h2>
<p>The role of HR technology has expanded significantly over the past decade. What began as systems for storing employee records and processing payroll has evolved into integrated platforms that touch nearly every aspect of people management and operational delivery. This article examines how modern HR technology supports smarter business operations and the benefits organisations can realise through strategic adoption.</p>

<h2>The Evolving HR Technology Landscape</h2>
<p>Today&rsquo;s HR technology ecosystem encompasses a broad range of capabilities, including:</p>
<ul>
    <li><strong>Core HR and payroll:</strong> Employee records, compensation, benefits administration, and payroll processing</li>
    <li><strong>Workforce management:</strong> Time and attendance, scheduling, absence management, and labour analytics</li>
    <li><strong>Talent management:</strong> Recruitment, onboarding, performance management, learning and development, and succession planning</li>
    <li><strong>Employee experience:</strong> Self-service portals, engagement surveys, recognition platforms, and communication tools</li>
    <li><strong>Analytics and reporting:</strong> Dashboards, predictive analytics, and workforce planning tools</li>
</ul>
<p>The most significant trend in recent years has been the move towards integrated platforms that bring these capabilities together, reducing data silos and providing a single source of truth for workforce data.</p>

<h2>How HR Technology Drives Smarter Operations</h2>

<h3>1. Process Automation</h3>
<p>Routine HR processes &mdash; from onboarding checklists and leave approvals to timesheet collection and payroll reconciliation &mdash; can be automated, eliminating manual effort, reducing errors, and accelerating cycle times. The time saved allows HR professionals to focus on higher-value activities such as employee development, culture building, and strategic workforce planning.</p>

<h3>2. Data-Driven Decision-Making</h3>
<p>Integrated HR platforms provide leaders with real-time visibility of workforce metrics. Rather than relying on periodic reports that are outdated by the time they are produced, managers can access live dashboards showing attendance, absence trends, labour costs, overtime, and compliance status. This enables faster, more informed decision-making at every level of the organisation.</p>

<h3>3. Improved Employee Experience</h3>
<p>Self-service capabilities give employees control over routine tasks such as viewing payslips, requesting leave, updating personal information, and accessing training materials. A seamless digital experience contributes to higher engagement and reduces the administrative burden on HR teams. In an increasingly competitive talent market, the quality of the employee technology experience is a differentiator.</p>

<h3>4. Compliance and Risk Management</h3>
<p>HR technology platforms maintain comprehensive audit trails and can be configured to enforce compliance with regulations such as the Working Time Regulations, GDPR, and National Minimum Wage requirements. Automated alerts flag potential issues before they become problems, and reporting capabilities ensure that evidence of compliance is always available.</p>

<h3>5. Scalability</h3>
<p>As organisations grow, manual HR processes become increasingly unsustainable. Technology platforms scale with the business, accommodating new locations, additional employees, and more complex organisational structures without a proportional increase in HR headcount.</p>

<h2>Key Considerations for Adoption</h2>
<p>While the benefits are clear, successful adoption of HR technology requires careful planning:</p>
<ul>
    <li><strong>Start with process, not technology:</strong> Map out your current processes and identify improvement opportunities before evaluating technology solutions. Technology should enable better processes, not automate inefficient ones.</li>
    <li><strong>Involve stakeholders early:</strong> Engage representatives from HR, IT, operations, finance, and end users in the selection and implementation process. Their input is essential for requirements definition and adoption.</li>
    <li><strong>Prioritise integrations:</strong> The value of HR technology is multiplied when systems work together. Prioritise platforms with strong integration capabilities and well-documented APIs.</li>
    <li><strong>Invest in change management:</strong> Technology adoption is fundamentally a people challenge. Invest in communication, training, and support to ensure users embrace the new tools.</li>
</ul>

<h2>Conclusion</h2>
<p>HR technology has moved from the back office to the strategic centre of business operations. Organisations that embrace integrated, data-driven HR platforms gain significant advantages in efficiency, decision-making, employee experience, and compliance. The key to realising these benefits lies in thoughtful selection, careful implementation, and sustained investment in user adoption.</p>
<p>SWAMITIME SOLUTIONS LTD provides independent advice and implementation support for organisations navigating the HR technology landscape. Contact us to discuss how we can help your business operate smarter.</p>
'
        ]
    ];

    if (isset($fallbackPosts[$slug])) {
        $post = $fallbackPosts[$slug];
    } else {
        http_response_code(404);
        include __DIR__ . '/404.php';
        return;
    }
}

$title = $post['title'] ?? 'Blog Post';

$relatedPosts = [];
try {
    $db = getDB();
    if (!empty($post['category_id'])) {
        $relatedStmt = $db->prepare("SELECT title, slug FROM blog_posts WHERE category_id = ? AND slug != ? AND status = 'published' ORDER BY published_at DESC LIMIT 3");
        $relatedStmt->execute([$post['category_id'], $slug]);
        $relatedPosts = $relatedStmt->fetchAll();
    }
} catch (Exception $e) {
    $relatedPosts = [];
}
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <span class="d-inline-block px-3 py-1 rounded-pill mb-3" style="background: rgba(255,255,255,0.15); font-size: 0.85rem; font-weight: 600;">
            <?php echo htmlspecialchars($post['category_name'] ?? 'General', ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <h1><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p style="opacity: 0.85; font-size: 0.95rem;">
            <i class="bi bi-person-fill me-1"></i> <?php echo htmlspecialchars($post['author_name'] ?? 'SwamiTime Team', ENT_QUOTES, 'UTF-8'); ?>
            &nbsp;&bull;&nbsp;
            <i class="bi bi-calendar3 me-1"></i> <?php echo htmlspecialchars(format_date($post['published_at'] ?? '', 'd F Y'), ENT_QUOTES, 'UTF-8'); ?>
            <?php if (!empty($post['read_time'])): ?>
            &nbsp;&bull;&nbsp;
            <i class="bi bi-clock me-1"></i> <?php echo htmlspecialchars($post['read_time'], ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
        </p>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/blog">Blog</a></li>
                <?php if (!empty($post['category_name'])): ?>
                <li class="breadcrumb-item"><a href="/blog?category=<?php echo htmlspecialchars($post['category_slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($post['category_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars(truncate($post['title'], 60), ENT_QUOTES, 'UTF-8'); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Featured Image Placeholder -->
<section>
    <div class="container">
        <div style="background: var(--gradient); height: 360px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; margin-top: 2rem;">
            <div style="position: absolute; inset: 0; background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.06) 1px, transparent 0); background-size: 28px 28px;"></div>
            <div style="position: relative; z-index: 1; text-align: center; color: rgba(255,255,255,0.5);">
                <i class="bi bi-image" style="font-size: 4rem; display: block; margin-bottom: 0.5rem;"></i>
                <span style="font-family: var(--font-heading); font-weight: 600; font-size: 1rem;"><?php echo htmlspecialchars($post['category_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <article class="blog-content" style="line-height: 1.8; color: var(--dark-text); font-size: 1.05rem;">
                    <?php if (isset($post['content'])): ?>
                        <?php echo $post['content']; ?>
                    <?php else: ?>
                        <p>Content not available.</p>
                    <?php endif; ?>
                </article>

                <style>
                .blog-content h2 { margin-top: 2.5rem; margin-bottom: 1rem; font-size: 1.6rem; }
                .blog-content h3 { margin-top: 1.75rem; margin-bottom: 0.75rem; font-size: 1.25rem; }
                .blog-content p { margin-bottom: 1.25rem; color: var(--muted-text); }
                .blog-content ul, .blog-content ol { margin-bottom: 1.25rem; color: var(--muted-text); }
                .blog-content ul li, .blog-content ol li { margin-bottom: 0.5rem; }
                .blog-content strong { color: var(--dark-text); }
                </style>

                <!-- Share Buttons -->
                <div class="mt-5 pt-4 border-top">
                    <h6 class="mb-3">Share this article:</h6>
                    <?php
                    $shareUrl = get_current_url();
                    $shareTitle = urlencode($post['title'] ?? '');
                    ?>
                    <div class="d-flex gap-2">
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($shareUrl); ?>&title=<?php echo $shareTitle; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm rounded-pill d-inline-flex align-items-center gap-2" style="background: #0A66C2; color: #fff; padding: 8px 16px;">
                            <i class="bi bi-linkedin"></i> LinkedIn
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($shareUrl); ?>&text=<?php echo $shareTitle; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm rounded-pill d-inline-flex align-items-center gap-2" style="background: #000; color: #fff; padding: 8px 16px;">
                            <i class="bi bi-twitter-x"></i> Twitter / X
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($shareUrl); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm rounded-pill d-inline-flex align-items-center gap-2" style="background: #1877F2; color: #fff; padding: 8px 16px;">
                            <i class="bi bi-facebook"></i> Facebook
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Table of Contents -->
                <div class="card mb-4" style="border: 1px solid var(--soft-grey); border-radius: var(--radius-lg);">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Table of Contents</h5>
                        <div id="toc" style="font-size: 0.9rem; color: var(--muted-text);">
                            <p class="small text-muted">Contents will load with the article.</p>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                <div class="card mb-4" style="border: 1px solid var(--soft-grey); border-radius: var(--radius-lg);">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Related Articles</h5>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($relatedPosts as $related): ?>
                            <li class="mb-2">
                                <a href="/blog/<?php echo htmlspecialchars($related['slug'], ENT_QUOTES, 'UTF-8'); ?>" style="font-size: 0.9rem;">
                                    <i class="bi bi-arrow-right-short"></i> <?php echo htmlspecialchars($related['title'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <!-- CTA Sidebar -->
                <div class="card" style="background: var(--gradient); border: none; border-radius: var(--radius-lg); color: #fff;">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-chat-dots-fill" style="font-size: 2rem; display: block; margin-bottom: 0.75rem; opacity: 0.9;"></i>
                        <h5 class="card-title" style="color: #fff;">Need Expert Advice?</h5>
                        <p style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">Speak with our team about your workforce management challenges.</p>
                        <a href="/contact-us" class="btn btn-white mt-2" style="background: #fff; color: var(--primary-dark); border-radius: var(--radius-full); font-weight: 600; padding: 10px 24px; text-decoration: none; display: inline-block;">
                            Get in Touch <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toc = document.getElementById('toc');
    if (!toc) return;
    var headings = document.querySelectorAll('.blog-content h2, .blog-content h3');
    if (headings.length === 0) { toc.innerHTML = '<p class="small text-muted">No sections available.</p>'; return; }
    var html = '<ul class="list-unstyled mb-0" style="padding-left: 0;">';
    headings.forEach(function (h, i) {
        var id = 'section-' + i;
        h.id = id;
        var indent = h.tagName === 'H3' ? 'padding-left: 1rem;' : '';
        html += '<li style="' + indent + 'margin-bottom: 4px;"><a href="#' + id + '" style="font-size: 0.85rem; color: var(--muted-text); text-decoration: none;">' + h.textContent + '</a></li>';
    });
    html += '</ul>';
    toc.innerHTML = html;
});
</script>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Stay Informed</h2>
        <p>Subscribe to receive the latest insights, best practices, and industry updates from our team of workforce management experts.</p>
        <a href="/contact-us" class="btn-white">Subscribe for Updates <i class="bi bi-bell-fill"></i></a>
        <div class="cta-trust">No spam &bull; Unsubscribe anytime &bull; Expert insights</div>
    </div>
</section>
