<?php
if (!defined('ADMIN_SEO_SETTINGS')) {
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/seo-settings.php' . $qs);
    exit;
}
$pageTitle = 'SEO Settings';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'save_seo') {
        $pageType = sanitize($_POST['page_type'] ?? '');
        $pageId = (int)($_POST['page_id'] ?? 0);
        $metaTitle = sanitize($_POST['meta_title'] ?? '');
        $metaDescription = sanitize($_POST['meta_description'] ?? '');
        $metaKeywords = sanitize($_POST['meta_keywords'] ?? '');
        $ogTitle = sanitize($_POST['og_title'] ?? '');
        $ogDescription = sanitize($_POST['og_description'] ?? '');
        $ogImage = sanitize($_POST['og_image'] ?? '');
        $canonicalUrl = sanitize($_POST['canonical_url'] ?? '');
        $schemaMarkup = $_POST['schema_markup'] ?? '';

        $existing = $db->prepare("SELECT id FROM seo_settings WHERE page_type=? AND page_id=?");
        $existing->execute([$pageType, $pageId]);
        if ($existing->fetch()) {
            $db->prepare("UPDATE seo_settings SET meta_title=?,meta_description=?,meta_keywords=?,og_title=?,og_description=?,og_image=?,canonical_url=?,schema_markup=?,updated_at=NOW() WHERE page_type=? AND page_id=?")
                ->execute([$metaTitle,$metaDescription,$metaKeywords,$ogTitle,$ogDescription,$ogImage,$canonicalUrl,$schemaMarkup,$pageType,$pageId]);
        } else {
            $db->prepare("INSERT INTO seo_settings (page_type,page_id,meta_title,meta_description,meta_keywords,og_title,og_description,og_image,canonical_url,schema_markup) VALUES (?,?,?,?,?,?,?,?,?,?)")
                ->execute([$pageType,$pageId,$metaTitle,$metaDescription,$metaKeywords,$ogTitle,$ogDescription,$ogImage,$canonicalUrl,$schemaMarkup]);
        }
        set_flash('success', 'SEO settings saved.');
        redirect(admin_url('seo-settings.php'));
    }

    if ($action === 'generate_sitemap') {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $dom->appendChild($urlset);

        $sources = [
            ['query'=>"SELECT slug, updated_at FROM pages WHERE status='published'", 'base'=>''],
            ['query'=>"SELECT slug, updated_at FROM services WHERE status='active'", 'base'=>'/services'],
            ['query'=>"SELECT slug, updated_at FROM blog_posts WHERE status='published'", 'base'=>'/blog'],
        ];
        foreach ($sources as $src) {
            $rows = $db->query($src['query'])->fetchAll();
            foreach ($rows as $row) {
                $url = $dom->createElement('url');
                $loc = $dom->createElement('loc', SITE_URL . $src['base'] . '/' . $row['slug']);
                $lastmod = $dom->createElement('lastmod', date('Y-m-d', strtotime($row['updated_at'] ?? 'now')));
                $url->appendChild($loc);
                $url->appendChild($lastmod);
                $urlset->appendChild($url);
            }
        }
        $sitemapPath = __DIR__ . '/../../sitemap.xml';
        $dom->save($sitemapPath);
        set_flash('success', 'Sitemap regenerated successfully.');
        redirect(admin_url('seo-settings.php'));
    }

    if ($action === 'save_robots') {
        $robotsContent = $_POST['robots_content'] ?? '';
        file_put_contents(__DIR__ . '/../../robots.txt', $robotsContent);
        set_flash('success', 'robots.txt saved.');
        redirect(admin_url('seo-settings.php'));
    }
}

$pages = $db->query("SELECT id, title, slug FROM pages WHERE status='published' ORDER BY sort_order")->fetchAll();
$services = $db->query("SELECT id, title, slug FROM services WHERE status='active' ORDER BY sort_order")->fetchAll();

$allPages = [];
foreach ($pages as $p) $allPages[] = ['type'=>'page','id'=>$p['id'],'title'=>$p['title'],'slug'=>$p['slug']];
foreach ($services as $s) $allPages[] = ['type'=>'service','id'=>$s['id'],'title'=>$s['title'],'slug'=>$s['slug']];

$seoData = [];
$seoRows = $db->query("SELECT * FROM seo_settings")->fetchAll();
foreach ($seoRows as $row) {
    $seoData[$row['page_type'].':'.$row['page_id']] = $row;
}

$editItem = null;
if (isset($_GET['edit_type']) && isset($_GET['edit_id'])) {
    $key = sanitize($_GET['edit_type']).':'.(int)$_GET['edit_id'];
    $editItem = $seoData[$key] ?? null;
    $editItem['page_type'] = sanitize($_GET['edit_type']);
    $editItem['page_id'] = (int)$_GET['edit_id'];
}

$robotsContent = file_exists(__DIR__ . '/../../robots.txt') ? file_get_contents(__DIR__ . '/../../robots.txt') : '';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-table mb-4">
            <div class="card-header"><h5>Page SEO Management</h5></div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Page</th><th>Type</th><th>Meta Title</th><th>Meta Description</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allPages as $ap):
                            $key = $ap['type'].':'.$ap['id'];
                            $sd = $seoData[$key] ?? null;
                        ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($ap['title']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo $ap['type']; ?></span></td>
                            <td><small><?php echo htmlspecialchars(truncate($sd['meta_title'] ?? '—', 60)); ?></small></td>
                            <td><small><?php echo htmlspecialchars(truncate($sd['meta_description'] ?? '—', 80)); ?></small></td>
                            <td>
                                <a href="?edit_type=<?php echo $ap['type']; ?>&edit_id=<?php echo $ap['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit SEO</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($editItem):
            $seoKey = $editItem['page_type'].':'.$editItem['page_id'];
            $sd = $seoData[$seoKey] ?? null;
            $pageTitleText = '';
            foreach ($allPages as $ap) {
                if ($ap['type'] === $editItem['page_type'] && (int)$ap['id'] === (int)$editItem['page_id']) {
                    $pageTitleText = $ap['title'];
                    break;
                }
            }
        ?>
        <div class="card form-card mb-4">
            <div class="card-header">Edit SEO for: <?php echo htmlspecialchars($pageTitleText); ?></div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="save_seo">
                    <input type="hidden" name="page_type" value="<?php echo $editItem['page_type']; ?>">
                    <input type="hidden" name="page_id" value="<?php echo $editItem['page_id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($sd['meta_title'] ?? ''); ?>" oninput="updatePreview()" id="metaTitle">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($sd['meta_keywords'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2" oninput="updatePreview()" id="metaDesc"><?php echo htmlspecialchars($sd['meta_description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">OG Title</label>
                            <input type="text" name="og_title" class="form-control" value="<?php echo htmlspecialchars($sd['og_title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">OG Image URL</label>
                            <input type="text" name="og_image" class="form-control" value="<?php echo htmlspecialchars($sd['og_image'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">OG Description</label>
                            <textarea name="og_description" class="form-control" rows="2"><?php echo htmlspecialchars($sd['og_description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Canonical URL</label>
                            <input type="text" name="canonical_url" class="form-control" value="<?php echo htmlspecialchars($sd['canonical_url'] ?? ''); ?>" oninput="updatePreview()" id="canonicalUrl">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Schema Markup (JSON-LD)</label>
                            <textarea name="schema_markup" class="form-control" rows="3" style="font-family:monospace;font-size:0.8rem;"><?php echo htmlspecialchars($sd['schema_markup'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-teal mt-3"><i class="bi bi-check-lg me-1"></i>Save SEO Settings</button>
                    <a href="settings.php" class="btn btn-outline-secondary mt-3 ms-2">Cancel</a>
                </form>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header">Google SERP Preview</div>
            <div class="card-body">
                <div class="serp-preview">
                    <div class="serp-title" id="previewTitle"><?php echo htmlspecialchars(truncate($sd['meta_title'] ?? $pageTitleText . ' - ' . SITE_NAME, 60)); ?></div>
                    <div class="serp-url" id="previewUrl"><?php echo htmlspecialchars($sd['canonical_url'] ?? SITE_URL . '/' . ($editItem['page_type'] === 'page' ? '' : $editItem['page_type'] . '/') . ($ap['slug'] ?? '')); ?></div>
                    <div class="serp-desc" id="previewDesc"><?php echo htmlspecialchars(truncate($sd['meta_description'] ?? '', 160)); ?></div>
                </div>
                <script>
                function updatePreview() {
                    document.getElementById('previewTitle').textContent = document.getElementById('metaTitle').value || '<?php echo htmlspecialchars(addslashes($pageTitleText)); ?>';
                    document.getElementById('previewDesc').textContent = document.getElementById('metaDesc').value || '';
                    const cu = document.getElementById('canonicalUrl');
                    document.getElementById('previewUrl').textContent = cu && cu.value ? cu.value : '<?php echo htmlspecialchars(addslashes(SITE_URL)); ?>/';
                }
                </script>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <div class="card form-card mb-4">
            <div class="card-header"><i class="bi bi-diagram-3 me-2"></i>XML Sitemap</div>
            <div class="card-body">
                <p class="small text-muted">Regenerate the XML sitemap with all published pages, services, and blog posts.</p>
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="generate_sitemap">
                    <button type="submit" class="btn btn-teal btn-sm w-100"><i class="bi bi-arrow-repeat me-1"></i>Regenerate Sitemap</button>
                </form>
                <?php if (file_exists(__DIR__ . '/../../sitemap.xml')): ?>
                <p class="small mt-2 mb-0 text-success"><i class="bi bi-check-circle me-1"></i>Sitemap exists - <?php echo format_date(date('Y-m-d', filemtime(__DIR__.'/../../sitemap.xml')), 'd M Y H:i'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card form-card mb-4">
            <div class="card-header"><i class="bi bi-robot me-2"></i>robots.txt Editor</div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="save_robots">
                    <textarea name="robots_content" class="form-control mb-2" rows="8" style="font-family:monospace;font-size:0.8rem;"><?php echo htmlspecialchars($robotsContent); ?></textarea>
                    <button type="submit" class="btn btn-teal btn-sm w-100"><i class="bi bi-check-lg me-1"></i>Save robots.txt</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
