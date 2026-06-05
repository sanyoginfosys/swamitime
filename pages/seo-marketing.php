<?php
$slug = 'seo-digital-marketing';
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
        <h1>SEO & Digital Marketing</h1>
        <p>Improve your search visibility, attract qualified leads, and grow your digital presence with data-driven strategies</p>
    </div>
</div>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/services">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">SEO & Digital Marketing</li>
            </ol>
        </nav>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <p class="lead">Being visible online is essential for B2B growth — but it requires more than just having a website. Our SEO and digital marketing services help your business get found by the right people, at the right time, with strategies that deliver measurable results.</p>

                <h2>Search Engine Optimisation (SEO)</h2>
                <p>SEO is the foundation of sustainable online visibility. Our approach covers every aspect of SEO — from the technical foundations of your website to the content that attracts and engages your audience. We start with a comprehensive audit of your current position, identifying strengths, weaknesses, and opportunities. From there, we develop a prioritised plan that addresses the areas that will have the greatest impact on your search performance.</p>
                <p>Our SEO work includes keyword research and targeting, on-page optimisation, technical SEO improvements, content strategy, link building guidance, and ongoing performance monitoring. We focus on tactics that comply with search engine guidelines and deliver sustainable results over time — not quick wins that may harm your site in the long run.</p>

                <h2>Technical SEO</h2>
                <p>The technical health of your website directly affects how search engines crawl, index, and rank your pages. Our technical SEO services cover site speed optimisation, mobile-friendliness, structured data markup, XML sitemaps, robots.txt configuration, canonical tags, broken link identification, redirect management, and HTTPS implementation. We identify and fix technical issues that may be holding back your search performance, ensuring that search engines can access and understand your content without obstacles.</p>

                <h2>On-Page SEO</h2>
                <p>Every page on your website should be optimised for both search engines and human readers. Our on-page SEO work covers title tags, meta descriptions, heading structure, internal linking, image alt text, content quality and relevance, and keyword optimisation. We ensure that each page clearly signals its topic and purpose to search engines while providing a great experience for visitors. On-page optimisation is an ongoing process — we regularly review and refine your content to maintain and improve rankings.</p>

                <h2>Local SEO</h2>
                <p>For businesses that serve specific geographic areas, local SEO is essential. Our local SEO services focus on improving your visibility in local search results and Google Maps. This includes Google Business Profile optimisation, local citation building, review management guidance, location-specific landing pages, and local link building. We help ensure that when potential clients in your area search for services you provide, your business appears prominently in the results.</p>

                <h2>Content Marketing</h2>
                <p>Content is at the heart of effective SEO and digital marketing. We help you develop a content strategy that addresses the questions and needs of your target audience — positioning your business as a knowledgeable, trustworthy resource. Our content marketing support covers blog strategy, service page content, case studies, guides and whitepapers, and content calendars. We focus on quality over quantity — creating content that genuinely serves your audience and supports your business goals.</p>

                <h2>Digital Marketing Strategy</h2>
                <p>SEO is most effective when it is part of a broader digital marketing strategy. We help you develop an integrated approach that connects your website, search presence, content, social media, and email marketing into a coherent plan. Our strategic guidance covers audience definition, channel selection, messaging, budgeting, and performance measurement. We work with B2B organisations to build marketing approaches that are practical, sustainable, and focused on generating qualified leads.</p>

                <h2>Google Business Profile Optimisation</h2>
                <p>Your Google Business Profile is often the first thing potential clients see when they search for your business. We help you create and optimise your profile — ensuring that your business information is accurate and complete, adding relevant categories and services, posting regular updates, managing and responding to reviews, and adding high-quality images. A well-maintained Google Business Profile improves your local visibility and gives potential clients the information they need to choose your business.</p>

                <h2>Performance Tracking & Reporting</h2>
                <p>You cannot improve what you do not measure. We set up comprehensive analytics and reporting that track your search performance, website traffic, user behaviour, and lead generation. Our reports are clear, jargon-free, and focused on the metrics that matter to your business — not vanity numbers. We provide regular performance updates with insights and recommendations, so you always know how your digital presence is performing and where to focus next.</p>
            </div>

            <div class="col-lg-4">
                <div class="highlight-box mb-4">
                    <h4 class="mb-3"><i class="bi bi-search text-primary me-2"></i>SEO Services</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>SEO Audits & Strategy</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Keyword Research & Targeting</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Technical SEO Improvements</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>On-Page Optimisation</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Local SEO & Google Business Profile</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Content Strategy & Creation</li>
                        <li><i class="bi bi-check-lg text-primary me-2"></i>Performance Analytics & Reporting</li>
                    </ul>
                </div>

                <div class="info-box mb-4">
                    <p><i class="bi bi-info-circle-fill me-2"></i>SEO is a long-term investment. While some improvements can show results within weeks, sustainable search visibility typically builds over months. We are transparent about timelines and expectations from the start.</p>
                </div>

                <div class="highlight-box">
                    <h4 class="mb-3"><i class="bi bi-graph-up-arrow text-primary me-2"></i>What Success Looks Like</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Higher rankings for target keywords</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Increased organic search traffic</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>More qualified enquiries and leads</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Improved local search visibility</li>
                        <li><i class="bi bi-check-lg text-primary me-2"></i>Better user engagement on your site</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Ready to Grow Your Digital Presence?</h2>
        <p>Whether you need a full SEO programme or help with a specific aspect of digital marketing, we are here to help. Book a free consultation today.</p>
        <a href="/contact-us" class="btn-white btn-lg">
            <i class="bi bi-calendar-check"></i> Book a Free Consultation
        </a>
    </div>
</section>
