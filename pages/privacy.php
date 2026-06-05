<?php
$slug = 'privacy-policy';
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
$title = $pageData['title'] ?? 'Privacy Policy';
$last_updated = '28 May 2026';
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Privacy Policy</h1>
        <p>How SWAMITIME SOLUTIONS LTD collects, uses, and protects your personal data.</p>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Privacy Policy Content -->
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <p class="text-muted mb-4"><em>Last updated: <?php echo $last_updated; ?></em></p>

                <p>SWAMITIME SOLUTIONS LTD (&ldquo;we,&rdquo; &ldquo;us,&rdquo; or &ldquo;our&rdquo;) is committed to protecting and respecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your personal data when you visit our website, engage with our services, or otherwise interact with us. It also explains your rights under the UK General Data Protection Regulation (UK GDPR) and the Data Protection Act 2018.</p>
                <p>Please read this policy carefully to understand our practices regarding your personal data. By using our website or services, you acknowledge that you have read and understood this policy.</p>

                <h3 class="mt-5 mb-3">1. Information We Collect</h3>
                <p>We may collect and process the following categories of personal data:</p>
                <ul>
                    <li><strong>Identity and Contact Data:</strong> Name, email address, phone number, company name, and job title, provided when you complete a contact form, subscribe to communications, or engage our services.</li>
                    <li><strong>Communication Data:</strong> Information contained in correspondence with us, including emails, contact form submissions, and meeting notes.</li>
                    <li><strong>Technical Data:</strong> Internet Protocol (IP) address, browser type and version, time zone setting, browser plug-in types, operating system, and other technology on the devices you use to access our website.</li>
                    <li><strong>Usage Data:</strong> Information about how you use our website, including pages visited, time spent, and navigation patterns.</li>
                    <li><strong>Marketing and Communications Data:</strong> Your preferences in receiving marketing communications from us.</li>
                </ul>
                <p>We do not collect any special categories of personal data (such as information about race, ethnicity, health, or biometric data) through our website.</p>

                <h3 class="mt-5 mb-3">2. How We Use Your Information</h3>
                <p>We use your personal data for the following purposes:</p>
                <ul>
                    <li><strong>Service Delivery:</strong> To respond to your enquiries, provide the services you have requested, and manage our relationship with you.</li>
                    <li><strong>Business Operations:</strong> To administer and protect our business and website, including troubleshooting, data analysis, and system maintenance.</li>
                    <li><strong>Marketing:</strong> To send you information about our services, industry insights, and relevant updates where you have consented to receive such communications. You may opt out at any time.</li>
                    <li><strong>Legal Compliance:</strong> To comply with applicable legal obligations, including those relating to tax, accounting, and regulatory requirements.</li>
                    <li><strong>Website Improvement:</strong> To analyse how our website is used and improve its functionality and user experience.</li>
                </ul>

                <h3 class="mt-5 mb-3">3. Legal Basis for Processing</h3>
                <p>Under the UK GDPR, we rely on the following lawful bases for processing your personal data:</p>
                <ul>
                    <li><strong>Consent:</strong> Where you have given clear consent for us to process your personal data for a specific purpose (e.g., receiving marketing communications).</li>
                    <li><strong>Contractual Necessity:</strong> Where processing is necessary for the performance of a contract with you or to take steps at your request before entering into a contract.</li>
                    <li><strong>Legitimate Interests:</strong> Where processing is necessary for our legitimate business interests or those of a third party, provided your interests and fundamental rights do not override those interests. This includes responding to enquiries, improving our services, and direct marketing to business contacts.</li>
                    <li><strong>Legal Obligation:</strong> Where processing is necessary for compliance with a legal obligation to which we are subject.</li>
                </ul>

                <h3 class="mt-5 mb-3">4. Data Retention</h3>
                <p>We retain personal data only for as long as is necessary to fulfil the purposes for which it was collected, including for the purposes of satisfying any legal, accounting, or reporting requirements. The criteria used to determine retention periods include:</p>
                <ul>
                    <li>The nature and sensitivity of the personal data</li>
                    <li>The potential risk of harm from unauthorised use or disclosure</li>
                    <li>The purposes for which we process the data and whether we can achieve those purposes through other means</li>
                    <li>Applicable legal requirements (e.g., HMRC requires certain financial records to be retained for six years)</li>
                </ul>
                <p>Enquiry data submitted through our contact form is typically retained for 24 months from the date of last correspondence, after which it is securely deleted unless an ongoing client relationship exists. Marketing consent records are retained indefinitely unless you withdraw your consent.</p>

                <h3 class="mt-5 mb-3">5. Your Rights</h3>
                <p>Under the UK GDPR, you have the following rights in relation to your personal data:</p>
                <ul>
                    <li><strong>Right to Be Informed:</strong> You have the right to be informed about the collection and use of your personal data (this policy serves that purpose).</li>
                    <li><strong>Right of Access:</strong> You have the right to request a copy of the personal data we hold about you.</li>
                    <li><strong>Right to Rectification:</strong> You have the right to request that inaccurate or incomplete personal data be corrected.</li>
                    <li><strong>Right to Erasure:</strong> You have the right to request that your personal data be deleted in certain circumstances (also known as the &ldquo;right to be forgotten&rdquo;).</li>
                    <li><strong>Right to Restrict Processing:</strong> You have the right to request that we limit the processing of your personal data in certain circumstances.</li>
                    <li><strong>Right to Data Portability:</strong> You have the right to receive your personal data in a structured, commonly used, machine-readable format and have it transferred to another controller.</li>
                    <li><strong>Right to Object:</strong> You have the right to object to processing based on legitimate interests or direct marketing.</li>
                    <li><strong>Rights in Relation to Automated Decision-Making:</strong> We do not engage in automated decision-making or profiling.</li>
                </ul>
                <p>To exercise any of these rights, please contact us using the details in Section 8. We will respond to your request within one calendar month. There is no fee for exercising your rights unless your request is manifestly unfounded or excessive.</p>

                <h3 class="mt-5 mb-3">6. Cookies</h3>
                <p>Our website uses cookies and similar technologies to enhance your browsing experience, analyse site traffic, and understand where our visitors come from. For detailed information about the cookies we use, please refer to our <a href="/cookie-policy">Cookie Policy</a>.</p>
                <p>When you first visit our website, you will be presented with a cookie consent banner that allows you to accept or manage your cookie preferences. You can change your preferences at any time by visiting our Cookie Policy page.</p>

                <h3 class="mt-5 mb-3">7. Third-Party Services</h3>
                <p>We may use third-party service providers to assist with website hosting, analytics, email communications, and other business operations. These providers may have access to your personal data only to perform specific tasks on our behalf and are contractually obligated to protect your data and not use it for any other purpose.</p>
                <p>Our website may include links to third-party websites, plug-ins, and applications. Clicking on those links may allow third parties to collect or share data about you. We do not control these third-party websites and are not responsible for their privacy practices. We encourage you to read the privacy policy of every website you visit.</p>

                <h3 class="mt-5 mb-3">8. Contact Information</h3>
                <p>SWAMITIME SOLUTIONS LTD is the data controller responsible for your personal data. If you have any questions about this Privacy Policy, wish to exercise your data protection rights, or would like to raise a concern about our data practices, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:hello@swamitime.com">hello@swamitime.com</a></li>
                    <li><strong>Phone:</strong> +44 (0) 000 000 0000</li>
                    <li><strong>Post:</strong> SWAMITIME SOLUTIONS LTD, London, United Kingdom</li>
                </ul>
                <p>You have the right to lodge a complaint with the Information Commissioner&rsquo;s Office (ICO), the UK supervisory authority for data protection issues (<a href="https://ico.org.uk" target="_blank" rel="noopener noreferrer">www.ico.org.uk</a>). We would, however, appreciate the opportunity to address your concerns before you approach the ICO, so please contact us in the first instance.</p>

                <h3 class="mt-5 mb-3">9. Updates to This Policy</h3>
                <p>We may update this Privacy Policy from time to time to reflect changes in our practices, legal requirements, or operational needs. The date at the top of this page indicates when the policy was last revised. We encourage you to review this policy periodically to stay informed about how we protect your personal data. Where changes are material, we will take reasonable steps to notify you, such as by posting a prominent notice on our website.</p>

                <div class="mt-5 p-4 rounded" style="background: var(--light-bg); border-left: 4px solid var(--primary);">
                    <h5 class="mb-2">Questions About This Policy?</h5>
                    <p class="mb-0">If you have any questions or concerns about our Privacy Policy or data practices, please do not hesitate to <a href="/contact-us">contact us</a>. We are committed to transparency and will respond promptly to your enquiry.</p>
                </div>
            </div>
        </div>
    </div>
</section>
