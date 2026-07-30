<?php
$slug = 'about-us';
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
$title = $pageData['title'] ?? 'About SWAMITIME SOLUTIONS LTD';
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>About SWAMITIME SOLUTIONS LTD</h1>
        <p>Independent workforce management and digital solutions consultancy delivering measurable operational improvements across UK industries.</p>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About Us</li>
            </ol>
        </nav>
    </div>
</section>

<?php if (!empty($pageData['content'])): ?>
<section class="section">
    <div class="container">
        <div class="content-section">
            <?php echo $pageData['content']; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Who We Are -->
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="about-img-wrap">
                    <div class="about-img" style="background: var(--gradient); height: 400px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                        <div style="position: absolute; inset: 0; background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.08) 1px, transparent 0); background-size: 24px 24px;"></div>
                        <div style="position: relative; z-index: 1; text-align: center; color: #fff; padding: 2rem;">
                            <i class="bi bi-building" style="font-size: 4rem; display: block; margin-bottom: 1rem; opacity: 0.9;"></i>
                            <span style="font-family: var(--font-heading); font-weight: 800; font-size: 1.5rem; letter-spacing: 1px;">SWAMITIME</span>
                            <small class="d-block" style="font-size: 0.65rem; letter-spacing: 3px; opacity: 0.8;">SOLUTIONS LTD</small>
                        </div>
                    </div>
                    <div class="about-stats-card">
                        <div class="stat-number">10+</div>
                        <div class="stat-label">Years Industry Experience</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-header text-start mb-3">
                    <span class="section-label">Who We Are</span>
                    <h2 class="section-title">Independent Workforce <span>Management</span> &amp; Digital Solutions Consultancy</h2>
                </div>
                <p>SWAMITIME SOLUTIONS LTD is a UK-based independent consultancy specialising in workforce management, UKG system support, and digital solutions. Founded with a clear purpose &mdash; to help organisations get the most from their workforce technology investments &mdash; we bring impartial, practical expertise to every engagement.</p>
                <p>Unlike larger generalist consultancies or vendor-tied resellers, we remain completely independent. This means our recommendations are always aligned with <em>your</em> operational needs, not a vendor&rsquo;s product roadmap or a sales target. We work alongside your teams to understand your challenges, configure the right solutions, and transfer knowledge so you become self-sufficient.</p>
                <p>We serve organisations across the United Kingdom &mdash; from small and medium-sized businesses to large enterprises &mdash; across retail, hospitality, logistics, manufacturing, healthcare, and professional services. Our approach is grounded in real-world operational experience, not just technical theory.</p>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Our Values</span>
            <h2 class="section-title">What <span>Drives</span> Us</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Four principles that shape every engagement, every recommendation, and every relationship we build.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="metric-card" data-aos="fade-up" data-aos-delay="0">
                    <div class="metric-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <h3 class="h5 mb-2">Practical</h3>
                    <p class="metric-label">We focus on what works in the real world. Our recommendations are grounded in operational reality, delivering outcomes that make a tangible difference to your day-to-day business &mdash; not theoretical models that look good on paper.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="metric-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="h5 mb-2">Independent</h3>
                    <p class="metric-label">We are not tied to any single vendor, platform, or product. Our advice is always impartial and aligned with your objectives. When we recommend a solution, it is because it is genuinely the right fit for your organisation.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="metric-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="h5 mb-2">Client-Focused</h3>
                    <p class="metric-label">Your success is our priority. We listen carefully, ask the right questions, and design solutions around your specific operational context. We measure our success by the outcomes you achieve, not by billable hours.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="metric-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h3 class="h5 mb-2">Experienced</h3>
                    <p class="metric-label">With over a decade of hands-on workforce management and technology delivery, we bring deep domain knowledge to every project. We have seen what works &mdash; and what does not &mdash; across multiple industries and system landscapes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Approach -->
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="section-header text-start mb-3">
                    <span class="section-label">Our Approach</span>
                    <h2 class="section-title">How We <span>Work</span> With You</h2>
                </div>
                <p>Every engagement begins with understanding. We invest time upfront to learn about your organisation &mdash; your operational model, your workforce structure, your technology landscape, and the specific challenges you face. This discovery phase ensures that every subsequent recommendation is grounded in your reality.</p>
                <p>We believe in knowledge transfer, not dependency. While we provide hands-on support throughout implementation, configuration, and optimisation, our goal is always to equip your internal teams with the skills and understanding they need to operate independently. We document thoroughly, train comprehensively, and remain available for ongoing advisory support when you need it.</p>
                <p>Our methodology combines structured project management with the flexibility to adapt as requirements evolve. We work in collaborative phases with clear milestones, regular checkpoints, and transparent communication throughout.</p>
            </div>
            <div class="col-lg-6">
                <div class="about-values">
                    <div class="about-value-item" data-aos="fade-left">
                        <div class="value-icon"><i class="bi bi-1-circle-fill"></i></div>
                        <div>
                            <h5>Discovery &amp; Analysis</h5>
                            <p>We immerse ourselves in your operational context, reviewing existing processes, systems, and data to identify opportunities for improvement.</p>
                        </div>
                    </div>
                    <div class="about-value-item" data-aos="fade-left" data-aos-delay="100">
                        <div class="value-icon"><i class="bi bi-2-circle-fill"></i></div>
                        <div>
                            <h5>Solution Design</h5>
                            <p>We develop a tailored proposal outlining the recommended approach, configuration, and implementation plan aligned with your business objectives.</p>
                        </div>
                    </div>
                    <div class="about-value-item" data-aos="fade-left" data-aos-delay="200">
                        <div class="value-icon"><i class="bi bi-3-circle-fill"></i></div>
                        <div>
                            <h5>Implementation &amp; Configuration</h5>
                            <p>We work alongside your teams to configure, test, and deploy solutions with minimal disruption to your ongoing operations.</p>
                        </div>
                    </div>
                    <div class="about-value-item" data-aos="fade-left" data-aos-delay="300">
                        <div class="value-icon"><i class="bi bi-4-circle-fill"></i></div>
                        <div>
                            <h5>Training &amp; Handover</h5>
                            <p>We deliver comprehensive training and documentation to ensure your team has the confidence and capability to operate independently.</p>
                        </div>
                    </div>
                    <div class="about-value-item" data-aos="fade-left" data-aos-delay="400">
                        <div class="value-icon"><i class="bi bi-5-circle-fill"></i></div>
                        <div>
                            <h5>Ongoing Support</h5>
                            <p>We remain available for managed support, periodic health checks, and advisory services to keep your systems performing optimally.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Experience & Background -->
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Experience</span>
            <h2 class="section-title">Decades of Combined <span>Expertise</span></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">A team built on deep domain knowledge, technical proficiency, and a genuine commitment to client outcomes.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="industry-card" data-aos="fade-up">
                    <div class="icon-circle"><i class="bi bi-gear-wide-connected"></i></div>
                    <h4>Workforce Management</h4>
                    <p>Extensive experience across UKG Pro WFM, UKG Dimensions, UKG Ready, and UKG TeleStaff. We have managed implementations, migrations, configurations, and optimisations for organisations with workforces ranging from 50 to over 10,000 employees.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="industry-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="icon-circle"><i class="bi bi-laptop"></i></div>
                    <h4>Digital &amp; IT Solutions</h4>
                    <p>Background in IT infrastructure, digital transformation, web development, and systems integration. We bridge the gap between operational workforce needs and the technology that enables them.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="industry-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="icon-circle"><i class="bi bi-building-check"></i></div>
                    <h4>Multi-Industry Delivery</h4>
                    <p>Projects delivered across retail, hospitality, logistics, manufacturing, healthcare, professional services, and SMEs. Each industry brings unique workforce management challenges &mdash; and we understand them all.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Philosophy -->
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <div class="section-header text-start mb-3">
                    <span class="section-label">Team Philosophy</span>
                    <h2 class="section-title">Small Team, <span>Big Impact</span></h2>
                </div>
                <p>We deliberately remain a lean, focused consultancy. This allows us to maintain the highest standards of quality, build genuine relationships with every client, and stay deeply connected to the work we deliver. We do not operate a pyramid model where senior people sell and junior people deliver &mdash; the people you meet at the start are the people who will do the work.</p>
                <p>Our consultants combine technical expertise with commercial awareness. We speak the language of operations, finance, HR, and IT equally fluently, enabling us to bridge gaps between departments and translate business requirements into technical solutions that work.</p>
                <a href="/contact-us" class="btn-link-teal">Speak with our team <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="col-lg-7">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="testimonial-card" data-aos="fade-up">
                            <div class="quote-icon"><i class="bi bi-quote"></i></div>
                            <p class="quote">Technical depth combined with a genuine understanding of operational reality. A rare combination that makes a real difference.</p>
                            <div class="author">
                                <div class="author-avatar"><i class="bi bi-person-fill"></i></div>
                                <div class="author-info">
                                    <h5>Operations Director</h5>
                                    <span>Confidential Client, Retail Sector</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="quote-icon"><i class="bi bi-quote"></i></div>
                            <p class="quote">Professional, knowledgeable, and refreshingly independent. They tell you what you need to hear, not what is easy to say.</p>
                            <div class="author">
                                <div class="author-avatar"><i class="bi bi-person-fill"></i></div>
                                <div class="author-info">
                                    <h5>HR Director</h5>
                                    <span>Confidential Client, Logistics Sector</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Work With Us?</h2>
        <p>Let us discuss your workforce management challenges and explore how we can help. Book a free, no-obligation consultation today.</p>
        <a href="/contact-us" class="btn-white">Book a Free Consultation <i class="bi bi-arrow-right"></i></a>
        <div class="cta-trust">No commitment &bull; Confidential discussion &bull; Tailored advice</div>
    </div>
</section>
