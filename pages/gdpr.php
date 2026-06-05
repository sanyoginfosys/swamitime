<?php
$slug = 'gdpr-compliance';
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
$title = $pageData['title'] ?? 'GDPR Compliance';
$last_updated = '01 June 2026';
?>

<div class="page-content">
    <!-- Page Header / Hero -->
    <section class="page-header bg-gradient-teal text-white py-5" data-aos="fade-down">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-3">
                            <li class="breadcrumb-item"><a href="/" class="text-white-50 text-decoration-none">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">GDPR Compliance</li>
                        </ol>
                    </nav>
                    <h1 class="display-5 fw-bold mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">GDPR Compliance</h1>
                    <p class="lead mb-0 opacity-75">How SWAMITIME SOLUTIONS LTD complies with UK and EU data protection regulations.</p>
                </div>
            </div>
        </div>
    </section>

    <style>
        .bg-gradient-teal {
            background: linear-gradient(135deg, #004E53 0%, #078E91 100%);
        }
        .section-py {
            padding: 3.5rem 0;
        }
        .content-section h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 1.75rem;
            color: #004E53;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #E6F4F4;
        }
        .content-section h3 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            font-size: 1.25rem;
            color: #078E91;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .content-section p, .content-section li {
            line-height: 1.8;
            color: #333;
        }
        .content-section a {
            color: #078E91;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .content-section a:hover {
            color: #004E53;
        }
        .lawful-basis-table th {
            background: #004E53;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .lawful-basis-table td {
            vertical-align: middle;
        }
        .lawful-basis-table .badge-used {
            background: #078E91;
            color: #fff;
            font-size: 0.75rem;
        }
        .lawful-basis-table .badge-na {
            background: #e9ecef;
            color: #6c757d;
            font-size: 0.75rem;
        }
        .gdpr-rights-list {
            counter-reset: gdpr-rights;
            list-style: none;
            padding-left: 0;
        }
        .gdpr-rights-list li {
            counter-increment: gdpr-rights;
            position: relative;
            padding-left: 3rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #E6F4F4;
        }
        .gdpr-rights-list li::before {
            content: counter(gdpr-rights);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            background: #004E53;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .gdpr-rights-list li strong {
            color: #004E53;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.05rem;
        }
        .cta-section {
            background: #E6F4F4;
            border-radius: 12px;
            padding: 2.5rem;
            text-align: center;
        }
        .cta-section h3 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #004E53;
        }
        .btn-teal {
            background: #004E53;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-teal:hover {
            background: #078E91;
            color: #ffffff;
        }
        .last-updated-badge {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }
    </style>

    <!-- Section 1: Our Commitment -->
    <section class="section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>1. Our Commitment to Data Protection</h2>
                        <p>
                            At SWAMITIME SOLUTIONS LTD, we are dedicated to protecting the privacy and personal data of our clients, partners, and website visitors. We understand the importance of data protection in today's digital landscape and are fully committed to complying with the UK General Data Protection Regulation (UK GDPR), the Data Protection Act 2018, and where applicable, the EU General Data Protection Regulation (EU GDPR).
                        </p>
                        <p>
                            Data protection is not merely a legal obligation for us&mdash;it is a fundamental aspect of how we conduct business. We have implemented comprehensive technical and organisational measures to ensure that all personal data we process is handled lawfully, fairly, and transparently. Our commitment extends across every facet of our operations, from our workforce management consulting services to our digital solutions and website management.
                        </p>
                        <p>
                            We regularly review and update our data protection policies, procedures, and practices to ensure continued compliance with evolving regulatory requirements and industry best practices. All staff members receive appropriate training on data protection principles and their responsibilities under the GDPR.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Data Controller Information -->
    <section class="bg-light section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>2. Data Controller Information</h2>
                        <p>
                            For the purposes of the UK GDPR and the Data Protection Act 2018, the data controller responsible for your personal data is:
                        </p>
                        <div class="card border-0 shadow-sm mt-3 mb-3">
                            <div class="card-body bg-white p-4 rounded-3">
                                <p class="mb-1"><strong>Data Controller:</strong> SWAMITIME SOLUTIONS LTD</p>
                                <p class="mb-1"><strong>Company Registration:</strong> Registered in England and Wales</p>
                                <p class="mb-1"><strong>Registered Office:</strong> [Registered Office Address], London, United Kingdom</p>
                                <p class="mb-0"><strong>Email:</strong> <a href="mailto:privacy@swamitime.com">privacy@swamitime.com</a></p>
                            </div>
                        </div>
                        <p>
                            If you have any questions about this GDPR compliance page or how we handle your personal data, please contact us using the details above. We take all data protection enquiries seriously and will respond promptly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Lawful Basis for Processing -->
    <section class="section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>3. Lawful Basis for Processing</h2>
                        <p>
                            Under the UK GDPR, all processing of personal data must have a valid lawful basis. There are six lawful bases for processing, and SWAMITIME SOLUTIONS LTD relies on the following three, depending on the nature and purpose of the processing activity:
                        </p>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered lawful-basis-table">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 22%;">Lawful Basis</th>
                                        <th scope="col" style="width: 48%;">Description</th>
                                        <th scope="col" style="width: 15%;">Used by SWAMITIME</th>
                                        <th scope="col" style="width: 15%;">Example</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Consent</strong></td>
                                        <td>The individual has given clear consent for us to process their personal data for a specific purpose.</td>
                                        <td class="text-center"><span class="badge badge-used">Yes</span></td>
                                        <td class="small">Newsletter subscriptions, cookie preferences, marketing communications</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Contract</strong></td>
                                        <td>The processing is necessary for a contract we have with the individual, or because they have asked us to take specific steps before entering into a contract.</td>
                                        <td class="text-center"><span class="badge badge-used">Yes</span></td>
                                        <td class="small">Client engagement agreements, service delivery, project scoping</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Legal Obligation</strong></td>
                                        <td>The processing is necessary for us to comply with the law (not including contractual obligations).</td>
                                        <td class="text-center"><span class="badge badge-na">Not Primary</span></td>
                                        <td class="small">Financial record-keeping, regulatory reporting</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Vital Interests</strong></td>
                                        <td>The processing is necessary to protect someone's life.</td>
                                        <td class="text-center"><span class="badge badge-na">N/A</span></td>
                                        <td class="small">Not applicable to our business operations</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Public Task</strong></td>
                                        <td>The processing is necessary for us to perform a task in the public interest or for official functions, with a clear basis in law.</td>
                                        <td class="text-center"><span class="badge badge-na">N/A</span></td>
                                        <td class="small">Not applicable to our business operations</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Legitimate Interests</strong></td>
                                        <td>The processing is necessary for our legitimate interests or the legitimate interests of a third party, unless there is a good reason to protect the individual's personal data that overrides those legitimate interests.</td>
                                        <td class="text-center"><span class="badge badge-used">Yes</span></td>
                                        <td class="small">Business development, service improvements, website analytics</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p class="mt-3">
                            We carefully assess each processing activity to identify the appropriate lawful basis and document our justification. Where we rely on legitimate interests, we conduct a Legitimate Interests Assessment (LIA) to balance our interests against the rights and freedoms of the data subject.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Your Rights Under GDPR -->
    <section class="bg-light section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>4. Your Rights Under GDPR</h2>
                        <p>
                            The UK GDPR provides individuals with eight fundamental data subject rights. We are committed to upholding these rights and ensuring you can exercise them easily and effectively.
                        </p>

                        <ol class="gdpr-rights-list mt-4">
                            <li>
                                <strong>The Right to be Informed</strong>
                                <p class="mb-0 mt-1">
                                    You have the right to be informed about the collection and use of your personal data. We provide this information through our Privacy Policy, this GDPR Compliance page, and at the point of data collection. We ensure that our privacy notices are concise, transparent, intelligible, and easily accessible.
                                </p>
                            </li>
                            <li>
                                <strong>The Right of Access (Subject Access Request)</strong>
                                <p class="mb-0 mt-1">
                                    You have the right to obtain confirmation that your data is being processed and to access your personal data. You can request a copy of the personal data we hold about you, along with supplementary information about how we process it. This is commonly known as a Subject Access Request (SAR). See section 6 below for details on how to submit a SAR.
                                </p>
                            </li>
                            <li>
                                <strong>The Right to Rectification</strong>
                                <p class="mb-0 mt-1">
                                    You have the right to have inaccurate personal data rectified, or completed if it is incomplete. If you believe any information we hold about you is incorrect or incomplete, please contact us and we will correct it promptly&mdash;within one month of your request.
                                </p>
                            </li>
                            <li>
                                <strong>The Right to Erasure (Right to be Forgotten)</strong>
                                <p class="mb-0 mt-1">
                                    You have the right to request the deletion or removal of your personal data in certain circumstances, such as when the data is no longer necessary for the purpose it was collected, you withdraw consent, or the data has been unlawfully processed. This right is not absolute and may be subject to legal or regulatory obligations that require us to retain certain data.
                                </p>
                            </li>
                            <li>
                                <strong>The Right to Restrict Processing</strong>
                                <p class="mb-0 mt-1">
                                    You have the right to request the restriction or suppression of your personal data in certain circumstances. When processing is restricted, we are permitted to store the data but not use it. You may exercise this right if you contest the accuracy of the data, the processing is unlawful, or you have objected to processing.
                                </p>
                            </li>
                            <li>
                                <strong>The Right to Data Portability</strong>
                                <p class="mb-0 mt-1">
                                    You have the right to obtain and reuse your personal data for your own purposes across different services. This allows you to receive personal data you have provided to us in a structured, commonly used, and machine-readable format (such as CSV or JSON), and to transmit that data to another data controller.
                                </p>
                            </li>
                            <li>
                                <strong>The Right to Object</strong>
                                <p class="mb-0 mt-1">
                                    You have the right to object to the processing of your personal data in certain circumstances, including processing for direct marketing purposes. If you object to processing for direct marketing, we will stop processing your data for this purpose immediately. For objections on other grounds, we will assess whether our legitimate grounds override your interests.
                                </p>
                            </li>
                            <li>
                                <strong>Rights Related to Automated Decision-Making and Profiling</strong>
                                <p class="mb-0 mt-1">
                                    You have the right not to be subject to a decision based solely on automated processing, including profiling, which produces legal effects concerning you or similarly significantly affects you. SWAMITIME SOLUTIONS LTD does not currently engage in any automated decision-making or profiling activities that produce legal or similarly significant effects.
                                </p>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: How to Exercise Your Rights -->
    <section class="section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>5. How to Exercise Your Rights</h2>
                        <p>
                            To exercise any of your data protection rights, please contact us using the details below. We aim to make the process as straightforward as possible.
                        </p>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-4">
                                <p class="mb-2"><strong><i class="bi bi-envelope-fill me-2" style="color: #078E91;"></i>Email:</strong> <a href="mailto:privacy@swamitime.com">privacy@swamitime.com</a></p>
                                <p class="mb-2"><strong><i class="bi bi-clock-fill me-2" style="color: #078E91;"></i>Response Timeframe:</strong> Within 30 calendar days (extendable by a further two months for complex requests, with notification).</p>
                                <p class="mb-0"><strong><i class="bi bi-info-circle-fill me-2" style="color: #078E91;"></i>Information to Provide:</strong> Full name, contact details, the specific right you wish to exercise, and any supporting details to help us identify and locate your data. We may ask for proof of identity before processing your request.</p>
                            </div>
                        </div>

                        <p>
                            We will not charge a fee to exercise your rights unless your request is manifestly unfounded or excessive, in which case we may charge a reasonable administrative fee or refuse to act on the request.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 6: Subject Access Request (SAR) -->
    <section class="bg-light section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>6. Subject Access Request (SAR)</h2>
                        <p>
                            A Subject Access Request (SAR) allows you to find out what personal data we hold about you, why we hold it, and who we disclose it to. You are entitled to receive a copy of your personal data along with supplementary information.
                        </p>

                        <h3>How to Submit a SAR</h3>
                        <p>To submit a Subject Access Request, please email us at <a href="mailto:privacy@swamitime.com">privacy@swamitime.com</a> with the subject line "Subject Access Request". To help us process your request efficiently, please include the following:</p>
                        <ul>
                            <li>Your full name and any previous names you may have used</li>
                            <li>Your contact details (email address and telephone number)</li>
                            <li>A description of the specific information you are requesting (this helps us narrow the scope)</li>
                            <li>Any relevant dates or timeframes (e.g. "data relating to my engagement as a client from January 2024")</li>
                            <li>Proof of identity (we may request a copy of photo ID such as a passport or driving licence)</li>
                        </ul>

                        <h3>What to Expect</h3>
                        <ul>
                            <li><strong>Acknowledgment:</strong> We will acknowledge receipt of your SAR within 5 working days.</li>
                            <li><strong>Response Time:</strong> We will respond to your SAR within 30 calendar days. If your request is complex or numerous, we may extend this by up to a further two months, and we will notify you of any extension within the initial 30-day period.</li>
                            <li><strong>Format:</strong> We will provide the information in a clear, commonly used electronic format unless you request otherwise.</li>
                            <li><strong>Refusal:</strong> If we refuse to comply with your request, we will explain why and inform you of your right to complain to the Information Commissioner's Office (ICO).</li>
                        </ul>

                        <h3>Third-Party Data</h3>
                        <p>
                            Where responding to a SAR would involve disclosing information relating to another individual (a third party), we will carefully consider whether it is reasonable to disclose that information without the third party's consent. We may redact third-party data or withhold it where appropriate.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 7: Data Retention -->
    <section class="section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>7. Data Retention</h2>
                        <p>
                            We retain personal data only for as long as is necessary to fulfil the purposes for which it was collected, or as required by applicable laws and regulations. Our data retention policy is based on the following criteria:
                        </p>
                        <ul>
                            <li><strong>Contractual Obligations:</strong> Data relating to client engagements is retained for the duration of the contract plus a period of 6 years following contract termination, in line with statutory limitation periods for contractual claims.</li>
                            <li><strong>Financial Records:</strong> In accordance with UK tax and accounting regulations, financial records are retained for a minimum of 6 years from the end of the last company financial year they relate to.</li>
                            <li><strong>Marketing Data:</strong> Personal data used for marketing purposes is retained until consent is withdrawn or the data subject objects. We review marketing consent periodically and refresh consent every 24 months.</li>
                            <li><strong>Website Analytics:</strong> Anonymised analytics data may be retained indefinitely for trend analysis. Personal identifiers are removed or pseudonymised where possible.</li>
                            <li><strong>Recruitment Data:</strong> CVs and application materials for unsuccessful candidates are retained for 12 months after the recruitment decision, unless the candidate consents to longer retention for future opportunities.</li>
                        </ul>
                        <p>
                            When personal data is no longer required, we securely delete or anonymise it in accordance with our data disposal procedures.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 8: Data Transfers -->
    <section class="bg-light section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>8. Data Transfers</h2>
                        <p>
                            SWAMITIME SOLUTIONS LTD primarily stores and processes personal data within the United Kingdom. Where possible, we ensure that all personal data remains within the UK or the European Economic Area (EEA).
                        </p>
                        <p>
                            In certain circumstances, we may need to transfer personal data to third-party service providers or partners located outside the UK or EEA. Where such transfers occur, we ensure that appropriate safeguards are in place in accordance with UK GDPR requirements, which may include:
                        </p>
                        <ul>
                            <li><strong>Adequacy Decisions:</strong> Transferring data to countries that have been deemed by the UK Government to provide an adequate level of data protection.</li>
                            <li><strong>Standard Contractual Clauses (SCCs):</strong> Using the UK International Data Transfer Agreement (IDTA) or the EU Standard Contractual Clauses, as applicable, to ensure contractual protections for transferred data.</li>
                            <li><strong>Binding Corporate Rules:</strong> Where applicable, relying on approved binding corporate rules for intra-group transfers.</li>
                        </ul>
                        <p>
                            We conduct Transfer Impact Assessments (TIAs) for any restricted transfers to assess the level of protection in the destination country and the effectiveness of the safeguards in place.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 9: Data Breach Procedures -->
    <section class="section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>9. Data Breach Procedures</h2>
                        <p>
                            We have robust procedures in place to detect, investigate, and respond to personal data breaches. A personal data breach is a breach of security leading to the accidental or unlawful destruction, loss, alteration, unauthorised disclosure of, or access to, personal data.
                        </p>
                        <ul>
                            <li><strong>ICO Notification:</strong> Where a personal data breach is likely to result in a risk to the rights and freedoms of individuals, we will notify the Information Commissioner's Office (ICO) without undue delay and, where feasible, within 72 hours of becoming aware of the breach.</li>
                            <li><strong>Data Subject Notification:</strong> Where a breach is likely to result in a high risk to the rights and freedoms of individuals, we will notify the affected data subjects without undue delay, describing the nature of the breach and providing recommendations to mitigate potential adverse effects.</li>
                            <li><strong>Internal Investigation:</strong> All breaches are logged in our internal breach register, and a full investigation is conducted to identify root causes and implement corrective measures to prevent recurrence.</li>
                            <li><strong>Staff Training:</strong> All employees receive training on identifying and reporting potential data breaches as part of their data protection awareness training.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 10: Data Protection Impact Assessments -->
    <section class="bg-light section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>10. Data Protection Impact Assessments (DPIAs)</h2>
                        <p>
                            We conduct Data Protection Impact Assessments (DPIAs) for any processing activity that is likely to result in a high risk to individuals' rights and freedoms. A DPIA is a systematic process that helps us identify and minimise the data protection risks of a project or processing activity.
                        </p>
                        <p>
                            We carry out a DPIA in the following circumstances:
                        </p>
                        <ul>
                            <li>When introducing new technologies or systems that process personal data</li>
                            <li>When processing that is likely to result in a high risk to individuals</li>
                            <li>When processing special category data or criminal offence data on a large scale</li>
                            <li>When systematically monitoring publicly accessible areas on a large scale</li>
                            <li>When engaging in profiling or automated decision-making with legal or significant effects</li>
                            <li>When combining, comparing, or matching personal data from multiple sources</li>
                        </ul>
                        <p>
                            If a DPIA identifies a high risk that cannot be adequately mitigated, we will consult with the ICO before proceeding with the processing activity.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 11: Third-Party Processors -->
    <section class="section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>11. Third-Party Processors</h2>
                        <p>
                            We may engage third-party service providers to process personal data on our behalf. These may include cloud hosting providers, email service providers, analytics platforms, customer relationship management (CRM) systems, and professional advisers.
                        </p>
                        <p>
                            Before engaging any third-party processor, we conduct thorough due diligence to ensure they:
                        </p>
                        <ul>
                            <li>Provide sufficient guarantees to implement appropriate technical and organisational measures to meet UK GDPR requirements</li>
                            <li>Process personal data only in accordance with our documented instructions</li>
                            <li>Have appropriate security measures in place to protect personal data</li>
                            <li>Do not engage sub-processors without our prior authorisation</li>
                            <li>Assist us in responding to data subject rights requests</li>
                            <li>Notify us of any personal data breaches without undue delay</li>
                            <li>Delete or return all personal data at the end of the contract</li>
                        </ul>
                        <p>
                            All third-party processor relationships are governed by a written contract that includes the mandatory data processing terms required under Article 28 of the UK GDPR. We maintain a register of all processors and sub-processors, which is reviewed and updated regularly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 12: Changes to This Policy -->
    <section class="bg-light section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>12. Changes to This Policy</h2>
                        <p>
                            We may update this GDPR Compliance page from time to time to reflect changes in our data processing practices, regulatory requirements, or for other operational, legal, or regulatory reasons. We encourage you to review this page periodically to stay informed about how we protect your personal data.
                        </p>
                        <p>
                            When we make material changes, we will:
                        </p>
                        <ul>
                            <li>Update the "last updated" date at the top of this page</li>
                            <li>Notify our active clients and subscribers of significant changes via email where appropriate</li>
                            <li>Post a prominent notice on our website for a reasonable period</li>
                        </ul>
                        <p>
                            This page was last updated on <strong><?php echo $last_updated; ?></strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 13: Supervisory Authority -->
    <section class="section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-section">
                        <h2>13. Supervisory Authority</h2>
                        <p>
                            If you believe that we have not adequately addressed your data protection concerns or have infringed your rights under data protection law, you have the right to lodge a complaint with the UK's supervisory authority, the Information Commissioner's Office (ICO).
                        </p>
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-4 bg-white">
                                <p class="mb-1"><strong>Information Commissioner's Office (ICO)</strong></p>
                                <p class="mb-1">Wycliffe House, Water Lane, Wilmslow, Cheshire, SK9 5AF</p>
                                <p class="mb-1">Telephone: 0303 123 1113</p>
                                <p class="mb-0">Website: <a href="https://ico.org.uk" target="_blank" rel="noopener noreferrer">www.ico.org.uk</a></p>
                            </div>
                        </div>
                        <p>
                            We would, however, appreciate the opportunity to address your concerns directly before you approach the ICO. Please contact us first so we can attempt to resolve the matter.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-py" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="cta-section">
                        <h3 class="mb-3">Have Questions About Your Data Rights?</h3>
                        <p class="mb-4">
                            If you have any questions about your data rights or wish to exercise any of your rights under the GDPR, please contact our data protection team.
                        </p>
                        <a href="mailto:privacy@swamitime.com" class="btn btn-teal btn-lg">
                            <i class="bi bi-envelope-fill me-2"></i>Contact Us: privacy@swamitime.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
