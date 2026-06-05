<?php
$slug = 'training-user-support';
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
        <h1>Training & User Support</h1>
        <p>Practical, role-based training that builds confidence and capability across your organisation — from managers and employees to system administrators</p>
    </div>
</div>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/services">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">Training & User Support</li>
            </ol>
        </nav>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <p class="lead">Even the best-configured workforce management system delivers limited value if your people do not know how to use it effectively. Our training services are designed to give every user — from frontline employee to system administrator — the practical skills and confidence they need to work efficiently and accurately.</p>

                <h2>Manager Training</h2>
                <p>Managers and team leaders are at the heart of workforce management. They approve timecards, manage exceptions, create schedules, and monitor attendance. Our manager training covers the full range of activities they perform daily, weekly, and monthly. Sessions are practical and hands-on, using real scenarios from your organisation so that managers can immediately apply what they learn.</p>
                <p>We cover timecard review and approval, exception handling, schedule creation and management, absence monitoring, reporting access, and how to support their team members with common queries. Each session includes a quick-reference guide that managers can keep at their desks for ongoing support.</p>

                <h2>Employee Self-Service Training</h2>
                <p>When employees can manage their own time, view their schedules, and request holidays without needing manager intervention, everyone saves time. Our employee training covers clocking in and out, viewing schedules, requesting time off, checking accrual balances, and updating availability. We focus on the practical tasks employees need to perform, presented in a straightforward, non-technical way that builds confidence and reduces reliance on managers for routine queries.</p>

                <h2>Admin Training</h2>
                <p>System administrators need deeper knowledge to maintain configurations, manage user access, troubleshoot issues, and support other users. Our admin training covers system navigation, configuration management, user role setup, data maintenance, reporting administration, and common troubleshooting scenarios. We also cover period-end processing, data validation routines, and how to manage system updates and changes safely.</p>
                <p>Admin training is typically delivered in smaller groups with plenty of time for hands-on practice and questions. We provide comprehensive documentation that serves as an ongoing reference for your admin team.</p>

                <h2>Timecard Training</h2>
                <p>Timecard management is one of the most frequent pain points in workforce management systems. We deliver focused training on timecard entry, editing, approval, and exception handling — covering both the employee and manager perspectives. Sessions include common scenarios such as missed punches, shift swaps, overtime calculations, and period-end reconciliation. Our goal is to reduce the volume of timecard-related queries and errors across your organisation.</p>

                <h2>Scheduling Training</h2>
                <p>Creating and managing schedules effectively requires a solid understanding of the system's scheduling tools. Our scheduling training covers schedule creation methods, shift pattern setup, availability management, schedule publishing, and handling schedule changes. We also cover how to use scheduling reports to monitor coverage, identify gaps, and plan future rotas. Training is tailored to your organisation's scheduling complexity and the specific tools available in your system.</p>

                <h2>Reporting Training</h2>
                <p>Managers and administrators often underutilise the reporting capabilities of their workforce management system. Our reporting training covers standard reports, custom report creation, report scheduling, and data export. We help users understand which reports are available, how to interpret the data, and how to use reports to drive better workforce decisions. Sessions include hands-on report building using your own system data.</p>

                <h2>Practical Step-by-Step Guidance</h2>
                <p>All our training is built around practical, step-by-step guidance. We do not deliver generic presentations — every session uses your system, your data, and your processes. Participants work through real tasks, ask questions in a supportive environment, and leave with documentation they can refer back to. We can deliver training on-site at your premises or remotely via video conferencing, depending on your preference and team locations.</p>
            </div>

            <div class="col-lg-4">
                <div class="highlight-box mb-4">
                    <h4 class="mb-3"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Training Delivery Options</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>On-site classroom sessions</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Remote video conference training</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>One-to-one coaching sessions</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Small group workshops</li>
                        <li><i class="bi bi-check-lg text-primary me-2"></i>Bespoke training programmes</li>
                    </ul>
                </div>

                <div class="info-box mb-4">
                    <p><i class="bi bi-info-circle-fill me-2"></i>All training sessions include supporting documentation, quick-reference guides, and follow-up support to reinforce learning. We can also record sessions for future reference.</p>
                </div>

                <div class="highlight-box">
                    <h4 class="mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Who We Train</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-person-check text-primary me-2"></i>Frontline Managers & Team Leaders</li>
                        <li class="mb-2"><i class="bi bi-person-check text-primary me-2"></i>Employees & Self-Service Users</li>
                        <li class="mb-2"><i class="bi bi-person-check text-primary me-2"></i>System Administrators</li>
                        <li class="mb-2"><i class="bi bi-person-check text-primary me-2"></i>Payroll Teams</li>
                        <li class="mb-2"><i class="bi bi-person-check text-primary me-2"></i>HR Operations Teams</li>
                        <li><i class="bi bi-person-check text-primary me-2"></i>New Starters & Onboarding Groups</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Invest in Your Team's Skills</h2>
        <p>Well-trained users make fewer errors, need less support, and get more value from your workforce management system. Book a free consultation to discuss your training needs.</p>
        <a href="/contact-us" class="btn-white btn-lg">
            <i class="bi bi-calendar-check"></i> Book a Free Consultation
        </a>
    </div>
</section>
