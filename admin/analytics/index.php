<?php
$pageTitle = 'Analytics & Tracking';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $ga = sanitize($_POST['google_analytics_id'] ?? '');
    $gtm = sanitize($_POST['google_tag_manager_id'] ?? '');
    $gsc = sanitize($_POST['google_search_console_meta'] ?? '');
    $pixel = sanitize($_POST['meta_pixel_id'] ?? '');
    $linkedin = sanitize($_POST['linkedin_insight_tag'] ?? '');
    $headerScripts = $_POST['custom_header_scripts'] ?? '';
    $footerScripts = $_POST['custom_footer_scripts'] ?? '';

    $existing = $db->query("SELECT id FROM analytics_settings WHERE id=1")->fetchColumn();
    if ($existing) {
        $db->prepare("UPDATE analytics_settings SET google_analytics_id=?, google_tag_manager_id=?, google_search_console_meta=?, meta_pixel_id=?, linkedin_insight_tag=?, custom_header_scripts=?, custom_footer_scripts=?, updated_at=NOW() WHERE id=1")
            ->execute([$ga, $gtm, $gsc, $pixel, $linkedin, $headerScripts, $footerScripts]);
    } else {
        $db->prepare("INSERT INTO analytics_settings (id, google_analytics_id, google_tag_manager_id, google_search_console_meta, meta_pixel_id, linkedin_insight_tag, custom_header_scripts, custom_footer_scripts, created_at) VALUES (1, ?, ?, ?, ?, ?, ?, ?, NOW())")
            ->execute([$ga, $gtm, $gsc, $pixel, $linkedin, $headerScripts, $footerScripts]);
    }
    set_flash('success', 'Analytics settings saved.');
    redirect(admin_url('analytics.php'));
}

$settings = $db->query("SELECT * FROM analytics_settings WHERE id=1")->fetch();

$enquiryCount = $db->query("SELECT COUNT(*) FROM enquiries")->fetchColumn();
$leadCount = $db->query("SELECT COUNT(*) FROM crm_leads")->fetchColumn();
$wonCount = $db->query("SELECT COUNT(*) FROM crm_leads WHERE lead_status='won'")->fetchColumn();
$conversionRate = $leadCount > 0 ? round(($wonCount / $leadCount) * 100, 1) : 0;

$pageViewCount = 0;
if (Database::isFallback()) {
    try { $pageViewCount = $db->query("SELECT COUNT(*) FROM analytics_data")->fetchColumn(); } catch (Exception $e) { $pageViewCount = 0; }
} else {
    if ($db->query("SHOW TABLES LIKE 'analytics_data'")->rowCount() > 0) {
        $pageViewCount = $db->query("SELECT COUNT(*) FROM analytics_data")->fetchColumn();
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Visitors', $pageViewCount, 'bi-people', 'bg-teal-light'],
        ['Page Views', $pageViewCount, 'bi-eye', 'bg-teal-light'],
        ['Enquiries', $enquiryCount, 'bi-envelope', 'bg-teal-light'],
        ['Lead Conv. Rate', $conversionRate . '%', 'bi-graph-up-arrow', 'bg-teal-light'],
    ];
    foreach ($cards as $c):
    ?>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value"><?php echo $c[1]; ?></div>
                    <div class="stat-label"><?php echo $c[0]; ?></div>
                </div>
                <div class="stat-icon <?php echo $c[2]; ?>">
                    <i class="bi <?php echo $c[2]; ?>"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card form-card mb-4">
            <div class="card-header"><i class="bi bi-bar-chart-line me-2"></i>Tracking Configuration</div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Google Analytics Measurement ID</label>
                            <input type="text" name="google_analytics_id" class="form-control" placeholder="G-XXXXXXXXXX" value="<?php echo htmlspecialchars($settings['google_analytics_id'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Google Tag Manager Container ID</label>
                            <input type="text" name="google_tag_manager_id" class="form-control" placeholder="GTM-XXXXXXX" value="<?php echo htmlspecialchars($settings['google_tag_manager_id'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Google Search Console Meta Tag</label>
                            <input type="text" name="google_search_console_meta" class="form-control" placeholder="<meta name=&quot;google-site-verification&quot; content=&quot;...&quot;>" value="<?php echo htmlspecialchars($settings['google_search_console_meta'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Pixel ID</label>
                            <input type="text" name="meta_pixel_id" class="form-control" placeholder="1234567890" value="<?php echo htmlspecialchars($settings['meta_pixel_id'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LinkedIn Insight Tag ID</label>
                            <input type="text" name="linkedin_insight_tag" class="form-control" placeholder="1234567" value="<?php echo htmlspecialchars($settings['linkedin_insight_tag'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-teal"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header"><i class="bi bi-code-slash me-2"></i>Custom Scripts</div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Custom Header Scripts <small class="text-muted">(injected before &lt;/head&gt;)</small></label>
                        <textarea name="custom_header_scripts" class="form-control" rows="5" style="font-family:monospace;font-size:0.8rem;"><?php echo htmlspecialchars($settings['custom_header_scripts'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Custom Footer Scripts <small class="text-muted">(injected before &lt;/body&gt;)</small></label>
                        <textarea name="custom_footer_scripts" class="form-control" rows="5" style="font-family:monospace;font-size:0.8rem;"><?php echo htmlspecialchars($settings['custom_footer_scripts'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-teal"><i class="bi bi-check-lg me-1"></i>Save Scripts</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card form-card mb-4">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Tracking Status</div>
            <div class="card-body">
                <?php
                $checks = [
                    ['Google Analytics', $settings['google_analytics_id'] ?? ''],
                    ['Google Tag Manager', $settings['google_tag_manager_id'] ?? ''],
                    ['Search Console', $settings['google_search_console_meta'] ?? ''],
                    ['Meta Pixel', $settings['meta_pixel_id'] ?? ''],
                    ['LinkedIn Insight', $settings['linkedin_insight_tag'] ?? ''],
                ];
                foreach ($checks as $chk):
                    $active = !empty(trim($chk[1]));
                ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small"><?php echo $chk[0]; ?></span>
                    <span class="badge <?php echo $active ? 'bg-success' : 'bg-secondary'; ?>">
                        <?php echo $active ? 'Configured' : 'Not Set'; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header"><i class="bi bi-graph-up me-2"></i>Quick Stats</div>
            <div class="card-body">
                <div class="mb-2"><small class="text-muted">Contact Form Conversions</small><br><span class="fw-bold fs-5"><?php echo $enquiryCount; ?></span></div>
                <div class="mb-2"><small class="text-muted">Lead Conversion Rate</small><br><span class="fw-bold fs-5"><?php echo $conversionRate; ?>%</span></div>
                <div class="mb-2"><small class="text-muted">Total Leads Won</small><br><span class="fw-bold fs-5"><?php echo $wonCount; ?></span></div>
                <p class="small text-muted mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Connect Google Analytics API to populate real-time data.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
