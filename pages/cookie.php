<?php
$slug = 'cookie-policy';
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
$title = $pageData['title'] ?? 'Cookie Policy';
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Cookie Policy</h1>
        <p>Information about how and why we use cookies on our website.</p>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cookie Policy</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Cookie Policy Content -->
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <p class="text-muted mb-4"><em>Last updated: 28 May 2026</em></p>

                <p>This Cookie Policy explains how SWAMITIME SOLUTIONS LTD (&ldquo;we,&rdquo; &ldquo;us,&rdquo; or &ldquo;our&rdquo;) uses cookies and similar tracking technologies on our website. It explains what cookies are, how we use them, the types of cookies we set, and how you can manage your preferences.</p>
                <p>For more information about how we handle your personal data, please see our <a href="/privacy-policy">Privacy Policy</a>.</p>

                <h3 class="mt-5 mb-3">1. What Are Cookies?</h3>
                <p>Cookies are small text files that are placed on your device (computer, tablet, or mobile phone) when you visit a website. They are widely used to make websites work more efficiently, to improve the user experience, and to provide information to the website owners.</p>
                <p>Cookies may be &ldquo;session cookies,&rdquo; which are deleted when you close your browser, or &ldquo;persistent cookies,&rdquo; which remain on your device for a set period or until you delete them manually. Cookies may be set by our website (&ldquo;first-party cookies&rdquo;) or by third-party services that we use (&ldquo;third-party cookies&rdquo;).</p>

                <h3 class="mt-5 mb-3">2. How We Use Cookies</h3>
                <p>We use cookies for the following purposes:</p>
                <ul>
                    <li>To ensure the website functions correctly and securely</li>
                    <li>To remember your cookie preferences and consent choices</li>
                    <li>To analyse how visitors use our website so we can improve its performance and content</li>
                    <li>To understand which pages and content are most relevant to our visitors</li>
                </ul>
                <p>We do not use cookies to collect personally identifiable information without your explicit consent. We do not sell or share cookie data with advertisers or data brokers.</p>

                <h3 class="mt-5 mb-3">3. Types of Cookies We Use</h3>

                <h4 class="mt-4">3.1 Essential Cookies (Strictly Necessary)</h4>
                <p>These cookies are essential for the website to function properly and cannot be disabled in our systems. They are typically set in response to actions you take, such as setting your privacy preferences or completing a form. These cookies do not store personally identifiable information.</p>
                <table class="table table-bordered mt-3" style="font-size: 0.9rem;">
                    <thead style="background: var(--light-bg);">
                        <tr>
                            <th>Cookie Name</th>
                            <th>Purpose</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>cookie_consent</code></td>
                            <td>Stores your cookie consent preferences</td>
                            <td>12 months</td>
                        </tr>
                        <tr>
                            <td><code>SWAMITIME_SESSION</code></td>
                            <td>Maintains your session state for security (CSRF protection, form submissions)</td>
                            <td>Session</td>
                        </tr>
                    </tbody>
                </table>

                <h4 class="mt-4">3.2 Analytics Cookies</h4>
                <p>These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously. They allow us to measure visits, identify popular pages, and understand navigation patterns. We use this information to improve our website and the content we provide.</p>
                <table class="table table-bordered mt-3" style="font-size: 0.9rem;">
                    <thead style="background: var(--light-bg);">
                        <tr>
                            <th>Cookie Name</th>
                            <th>Purpose</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>_ga</code></td>
                            <td>Google Analytics &ndash; distinguishes unique visitors</td>
                            <td>2 years</td>
                        </tr>
                        <tr>
                            <td><code>_ga_*</code></td>
                            <td>Google Analytics &ndash; maintains session state</td>
                            <td>2 years</td>
                        </tr>
                        <tr>
                            <td><code>_gid</code></td>
                            <td>Google Analytics &ndash; distinguishes visitors</td>
                            <td>24 hours</td>
                        </tr>
                        <tr>
                            <td><code>_gat</code></td>
                            <td>Google Analytics &ndash; throttles request rate</td>
                            <td>1 minute</td>
                        </tr>
                    </tbody>
                </table>
                <p>Google Analytics cookies are set only if you consent to analytics cookies via our cookie banner. We have configured Google Analytics to anonymise IP addresses and have disabled data sharing with Google.</p>

                <h4 class="mt-4">3.3 Functional Cookies</h4>
                <p>These cookies enable the website to provide enhanced functionality and a more personalised experience. They may be set by us or by third-party providers whose services we have added to our pages.</p>
                <table class="table table-bordered mt-3" style="font-size: 0.9rem;">
                    <thead style="background: var(--light-bg);">
                        <tr>
                            <th>Cookie Name</th>
                            <th>Purpose</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>preferred_theme</code></td>
                            <td>Remembers your display preference (if applicable)</td>
                            <td>12 months</td>
                        </tr>
                    </tbody>
                </table>

                <h4 class="mt-4">3.4 Marketing Cookies</h4>
                <p>We do not currently set marketing or advertising cookies on our website. Should this change in the future, we will update this policy and seek your consent before any such cookies are deployed.</p>

                <h3 class="mt-5 mb-3">4. Third-Party Cookies</h3>
                <p>Some cookies on our website are set by trusted third-party services that we use. These include:</p>
                <ul>
                    <li><strong>Google Analytics:</strong> As described above, for anonymous website usage analysis. Google&rsquo;s privacy policy is available at <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">policies.google.com/privacy</a>.</li>
                    <li><strong>Cloudflare (if applicable):</strong> For website security and performance. Cloudflare&rsquo;s cookie policy is available at <a href="https://www.cloudflare.com/cookie-policy/" target="_blank" rel="noopener noreferrer">www.cloudflare.com/cookie-policy</a>.</li>
                </ul>
                <p>We do not control the setting of third-party cookies. We recommend checking the relevant third-party website for more information about their cookie practices.</p>

                <h3 class="mt-5 mb-3">5. Managing Your Cookie Preferences</h3>
                <p>When you first visit our website, you will see a cookie consent banner that allows you to accept or manage your cookie preferences. You can change your preferences at any time by clicking the &ldquo;Cookie Settings&rdquo; link in the website footer or by clearing your browser cookies and revisiting the website.</p>
                <p>You can also control cookies through your browser settings. Most browsers allow you to:</p>
                <ul>
                    <li>View the cookies stored on your device and delete them individually or in bulk</li>
                    <li>Block third-party cookies</li>
                    <li>Block cookies from specific websites</li>
                    <li>Block all cookies (note: this may prevent some website features from functioning)</li>
                    <li>Delete all cookies when you close your browser</li>
                </ul>
                <p>Instructions for managing cookies in popular browsers:</p>
                <ul>
                    <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener noreferrer">Google Chrome</a></li>
                    <li><a href="https://support.mozilla.org/en-US/kb/enhanced-tracking-protection-firefox-desktop" target="_blank" rel="noopener noreferrer">Mozilla Firefox</a></li>
                    <li><a href="https://support.apple.com/en-gb/guide/safari/sfri11471/mac" target="_blank" rel="noopener noreferrer">Apple Safari</a></li>
                    <li><a href="https://support.microsoft.com/en-us/windows/delete-and-manage-cookies-168dab11-0753-043d-7c16-ede5947fc64d" target="_blank" rel="noopener noreferrer">Microsoft Edge</a></li>
                </ul>

                <h3 class="mt-5 mb-3">6. Updates to This Policy</h3>
                <p>We may update this Cookie Policy from time to time to reflect changes in the cookies we use, the services we deploy, or applicable legal requirements. The date at the top of this page indicates when the policy was last revised. We encourage you to review this policy periodically.</p>

                <div class="mt-5 p-4 rounded" style="background: var(--light-bg); border-left: 4px solid var(--primary);">
                    <h5 class="mb-2">Questions About Cookies?</h5>
                    <p class="mb-0">If you have any questions about our use of cookies, please <a href="/contact-us">contact us</a>. We are happy to provide further information about our cookie practices.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.table-bordered { border-color: var(--soft-grey); }
.table-bordered th, .table-bordered td { border-color: var(--soft-grey); padding: 10px 14px; vertical-align: middle; }
.table code { background: var(--light-bg); padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; color: var(--primary); }
</style>
