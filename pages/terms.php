<?php
$slug = 'terms-conditions';
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
$title = $pageData['title'] ?? 'Terms & Conditions';
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Terms &amp; Conditions</h1>
        <p>The terms governing the use of our website and the provision of our consulting services.</p>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Terms &amp; Conditions</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Terms Content -->
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <p class="text-muted mb-4"><em>Last updated: 28 May 2026</em></p>

                <p>These Terms and Conditions (&ldquo;Terms&rdquo;) govern your use of the SWAMITIME SOLUTIONS LTD website and the provision of consulting services by SWAMITIME SOLUTIONS LTD (&ldquo;we,&rdquo; &ldquo;us,&rdquo; or &ldquo;our&rdquo;). By accessing our website or engaging our services, you agree to be bound by these Terms. If you do not agree, please refrain from using our website or services.</p>

                <h3 class="mt-5 mb-3">1. Definitions</h3>
                <p>In these Terms, the following definitions apply:</p>
                <ul>
                    <li><strong>&ldquo;Client&rdquo;</strong> means the person, firm, or company engaging our services.</li>
                    <li><strong>&ldquo;Services&rdquo;</strong> means the workforce management consulting, UKG system support, digital solutions, and related services provided by us.</li>
                    <li><strong>&ldquo;Engagement Letter&rdquo;</strong> means the written document (which may be in electronic form) setting out the specific scope, deliverables, timelines, and fees for a particular engagement.</li>
                    <li><strong>&ldquo;Deliverables&rdquo;</strong> means the outputs, reports, configurations, documentation, or other materials produced as part of the Services.</li>
                    <li><strong>&ldquo;Website&rdquo;</strong> means the website at www.swamitime.com and any subdomains.</li>
                </ul>

                <h3 class="mt-5 mb-3">2. Services</h3>
                <p>2.1 We provide independent workforce management and digital solutions consulting services. The specific scope, deliverables, timeline, and fees for each engagement will be set out in a separate Engagement Letter or Statement of Work agreed between us and the Client.</p>
                <p>2.2 In the event of any conflict between these Terms and the Engagement Letter, the Engagement Letter shall prevail with respect to the specific engagement.</p>
                <p>2.3 We reserve the right to decline to provide Services where we determine, in our reasonable discretion, that the proposed engagement falls outside our expertise or creates an unacceptable conflict of interest.</p>
                <p>2.4 Any timelines or delivery dates provided are estimates made in good faith. While we will use reasonable endeavours to meet them, time shall not be of the essence unless expressly agreed in writing.</p>

                <h3 class="mt-5 mb-3">3. Client Obligations</h3>
                <p>3.1 The Client shall provide us with timely access to such information, personnel, systems, and facilities as are reasonably necessary for us to perform the Services.</p>
                <p>3.2 The Client warrants that any information, data, or materials provided to us are accurate and complete to the best of the Client&rsquo;s knowledge and that the Client has the necessary rights and permissions to share such information with us.</p>
                <p>3.3 The Client is responsible for ensuring that its own personnel cooperate with us and comply with any reasonable instructions we provide in connection with the delivery of the Services.</p>
                <p>3.4 Delays caused by the Client&rsquo;s failure to meet its obligations under this clause may result in adjusted timelines and/or additional fees, which will be communicated to the Client in advance.</p>

                <h3 class="mt-5 mb-3">4. Fees and Payment</h3>
                <p>4.1 Fees for Services will be set out in the Engagement Letter. Unless otherwise specified, fees are exclusive of VAT and any other applicable taxes, which will be charged at the prevailing rate.</p>
                <p>4.2 Invoices will be issued in accordance with the payment schedule specified in the Engagement Letter. Payment is due within 30 calendar days of the invoice date unless otherwise agreed.</p>
                <p>4.3 We reserve the right to charge interest on overdue invoices at the rate of 4% per annum above the Bank of England base rate, calculated on a daily basis from the due date until payment is received in full. This is in addition to our right to recover reasonable debt collection costs under the Late Payment of Commercial Debts (Interest) Act 1998.</p>
                <p>4.4 We reserve the right to suspend the provision of Services if payment is not received within 45 days of the invoice due date, following written notice to the Client.</p>

                <h3 class="mt-5 mb-3">5. Intellectual Property</h3>
                <p>5.1 All intellectual property rights in the Deliverables created by us in the course of providing the Services shall, upon full payment of all fees due, be assigned to the Client, subject to clause 5.2.</p>
                <p>5.2 We retain ownership of all pre-existing intellectual property, methodologies, tools, templates, know-how, and frameworks used in the delivery of the Services (&ldquo;Background IP&rdquo;). We grant the Client a non-exclusive, perpetual, royalty-free licence to use any Background IP incorporated into the Deliverables solely for the Client&rsquo;s internal business purposes.</p>
                <p>5.3 The Client retains ownership of all data, materials, and intellectual property provided by the Client to us.</p>
                <p>5.4 All content on our Website, including text, graphics, logos, and images, is our property or the property of our licensors and is protected by copyright and other intellectual property laws. You may not reproduce, distribute, or create derivative works from Website content without our prior written consent.</p>

                <h3 class="mt-5 mb-3">6. Confidentiality</h3>
                <p>6.1 Each party undertakes to keep confidential all information (whether written or oral) concerning the business, affairs, systems, data, or personnel of the other party that is disclosed during the course of an engagement (&ldquo;Confidential Information&rdquo;).</p>
                <p>6.2 Neither party shall disclose the other&rsquo;s Confidential Information to any third party without the disclosing party&rsquo;s prior written consent, except as required by law or to professional advisers bound by duties of confidentiality.</p>
                <p>6.3 The obligations of confidentiality in this clause shall survive the termination of any engagement for a period of three years, or indefinitely in respect of information that constitutes a trade secret.</p>

                <h3 class="mt-5 mb-3">7. Limitation of Liability</h3>
                <p>7.1 Nothing in these Terms excludes or limits our liability for death or personal injury caused by our negligence, fraud or fraudulent misrepresentation, or any other liability that cannot be excluded or limited by English law.</p>
                <p>7.2 Subject to clause 7.1, our total aggregate liability to the Client in respect of any engagement, whether in contract, tort (including negligence), breach of statutory duty, or otherwise, shall be limited to the total fees paid by the Client under that engagement.</p>
                <p>7.3 We shall not be liable to the Client for any indirect, consequential, or special loss, including but not limited to loss of profits, loss of business, loss of revenue, loss of data, or loss of goodwill, whether foreseeable or not.</p>
                <p>7.4 We provide consulting advice based on information available at the time and our professional judgement. The Client acknowledges that business outcomes depend on many factors beyond our control, and we do not guarantee specific financial or operational results.</p>
                <p>7.5 The content on our Website is provided for general information purposes only. While we endeavour to keep information accurate and up to date, we make no representations or warranties of any kind about the completeness, accuracy, reliability, or suitability of the Website content.</p>

                <h3 class="mt-5 mb-3">8. Termination</h3>
                <p>8.1 Either party may terminate an engagement by giving 30 calendar days&rsquo; written notice to the other party, unless a different notice period is specified in the Engagement Letter.</p>
                <p>8.2 Either party may terminate an engagement with immediate effect by written notice if the other party:</p>
                <ul>
                    <li>Commits a material breach of these Terms or the Engagement Letter and, where the breach is capable of remedy, fails to remedy it within 14 days of receiving written notice of the breach; or</li>
                    <li>Becomes insolvent, enters into liquidation or administration, or has a receiver appointed over its assets.</li>
                </ul>
                <p>8.3 Upon termination, the Client shall pay all fees due for Services performed up to the date of termination, including any non-cancellable costs incurred by us. Any provisions of these Terms that by their nature should survive termination shall continue in full force and effect.</p>

                <h3 class="mt-5 mb-3">9. Governing Law and Jurisdiction</h3>
                <p>9.1 These Terms and any dispute or claim arising out of or in connection with them (including non-contractual disputes or claims) shall be governed by and construed in accordance with the laws of England and Wales.</p>
                <p>9.2 The parties irrevocably agree that the courts of England and Wales shall have exclusive jurisdiction to settle any dispute or claim arising out of or in connection with these Terms or any engagement.</p>

                <h3 class="mt-5 mb-3">10. Changes to These Terms</h3>
                <p>10.1 We may revise these Terms from time to time. The most current version will always be available on our Website. By continuing to use our Website after changes are posted, you agree to be bound by the revised Terms.</p>
                <p>10.2 For existing engagements, changes to these Terms will not apply retrospectively. Any new engagement entered into after the date of revision will be governed by the updated Terms unless otherwise agreed in writing.</p>

                <div class="mt-5 p-4 rounded" style="background: var(--light-bg); border-left: 4px solid var(--primary);">
                    <h5 class="mb-2">Questions About These Terms?</h5>
                    <p class="mb-0">If you have any questions about these Terms and Conditions, please <a href="/contact-us">contact us</a>. We are happy to discuss any aspect of these Terms before you engage our services.</p>
                </div>
            </div>
        </div>
    </div>
</section>
