<?php
// Generic page template - renders content from pages table
$slug = trim($request_uri, '/');
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}

if (!$pageData) {
    http_response_code(404);
    include_once __DIR__ . '/404.php';
    return;
}

$title = $pageData['title'] ?? 'Page';
$content = $pageData['content'] ?? '';
$metaTitle = $pageData['meta_title'] ?: $title;
$metaDesc = $pageData['meta_description'] ?? '';

// Render page
?>
<section class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($title); ?></h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="content-section">
            <?php if ($content): ?>
                <?php echo $content; ?>
            <?php else: ?>
                <p class="text-muted text-center py-5">Content is being prepared. Please check back soon.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// CTA unless page has hide_cta set
if (empty($pageData['hide_cta'])):
?>
<section class="cta-section">
    <div class="container">
        <h2>Ready to Get Started?</h2>
        <p>Contact us today to discuss how we can support your business goals.</p>
        <a href="/contact-us" class="btn-white pulse-glow">
            <i class="bi bi-calendar-check"></i> Book a Free Consultation
        </a>
    </div>
</section>
<?php endif; ?>
