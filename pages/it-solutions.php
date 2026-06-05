<?php
$slug = 'it-digital-solutions';
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
        <h1>IT & Digital Solutions</h1>
        <p>Practical technology guidance to modernise your operations, improve efficiency, and support business growth</p>
    </div>
</div>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/services">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">IT & Digital Solutions</li>
            </ol>
        </nav>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <p class="lead">Technology should work for your business, not the other way around. Our IT and digital solutions help organisations of all sizes make smart technology choices, improve operational efficiency, and build the digital capabilities they need to compete and grow.</p>

                <h2>IT Consulting</h2>
                <p>Making the right technology decisions requires a clear understanding of your business goals, current capabilities, and the options available. Our IT consulting services provide impartial, practical advice on technology strategy, system selection, infrastructure planning, and IT operations. We help you evaluate options, understand costs and benefits, and develop a roadmap that aligns technology investment with business priorities.</p>
                <p>Whether you are considering a move to the cloud, reviewing your IT support arrangements, or planning a major system upgrade, we provide the independent perspective and technical knowledge to help you move forward with confidence.</p>

                <h2>Business Automation</h2>
                <p>Manual, repetitive processes consume time and introduce errors. We help organisations identify automation opportunities and implement practical solutions that reduce manual effort, improve accuracy, and free up your teams to focus on higher-value work. Our automation work covers workflow automation, data integration between systems, report generation, notification and alerting, and process digitisation. We focus on practical, achievable automation that delivers measurable time and cost savings.</p>

                <h2>Website Development</h2>
                <p>Your website is often the first impression potential clients have of your business. We design and build professional, responsive websites that reflect your brand, communicate your value, and support your business objectives. From simple brochure sites to complex business portals, our development approach prioritises usability, accessibility, performance, and search engine visibility. Every website we build is designed to grow with your business.</p>

                <h2>SEO</h2>
                <p>A great website needs to be found. Our SEO services help improve your search engine visibility, attract qualified traffic, and convert visitors into enquiries. We cover technical SEO, on-page optimisation, content strategy, local SEO, and performance monitoring. Our approach is data-driven and transparent — we explain what we are doing, why, and what results you can expect. For organisations that rely on local or regional visibility, we pay particular attention to Google Business Profile optimisation and local search presence.</p>

                <h2>Digital Marketing</h2>
                <p>Effective digital marketing connects your business with the right audience at the right time. We provide strategic guidance on digital marketing planning, channel selection, content marketing, and performance measurement. Our support is practical and focused on B2B organisations — helping you build a digital marketing approach that generates leads, supports existing client relationships, and strengthens your market position without unnecessary spend.</p>

                <h2>CRM Setup</h2>
                <p>Managing client relationships effectively requires the right tools and processes. We help organisations select, configure, and adopt CRM systems that fit their sales and service workflows. Our support covers requirements gathering, system selection, configuration, data migration, user training, and ongoing optimisation. We focus on making the CRM work for your team — not burdening them with additional admin.</p>

                <h2>Cloud Guidance</h2>
                <p>Moving to the cloud or optimising your existing cloud setup can deliver significant benefits in flexibility, cost, and capability. We provide practical cloud guidance covering migration planning, platform selection, cost optimisation, security considerations, and integration with existing systems. Our advice is vendor-neutral and focused on what delivers the best outcome for your business rather than what suits a particular cloud provider.</p>

                <h2>Digital Workflow Improvement</h2>
                <p>Many organisations have pockets of paper-based or spreadsheet-driven processes that could be digitised for greater efficiency. We review your operational workflows, identify digitisation opportunities, and help you implement practical digital solutions. This might include online forms, automated approvals, digital document management, or system integrations that eliminate duplicate data entry. Every improvement is designed to save time, reduce errors, and improve visibility.</p>
            </div>

            <div class="col-lg-4">
                <div class="highlight-box mb-4">
                    <h4 class="mb-3"><i class="bi bi-cpu text-primary me-2"></i>Technology Areas</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>IT Strategy & Planning</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Cloud Migration & Optimisation</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Business Process Automation</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Website Design & Development</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>SEO & Digital Marketing</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>CRM Selection & Setup</li>
                        <li><i class="bi bi-check-lg text-primary me-2"></i>Digital Workflow Design</li>
                    </ul>
                </div>

                <div class="info-box mb-4">
                    <p><i class="bi bi-info-circle-fill me-2"></i>Our IT consulting is independent and vendor-neutral. We recommend solutions that are right for your business, your budget, and your team — not what suits a particular technology vendor.</p>
                </div>

                <div class="highlight-box">
                    <h4 class="mb-3"><i class="bi bi-building text-primary me-2"></i>Who We Help</h4>
                    <p class="mb-2">Our IT and digital solutions are designed for:</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-dot text-primary"></i>Small and medium businesses without in-house IT</li>
                        <li class="mb-2"><i class="bi bi-dot text-primary"></i>Growing organisations upgrading legacy systems</li>
                        <li class="mb-2"><i class="bi bi-dot text-primary"></i>Professional services firms improving digital presence</li>
                        <li><i class="bi bi-dot text-primary"></i>Operations-focused businesses automating processes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Ready to Improve Your Technology?</h2>
        <p>Whether you need a new website, IT strategy advice, or help automating your processes, we are here to help. Book a free consultation to discuss your requirements.</p>
        <a href="/contact-us" class="btn-white btn-lg">
            <i class="bi bi-calendar-check"></i> Book a Free Consultation
        </a>
    </div>
</section>
