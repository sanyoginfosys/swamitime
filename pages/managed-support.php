<?php
$slug = 'managed-support-services';
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
?>

<div class="page-header">
    <div class="container">
        <h1>Managed Support Services</h1>
        <p>Ongoing, proactive support to keep your workforce management system running smoothly — so your teams can focus on your business</p>
    </div>
</div>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/services">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">Managed Support Services</li>
            </ol>
        </nav>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <p class="lead">Workforce management systems need regular attention to perform at their best. Our managed support services provide ongoing, proactive care for your UKG or other workforce management system — giving you access to specialist expertise without the overhead of a full-time hire.</p>

                <h2>Ongoing Support</h2>
                <p>Our managed support provides a responsive, reliable point of contact for your teams when they encounter system issues or have questions about how to use the system effectively. We handle timecard discrepancies, configuration queries, user access issues, reporting problems, and general troubleshooting. Support is delivered by experienced consultants who understand both the technology and the operational context in which it is used.</p>
                <p>We offer flexible support arrangements to suit your organisation — from a set number of hours per month to retainer-based models with defined response times. Our goal is to provide the right level of support for your needs without unnecessary cost.</p>

                <h2>Configuration Updates</h2>
                <p>As your business evolves, your system configuration needs to keep pace. Whether you are introducing new pay rules, adding locations, changing holiday policies, or adjusting accrual calculations, we handle configuration updates methodically and safely. Every change is planned, tested, and documented so that you maintain a clear record of how your system is configured and why.</p>
                <p>We also manage configuration changes related to legislative updates — ensuring that your system remains compliant with working time regulations, minimum wage requirements, and other statutory obligations that affect workforce management.</p>

                <h2>Monthly System Checks</h2>
                <p>Prevention is better than cure. Our monthly system checks proactively review your system for potential issues before they become problems. We check data integrity, review exception volumes, monitor integration health, verify that scheduled jobs have run correctly, and confirm that period-end processes completed successfully. A summary report is provided each month, highlighting any observations and recommended actions. This proactive approach catches issues early and reduces the risk of payroll delays or data errors.</p>

                <h2>Issue Investigation</h2>
                <p>When something goes wrong — a payroll discrepancy, an integration failure, or unexpected system behaviour — our team investigates thoroughly to identify the root cause and implement a fix. We work methodically through the issue, examining configuration, data, and process to understand what happened and why. Our investigation reports provide clear explanations and recommended actions, helping you prevent recurrence and maintain confidence in your system.</p>

                <h2>User Support</h2>
                <p>We act as an extension of your internal support function, handling user queries that your first-line team cannot resolve. Whether it is a manager struggling with a complex schedule, an employee unable to view their timecard, or a payroll team member needing data clarification, we provide clear, practical guidance. Our user support reduces the burden on your internal teams and ensures that users get accurate, helpful responses quickly.</p>

                <h2>Reporting Assistance</h2>
                <p>Need a new report or struggling to get the data you need? Our team can build, modify, and schedule reports to meet your requirements. We also help you interpret report data, identify trends, and present workforce information in a format that supports decision-making. Whether you need a one-off data extract or a set of recurring management reports, we ensure you have the workforce intelligence you need.</p>

                <h2>Process Improvement</h2>
                <p>Managed support is not just about fixing things when they break — it is about continuous improvement. As we work with your system over time, we identify opportunities to streamline processes, reduce manual effort, and improve the user experience. We bring these suggestions to your attention with clear explanations of the benefits and effort involved, so you can prioritise improvements that deliver the greatest value.</p>
            </div>

            <div class="col-lg-4">
                <div class="highlight-box mb-4">
                    <h4 class="mb-3"><i class="bi bi-gear-wide-connected text-primary me-2"></i>What's Included</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i><strong>Responsive support</strong> for system issues and user queries</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i><strong>Configuration management</strong> for policy and rule changes</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i><strong>Monthly health checks</strong> with summary reports</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i><strong>Root cause investigation</strong> for system issues</li>
                        <li class="mb-3"><i class="bi bi-check-lg text-primary me-2"></i><strong>Report building</strong> and data extraction</li>
                        <li><i class="bi bi-check-lg text-primary me-2"></i><strong>Continuous improvement</strong> recommendations</li>
                    </ul>
                </div>

                <div class="info-box mb-4">
                    <p><i class="bi bi-info-circle-fill me-2"></i>Managed support agreements are tailored to your organisation's size, complexity, and support requirements. Contact us to discuss a package that fits your needs.</p>
                </div>

                <div class="highlight-box">
                    <h4 class="mb-3"><i class="bi bi-clock-history text-primary me-2"></i>Response Times</h4>
                    <p>We offer flexible response time commitments based on issue severity:</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i><strong>Critical:</strong> Same-day response</li>
                        <li class="mb-2"><i class="bi bi-exclamation-circle-fill text-primary me-2"></i><strong>High:</strong> Within 4 business hours</li>
                        <li class="mb-2"><i class="bi bi-info-circle-fill text-info me-2"></i><strong>Medium:</strong> Within 8 business hours</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Low:</strong> Within 2 business days</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Let Us Manage Your System Support</h2>
        <p>Focus on running your business while we keep your workforce management system in peak condition. Book a free consultation to discuss a managed support package.</p>
        <a href="/contact-us" class="btn-white btn-lg">
            <i class="bi bi-calendar-check"></i> Book a Free Consultation
        </a>
    </div>
</section>
