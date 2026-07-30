<?php
$slug = 'contact-us';
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
$title = $pageData['title'] ?? 'Contact Us';
$csrf_token = generate_csrf_token();

$contactEmail   = '';
$contactPhone   = '';
$contactAddress = '';
$contactHours   = '';
try {
    $rows = $db->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('site_email','site_phone','site_address','working_hours')")->fetchAll();
    foreach ($rows as $r) {
        if ($r['setting_key'] === 'site_email')     $contactEmail   = $r['setting_value'];
        if ($r['setting_key'] === 'site_phone')     $contactPhone   = $r['setting_value'];
        if ($r['setting_key'] === 'site_address')   $contactAddress = $r['setting_value'];
        if ($r['setting_key'] === 'working_hours')  $contactHours   = $r['setting_value'];
    }
} catch (Exception $e) {}
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Ready to discuss your workforce management or digital solutions needs? Get in touch and a member of our team will respond within one business day.</p>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Contact Form + Info -->
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="contact-form" id="contactFormWrapper">
                    <h3 class="mb-4">Send Us a Message</h3>
                    <form id="contactForm" action="/api/contact-submit.php" method="POST" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="d-none">
                            <label for="website">Website</label>
                            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Your full name" required>
                                <div class="invalid-feedback">Please enter your full name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Your company (optional)">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="you@company.com" required>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+44 (0) 0000 000000">
                            </div>
                            <div class="col-md-6">
                                <label for="service_required" class="form-label">Service Required</label>
                                <select class="form-select" id="service_required" name="service_required">
                                    <option value="" selected disabled>Select a service...</option>
                                    <option value="ukg-support">UKG Workforce Management Support</option>
                                    <option value="workforce-consulting">Workforce Management Consulting</option>
                                    <option value="implementation">Implementation &amp; Configuration Support</option>
                                    <option value="training">Training &amp; User Support</option>
                                    <option value="managed-support">Managed Support Services</option>
                                    <option value="reporting">Reporting &amp; Data Support</option>
                                    <option value="it-solutions">IT &amp; Digital Solutions</option>
                                    <option value="web-development">Web Development</option>
                                    <option value="seo-marketing">SEO &amp; Digital Marketing</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="budget_range" class="form-label">Budget Range</label>
                                <select class="form-select" id="budget_range" name="budget_range">
                                    <option value="" selected disabled>Select budget range...</option>
                                    <option value="under-5000">Under &pound;5,000</option>
                                    <option value="5000-15000">&pound;5,000 &ndash; &pound;15,000</option>
                                    <option value="15000-50000">&pound;15,000 &ndash; &pound;50,000</option>
                                    <option value="50000-plus">&pound;50,000+</option>
                                    <option value="prefer-not">Prefer Not to Say</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Preferred Contact Method</label>
                                <div class="d-flex gap-4 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="contact_method" id="contact_email" value="email" checked>
                                        <label class="form-check-label" for="contact_email">Email</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="contact_method" id="contact_phone" value="phone">
                                        <label class="form-check-label" for="contact_phone">Phone</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="contact_method" id="contact_either" value="either">
                                        <label class="form-check-label" for="contact_either">Either</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Your Message <span class="required">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Tell us about your project, requirements, or questions..." required></textarea>
                                <div class="invalid-feedback">Please enter your message.</div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gdpr_consent" name="gdpr_consent" value="1" required>
                                    <label class="form-check-label" for="gdpr_consent" style="font-size: 0.85rem; color: var(--muted-text);">
                                        I agree to the processing of my personal data in accordance with the <a href="/privacy-policy" target="_blank">Privacy Policy</a>. <span class="required">*</span>
                                    </label>
                                    <div class="invalid-feedback">You must agree to the privacy policy to proceed.</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-submit" id="contactSubmitBtn">
                                    <span class="btn-text">Send Message</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="contactFormMessage" class="mt-3 d-none"></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-4">
                    <?php if ($contactEmail): ?>
                    <div class="col-12">
                        <div class="contact-info-card" data-aos="fade-left">
                            <div class="info-icon"><i class="bi bi-envelope"></i></div>
                            <h4>Email</h4>
                            <p><a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>" class="contact-link"><?php echo htmlspecialchars($contactEmail); ?></a></p>
                            <p class="small mt-1">We aim to respond to all enquiries within one business day.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($contactPhone): ?>
                    <div class="col-12">
                        <div class="contact-info-card" data-aos="fade-left" data-aos-delay="100">
                            <div class="info-icon"><i class="bi bi-telephone"></i></div>
                            <h4>Phone</h4>
                            <p><a href="tel:<?php echo htmlspecialchars(preg_replace('/[^\d+]/', '', $contactPhone)); ?>" class="contact-link"><?php echo htmlspecialchars($contactPhone); ?></a></p>
                            <p class="small mt-1">Available Monday to Friday, 9:00 AM &ndash; 5:30 PM (GMT).</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($contactAddress): ?>
                    <div class="col-12">
                        <div class="contact-info-card" data-aos="fade-left" data-aos-delay="200">
                            <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                            <h4>Location</h4>
                            <p><?php echo htmlspecialchars($contactAddress); ?></p>
                            <p class="small mt-1">We serve clients across England, Scotland, Wales, and Northern Ireland, with remote and on-site delivery options available.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($contactHours): ?>
                    <div class="col-12">
                        <div class="contact-info-card" data-aos="fade-left" data-aos-delay="300">
                            <div class="info-icon"><i class="bi bi-clock"></i></div>
                            <h4>Working Hours</h4>
                            <p><?php echo htmlspecialchars($contactHours); ?></p>
                            <p class="small mt-1">Out-of-hours support available for managed service clients under agreed service level agreements (SLAs).</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="mt-4">
                    <?php if ($contactAddress): ?>
                    <div class="contact-map" style="background: var(--gradient); min-height: 250px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.95rem; text-align: center; padding: 2rem;">
                        <div>
                            <i class="bi bi-geo-alt-fill" style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem; opacity: 0.8;"></i>
                            <strong><?php echo htmlspecialchars($contactAddress); ?></strong><br>
                            <span style="opacity: 0.8; font-size: 0.85rem;">Serving clients nationwide</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<?php if ($contactPhone): ?>
<section class="cta-section">
    <div class="container">
        <h2>Prefer a Direct Call?</h2>
        <p>Schedule a free 30-minute consultation at a time that suits you. We will listen to your challenges and provide initial guidance with no obligation.</p>
        <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^\d+]/', '', $contactPhone)); ?>" class="btn-white">Call Us Now <i class="bi bi-telephone-fill"></i></a>
        <div class="cta-trust">Free initial consultation &bull; No commitment &bull; Expert advice</div>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        var btn = document.getElementById('contactSubmitBtn');
        var btnText = btn.querySelector('.btn-text');
        var spinner = btn.querySelector('.spinner-border');
        var icon = btn.querySelector('.bi-send-fill');
        var msgDiv = document.getElementById('contactFormMessage');

        btn.disabled = true;
        btnText.textContent = 'Sending...';
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            msgDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
            if (data.success) {
                msgDiv.classList.add('alert', 'alert-success');
                msgDiv.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + (data.message || 'Thank you for your message. We will be in touch shortly.');
                form.reset();
                form.classList.remove('was-validated');
            } else {
                msgDiv.classList.add('alert', 'alert-danger');
                msgDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (data.message || 'Something went wrong. Please try again or email us directly.');
            }
        })
        .catch(function () {
            msgDiv.classList.remove('d-none');
            msgDiv.classList.add('alert', 'alert-danger');
            msgDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Network error. Please try again<?php if ($contactEmail): ?> or email us at <?php echo htmlspecialchars($contactEmail); ?><?php endif; ?>.';
        })
        .finally(function () {
            btn.disabled = false;
            btnText.textContent = 'Send Message';
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');
        });
    });
});
</script>
