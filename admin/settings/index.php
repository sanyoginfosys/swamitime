<?php
if (!defined('ADMIN_SETTINGS')) {
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/settings.php' . $qs);
    exit;
}
$pageTitle = 'Site Settings';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();

    $settings = [
        'site_name' => sanitize($_POST['site_name'] ?? ''),
        'site_tagline' => sanitize($_POST['site_tagline'] ?? ''),
        'site_description' => sanitize($_POST['site_description'] ?? ''),
        'site_url' => sanitize($_POST['site_url'] ?? ''),
        'site_email' => sanitize($_POST['site_email'] ?? ''),
        'site_phone' => sanitize($_POST['site_phone'] ?? ''),
        'site_address' => sanitize($_POST['site_address'] ?? ''),
        'working_hours' => sanitize($_POST['working_hours'] ?? ''),
        'social_facebook' => sanitize($_POST['social_facebook'] ?? ''),
        'social_linkedin' => sanitize($_POST['social_linkedin'] ?? ''),
        'social_twitter' => sanitize($_POST['social_twitter'] ?? ''),
        'footer_disclaimer' => $_POST['footer_disclaimer'] ?? '',
        'cookie_consent_text' => $_POST['cookie_consent_text'] ?? '',
        'hp_section_hero'       => $_POST['hp_section_hero'] ?? '0',
        'hp_section_metrics'    => $_POST['hp_section_metrics'] ?? '0',
        'hp_section_services'   => $_POST['hp_section_services'] ?? '0',
        'hp_section_workforce'  => $_POST['hp_section_workforce'] ?? '0',
        'hp_section_ai'         => $_POST['hp_section_ai'] ?? '0',
        'hp_section_process'    => $_POST['hp_section_process'] ?? '0',
        'hp_section_industries' => $_POST['hp_section_industries'] ?? '0',
        'hp_section_case_studies' => $_POST['hp_section_case_studies'] ?? '0',
        'hp_section_why_choose' => $_POST['hp_section_why_choose'] ?? '0',
        'hp_section_blog'       => $_POST['hp_section_blog'] ?? '0',
        'hp_section_cta'        => $_POST['hp_section_cta'] ?? '0',
        'hp_section_faq'        => $_POST['hp_section_faq'] ?? '0',
    ];

    $logoPath = sanitize($_POST['existing_logo'] ?? '');
    $logoUploaded = false;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload = upload_file($_FILES['logo'], 'uploads', ['jpg','jpeg','png','gif','webp','svg','ico'], MAX_UPLOAD_SIZE);
        if ($upload['success']) { $logoPath = $upload['path']; $logoUploaded = true; }
    }
    if ($logoUploaded) $settings['logo_path'] = $logoPath;

    $faviconPath = sanitize($_POST['existing_favicon'] ?? '');
    $faviconUploaded = false;
    if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
        $upload = upload_file($_FILES['favicon'], 'uploads', ['jpg','jpeg','png','ico','svg'], MAX_UPLOAD_SIZE);
        if ($upload['success']) { $faviconPath = $upload['path']; $faviconUploaded = true; }
    }
    if ($faviconUploaded) $settings['favicon_path'] = $faviconPath;

    $recaptchaSite = sanitize($_POST['recaptcha_site_key'] ?? '');
    $recaptchaSecret = sanitize($_POST['recaptcha_secret_key'] ?? '');
    $settings['recaptcha_site_key'] = $recaptchaSite;
    $settings['recaptcha_secret_key'] = $recaptchaSecret;

    // Detect which form was submitted
    $isHomepageForm = false;
    foreach ($settings as $key => $value) {
        if (str_starts_with($key, 'hp_section_') && array_key_exists($key, $_POST)) {
            $isHomepageForm = true;
            break;
        }
    }

    foreach ($settings as $key => $value) {
        // Only save settings that were actually submitted in this form
        $isFile = in_array($key, ['logo_path', 'favicon_path'], true);
        $isPost  = array_key_exists($key, $_POST);
        $isHp    = $isHomepageForm && str_starts_with($key, 'hp_section_');
        if (!$isFile && !$isPost && !$isHp) continue;

        $existing = $db->prepare("SELECT id FROM site_settings WHERE setting_key=?");
        $existing->execute([$key]);
        if ($existing->fetch()) {
            $db->prepare("UPDATE site_settings SET setting_value=?, updated_at=NOW() WHERE setting_key=?")->execute([$value, $key]);
        } else {
            $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)")->execute([$key, $value]);
        }
    }

    set_flash('success', 'Site settings saved successfully.');
    redirect(admin_url('settings.php'));
}

$settings = [];
$rows = $db->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll();
foreach ($rows as $row) $settings[$row['setting_key']] = $row['setting_value'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-9">
        <div class="card form-card mb-4">
            <div class="card-header"><i class="bi bi-gear me-2"></i>General Settings</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php echo Security::csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Site Name</label>
                            <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Site URL</label>
                            <input type="text" name="site_url" class="form-control" value="<?php echo htmlspecialchars($settings['site_url'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="site_tagline" class="form-control" value="<?php echo htmlspecialchars($settings['site_tagline'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="site_description" class="form-control" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="site_email" class="form-control" value="<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="site_phone" class="form-control" value="<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea name="site_address" class="form-control" rows="2"><?php echo htmlspecialchars($settings['site_address'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Working Hours</label>
                            <input type="text" name="working_hours" class="form-control" value="<?php echo htmlspecialchars($settings['working_hours'] ?? ''); ?>">
                        </div>
                    </div>
                    <h6 class="mt-4 fw-bold">Social Media Links</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Facebook</label>
                            <input type="text" name="social_facebook" class="form-control" value="<?php echo htmlspecialchars($settings['social_facebook'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">LinkedIn</label>
                            <input type="text" name="social_linkedin" class="form-control" value="<?php echo htmlspecialchars($settings['social_linkedin'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Twitter / X</label>
                            <input type="text" name="social_twitter" class="form-control" value="<?php echo htmlspecialchars($settings['social_twitter'] ?? ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-teal mt-3"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
                </form>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header"><i class="bi bi-file-text me-2"></i>Legal & Content</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php echo Security::csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Footer Disclaimer</label>
                        <textarea name="footer_disclaimer" class="form-control" rows="5"><?php echo htmlspecialchars($settings['footer_disclaimer'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cookie Consent Text</label>
                        <textarea name="cookie_consent_text" class="form-control" rows="3"><?php echo htmlspecialchars($settings['cookie_consent_text'] ?? ''); ?></textarea>
                    </div>
                    <h6 class="fw-bold">Google reCAPTCHA</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Site Key</label>
                            <input type="text" name="recaptcha_site_key" class="form-control" value="<?php echo htmlspecialchars($settings['recaptcha_site_key'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secret Key</label>
                            <input type="text" name="recaptcha_secret_key" class="form-control" value="<?php echo htmlspecialchars($settings['recaptcha_secret_key'] ?? ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-teal mt-3"><i class="bi bi-check-lg me-1"></i>Save</button>
                </form>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header"><i class="bi bi-toggles me-2"></i>Homepage Sections</div>
            <div class="card-body">
                <p class="text-muted small mb-3">Toggle which sections appear on the homepage. Unchecked sections are hidden.</p>
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <div class="row g-3">
                        <?php
                        $sections = [
                            'hp_section_hero'        => '🦸 Hero Banner',
                            'hp_section_metrics'     => '📊 Trust Metrics',
                            'hp_section_services'    => '🛠️ Services',
                            'hp_section_workforce'   => '👥 Workforce Solutions',
                            'hp_section_ai'          => '🤖 AI Workforce Intelligence',
                            'hp_section_process'     => '📋 Delivery Process',
                            'hp_section_industries'  => '🏭 Industries',
                            'hp_section_case_studies' => '⭐ Case Studies',
                            'hp_section_why_choose'  => '✅ Why Choose SWAMITIME',
                            'hp_section_blog'        => '📝 Blog & Insights',
                            'hp_section_cta'         => '📢 Call to Action',
                            'hp_section_faq'         => '❓ FAQ',
                        ];
                        foreach ($sections as $key => $label):
                            $checked = ($settings[$key] ?? '1') === '1';
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="<?php echo $key; ?>" value="1" id="<?php echo $key; ?>" <?php echo $checked ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="<?php echo $key; ?>"><?php echo $label; ?></label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-teal mt-3"><i class="bi bi-check-lg me-1"></i>Save Sections</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card form-card mb-4">
            <div class="card-header">Logo</div>
            <div class="card-body">
                <?php $logo = $settings['logo_path'] ?? ''; ?>
                <?php if ($logo): ?>
                <img src="<?php echo BASE_URL . '/' . ltrim($logo, '/'); ?>" class="img-fluid rounded mb-2" alt="Logo">
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="existing_logo" value="<?php echo htmlspecialchars($logo); ?>">
                    <input type="file" name="logo" class="form-control form-control-sm mb-2">
                    <button type="submit" class="btn btn-sm btn-teal-outline w-100">Upload Logo</button>
                </form>
            </div>
        </div>
        <div class="card form-card mb-4">
            <div class="card-header">Favicon</div>
            <div class="card-body">
                <?php $fav = $settings['favicon_path'] ?? ''; ?>
                <?php if ($fav): ?>
                <img src="<?php echo BASE_URL . '/' . ltrim($fav, '/'); ?>" class="rounded mb-2" style="width:32px;height:32px;" alt="Favicon">
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="existing_favicon" value="<?php echo htmlspecialchars($fav); ?>">
                    <input type="file" name="favicon" class="form-control form-control-sm mb-2">
                    <button type="submit" class="btn btn-sm btn-teal-outline w-100">Upload Favicon</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
