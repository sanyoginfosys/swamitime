<?php
$slug = 'web-development';
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
        <h1>Web Development</h1>
        <p>Custom websites and digital platforms built to perform — responsive, accessible, and designed around your business goals</p>
    </div>
</div>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/services">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">Web Development</li>
            </ol>
        </nav>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <p class="lead">Your website is a critical business asset. It represents your brand, communicates your value, and often serves as the first point of contact for potential clients. We design and build websites that are professional, fast, accessible, and aligned with your business objectives.</p>

                <h2>Custom Web Development</h2>
                <p>Off-the-shelf templates can only take you so far. Our custom web development service creates websites that are built specifically for your business — reflecting your brand identity, supporting your unique requirements, and providing a user experience that sets you apart from competitors. We work with modern technologies and frameworks to deliver websites that are secure, scalable, and easy to maintain.</p>
                <p>Every project starts with understanding your business, your audience, and your goals. We then design and build a solution that meets those needs — whether that is a simple brochure site, a complex business portal, or anything in between. Our development process is transparent, with regular updates and opportunities for your feedback at every stage.</p>

                <h2>Responsive Design</h2>
                <p>Your website must work seamlessly across every device — desktop, tablet, and mobile. All our websites are built with responsive design as a core principle, not an afterthought. We test across multiple devices and screen sizes to ensure that every user has a great experience, regardless of how they access your site. Responsive design is also a key factor in search engine rankings, so getting it right benefits both your users and your visibility.</p>

                <h2>Business Websites</h2>
                <p>A professional business website communicates credibility, showcases your services, and converts visitors into enquiries. We build business websites that are clean, modern, and focused on results. Every element — from the navigation structure to the call-to-action buttons — is designed with your target audience in mind. We also ensure that your website is optimised for search engines from day one, with proper heading structure, meta tags, fast loading times, and mobile-friendly design.</p>

                <h2>Client Portals & Business Platforms</h2>
                <p>Beyond brochure websites, we build functional digital platforms that support your business operations. This includes client portals for document sharing and communication, booking and scheduling systems, secure member areas, and custom web applications that streamline your workflows. These platforms are built with security, usability, and reliability as priorities — ensuring that both your team and your clients have a positive experience.</p>

                <h2>E-Commerce</h2>
                <p>For businesses looking to sell products or services online, we build e-commerce websites that are secure, easy to manage, and designed to convert. Our e-commerce solutions cover product catalogues, shopping carts, secure payment processing, order management, and integration with your existing systems. We focus on creating a straightforward buying experience that encourages customers to complete their purchases.</p>

                <h2>Content Management Systems</h2>
                <p>We believe you should be able to manage your own website content without needing technical skills. Our websites are built on established content management systems that give you control over your pages, blog posts, images, and other content. We provide training and documentation so your team can make updates confidently, and we are always available for ongoing support when you need it.</p>

                <h2>Performance & Accessibility</h2>
                <p>A slow or inaccessible website loses visitors and damages your reputation. We build websites that load quickly, perform well on all devices, and meet accessibility standards (WCAG 2.1). This includes optimised images, efficient code, proper semantic HTML, keyboard navigation support, and appropriate colour contrast. We test performance and accessibility throughout development to ensure your site delivers a great experience for every user.</p>

                <h2>Ongoing Support & Maintenance</h2>
                <p>A website needs regular care to stay secure, up-to-date, and performing well. We offer ongoing support and maintenance packages that cover software updates, security monitoring, backup management, content updates, and performance optimisation. Whether you need occasional updates or regular monthly support, we have a package that fits your needs.</p>
            </div>

            <div class="col-lg-4">
                <div class="highlight-box mb-4">
                    <h4 class="mb-3"><i class="bi bi-window-stack text-primary me-2"></i>What We Build</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Corporate & Business Websites</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Client Portals & Login Areas</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>E-Commerce Stores</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Landing Pages & Campaign Sites</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Booking & Scheduling Systems</li>
                        <li><i class="bi bi-check-lg text-primary me-2"></i>Custom Web Applications</li>
                    </ul>
                </div>

                <div class="info-box mb-4">
                    <p><i class="bi bi-info-circle-fill me-2"></i>Every website we build is designed with SEO, performance, and accessibility in mind from day one — not bolted on afterwards.</p>
                </div>

                <div class="highlight-box">
                    <h4 class="mb-3"><i class="bi bi-shield-check text-primary me-2"></i>Our Standards</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Responsive across all devices</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>WCAG 2.1 accessibility compliance</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Fast page load performance</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>SEO-optimised structure</li>
                        <li class="mb-2"><i class="bi bi-check-lg text-primary me-2"></i>Secure by design (HTTPS, data protection)</li>
                        <li><i class="bi bi-check-lg text-primary me-2"></i>CMS integration for self-management</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Let's Build Your Website</h2>
        <p>Whether you need a new business website or a custom digital platform, we would love to discuss your project. Book a free consultation today.</p>
        <a href="/contact-us" class="btn-white btn-lg">
            <i class="bi bi-calendar-check"></i> Book a Free Consultation
        </a>
    </div>
</section>
