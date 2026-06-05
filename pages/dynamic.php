<?php
$page = $GLOBALS['current_page'] ?? null;

if (empty($page)) {
    $slug = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
        $stmt->execute([$slug ?: 'home']);
        $page = $stmt->fetch();
    } catch (Exception $e) {
        $page = null;
    }

    if (empty($page)) {
        http_response_code(404);
        include __DIR__ . '/404.php';
        return;
    }
}

$title = $page['title'] ?? 'Page';
$content = $page['content'] ?? '';
$meta_description = $page['meta_description'] ?? '';
$slug = $page['slug'] ?? '';
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php if (!empty($meta_description)): ?>
        <p><?php echo htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <?php if (!empty($page['parent_slug'])): ?>
                <li class="breadcrumb-item"><a href="/<?php echo htmlspecialchars($page['parent_slug'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $page['parent_slug'])), ENT_QUOTES, 'UTF-8'); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Dynamic Page Content -->
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div style="line-height: 1.8; color: var(--muted-text); font-size: 1.05rem;">
                    <?php if (!empty($content)): ?>
                        <?php echo $content; ?>
                    <?php else: ?>
                        <p>Content is being prepared. Please check back shortly or <a href="/contact-us">contact us</a> for more information.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Optional CTA section for dynamic pages that don't have their own -->
<?php if (empty($page['hide_cta'])): ?>
<section class="cta-section">
    <div class="container">
        <h2>Want to Learn More?</h2>
        <p>Get in touch to discuss how we can support your organisation with expert workforce management and digital solutions.</p>
        <a href="/contact-us" class="btn-white">Contact Us Today <i class="bi bi-arrow-right"></i></a>
        <div class="cta-trust">Free initial consultation &bull; No obligation &bull; Expert advice</div>
    </div>
</section>
<?php endif; ?>

<style>
.dynamic-content h2 { color: var(--dark-text); margin-top: 2rem; margin-bottom: 1rem; font-size: 1.5rem; }
.dynamic-content h3 { color: var(--dark-text); margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 1.2rem; }
.dynamic-content ul, .dynamic-content ol { margin-bottom: 1.25rem; }
.dynamic-content li { margin-bottom: 0.5rem; }
.dynamic-content strong { color: var(--dark-text); }
.dynamic-content a { color: var(--primary); }
.dynamic-content table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
.dynamic-content table th,
.dynamic-content table td { border: 1px solid var(--soft-grey); padding: 10px 14px; text-align: left; }
.dynamic-content table th { background: var(--light-bg); color: var(--dark-text); font-weight: 600; }
.dynamic-content blockquote { border-left: 4px solid var(--primary); padding: 1rem 1.5rem; margin: 1.5rem 0; background: var(--light-bg); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-style: italic; color: var(--dark-text); }
.dynamic-content img { max-width: 100%; height: auto; border-radius: var(--radius-md); margin: 1rem 0; }
.dynamic-content pre { background: var(--light-bg); padding: 1rem; border-radius: var(--radius-sm); overflow-x: auto; }
.dynamic-content code { background: var(--light-bg); padding: 2px 6px; border-radius: 4px; font-size: 0.9rem; color: var(--primary-dark); }
</style>
