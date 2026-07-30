<?php
if (!defined('ADMIN_SEO_TOOLS')) {
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/seo-tools.php' . $qs);
    exit;
}
$pageTitle = 'AI SEO Tools';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

$aiSettings = $db->query("SELECT * FROM ai_settings WHERE id = 1")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'save_config') {
        $provider = sanitize($_POST['api_provider'] ?? 'openai');
        $apiKey = $_POST['api_key'] ?? '';
        $model = sanitize($_POST['model_name'] ?? '');
        $temp = (float)($_POST['temperature'] ?? 0.7);
        $maxTokens = (int)($_POST['max_tokens'] ?? 2000);
        // Upsert: insert if no row exists, update if it does
        $existing = $db->query("SELECT id FROM ai_settings WHERE id = 1")->fetchColumn();
        if ($existing) {
            $db->prepare("UPDATE ai_settings SET api_provider=?, api_key=?, model_name=?, temperature=?, max_tokens=?, updated_at=NOW() WHERE id=1")
                ->execute([$provider, $apiKey, $model, $temp, $maxTokens]);
        } else {
            $db->prepare("INSERT INTO ai_settings (id, api_provider, api_key, model_name, temperature, max_tokens, is_active, created_at) VALUES (1, ?, ?, ?, ?, ?, 1, NOW())")
                ->execute([$provider, $apiKey, $model, $temp, $maxTokens]);
        }
        set_flash('success', 'AI configuration saved.');
        redirect(admin_url('seo-tools.php'));
    }

    if ($action === 'approve_suggestion') {
        $sid = (int)($_POST['suggestion_id'] ?? 0);
        $db->prepare("UPDATE ai_suggestions SET is_approved=1 WHERE id=?")->execute([$sid]);
        set_flash('success', 'Suggestion approved.');
        redirect(admin_url('seo-tools.php'));
    }

    if ($action === 'discard_suggestion') {
        $sid = (int)($_POST['suggestion_id'] ?? 0);
        $db->prepare("DELETE FROM ai_suggestions WHERE id=?")->execute([$sid]);
        set_flash('success', 'Suggestion discarded.');
        redirect(admin_url('seo-tools.php'));
    }

    if ($action === 'bulk_action') {
        $ids = $_POST['selected_ids'] ?? [];
        $bulkAction = sanitize($_POST['bulk_action'] ?? '');
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            if ($bulkAction === 'approve') {
                $db->prepare("UPDATE ai_suggestions SET is_approved=1 WHERE id IN ($placeholders)")->execute($ids);
                set_flash('success', 'Selected suggestions approved.');
            } elseif ($bulkAction === 'discard') {
                $db->prepare("DELETE FROM ai_suggestions WHERE id IN ($placeholders)")->execute($ids);
                set_flash('success', 'Selected suggestions discarded.');
            }
        }
        redirect(admin_url('seo-tools.php'));
    }
}

if (isset($_GET['generate']) && $_GET['generate'] === 'keywords') {
    $keyword = sanitize($_GET['keyword'] ?? '');
    if ($keyword) generateMockKeywords($db, $keyword);
}

$pageTargets = [];
$pages = $db->query("SELECT id, title, slug FROM pages WHERE status='published' ORDER BY title")->fetchAll();
foreach ($pages as $p) $pageTargets[] = ['type'=>'page','id'=>$p['id'],'title'=>$p['title'],'slug'=>$p['slug']];
$services = $db->query("SELECT id, title, slug FROM services WHERE status='active' ORDER BY title")->fetchAll();
foreach ($services as $s) $pageTargets[] = ['type'=>'service','id'=>$s['id'],'title'=>$s['title'],'slug'=>$s['slug']];
$blogPosts = $db->query("SELECT id, title, slug FROM blog_posts WHERE status='published' ORDER BY title")->fetchAll();
foreach ($blogPosts as $b) $pageTargets[] = ['type'=>'blog_post','id'=>$b['id'],'title'=>$b['title'],'slug'=>$b['slug']];

$suggestionFilter = sanitize($_GET['stype'] ?? '');
$statusFilter = sanitize($_GET['sstatus'] ?? '');
$swhere = [];
$sparams = [];
if ($suggestionFilter) { $swhere[] = 'suggestion_type=?'; $sparams[] = $suggestionFilter; }
if ($statusFilter === 'approved') { $swhere[] = 'is_approved=1'; } elseif ($statusFilter === 'discarded') { $swhere[] = 'is_approved=0'; }
$swhereClause = $swhere ? 'WHERE ' . implode(' AND ', $swhere) : '';
$suggestions = $db->prepare("SELECT * FROM ai_suggestions $swhereClause ORDER BY created_at DESC LIMIT 50");
$suggestions->execute($sparams);
$suggestions = $suggestions->fetchAll();

function generateMockKeywords($db, $keyword) {
    $mockKeywords = [
        ["$keyword solutions", 880, 'Low', 95],
        ["$keyword services UK", 590, 'Medium', 88],
        ["best $keyword provider", 320, 'High', 72],
        ["$keyword consulting", 440, 'Medium', 85],
        ["$keyword implementation", 260, 'Medium', 80],
        ["$keyword support", 720, 'Low', 92],
        ["$keyword management", 510, 'High', 68],
        ["$keyword automation", 180, 'Low', 90],
        ["$keyword training", 340, 'Medium', 78],
        ["$keyword for business", 620, 'Medium', 84],
    ];
    foreach ($mockKeywords as $kw) {
        $db->prepare("INSERT INTO ai_suggestions (page_type, page_id, suggestion_type, suggestion_content, is_approved) VALUES (?, ?, 'keyword', ?, 0)")
            ->execute(['page', 1, json_encode(['keyword'=>$kw[0],'volume'=>$kw[1],'competition'=>$kw[2],'relevance'=>$kw[3]])]);
    }
    set_flash('success', 'Keyword ideas generated for: ' . htmlspecialchars($keyword));
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="card form-card mb-4">
    <div class="card-header"><i class="bi bi-robot me-2"></i>AI API Configuration</div>
    <div class="card-body">
        <form method="POST">
            <?php echo Security::csrf_field(); ?>
            <input type="hidden" name="action" value="save_config">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">API Provider</label>
                    <select name="api_provider" class="form-select">
                        <?php foreach (['openai'=>'OpenAI','deepseek'=>'DeepSeek','anthropic'=>'Anthropic','custom'=>'Custom'] as $k=>$v): ?>
                        <option value="<?php echo $k; ?>" <?php echo ($aiSettings['api_provider']??'openai')===$k?'selected':''; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">API Key</label>
                    <div class="input-group">
                        <input type="password" name="api_key" id="apiKeyInput" class="form-control" value="<?php echo htmlspecialchars($aiSettings['api_key'] ?? ''); ?>">
                        <button type="button" class="btn btn-outline-secondary" onclick="const e=document.getElementById('apiKeyInput');e.type=e.type==='password'?'text':'password';"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Model</label>
                    <select name="model_name" class="form-select">
                        <?php
                        $models = ['gpt-4o','gpt-4o-mini','deepseek-chat','claude-3-5-sonnet','custom'];
                        foreach ($models as $m):
                        ?>
                        <option value="<?php echo $m; ?>" <?php echo ($aiSettings['model_name']??'')===$m?'selected':''; ?>><?php echo $m; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Temp</label>
                    <input type="number" name="temperature" class="form-control" step="0.1" min="0" max="1" value="<?php echo $aiSettings['temperature']??0.7; ?>">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Max Tokens</label>
                    <input type="number" name="max_tokens" class="form-control" value="<?php echo $aiSettings['max_tokens']??2000; ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-teal w-100"><i class="bi bi-check-lg"></i> Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card form-card mb-4">
    <div class="card-header"><i class="bi bi-stars me-2"></i>AI Content Tools</div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Target Page</label>
            <select id="targetPage" class="form-select" style="max-width:400px;">
                <option value="">Select a page...</option>
                <?php foreach ($pageTargets as $pt): ?>
                <option value="<?php echo $pt['type'].':'.$pt['id']; ?>"><?php echo htmlspecialchars($pt['title']); ?> (<?php echo $pt['type']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="row g-2 mb-3">
            <?php
            $actions = [
                'keyword' => ['Generate Keyword Ideas', 'bi-lightbulb'],
                'title' => ['Generate SEO Title', 'bi-type-h1'],
                'meta_description' => ['Generate Meta Description', 'bi-card-text'],
                'meta_keywords' => ['Generate Meta Keywords', 'bi-tags'],
                'blog_topic' => ['Generate Blog Topic Ideas', 'bi-journal'],
                'faq' => ['Generate FAQ Suggestions', 'bi-question-circle'],
                'alt_text' => ['Generate Image Alt Text', 'bi-image'],
                'internal_link' => ['Generate Internal Link Suggestions', 'bi-link-45deg'],
                'content_improvement' => ['Generate Content Improvements', 'bi-pencil-square'],
            ];
            foreach ($actions as $type => $info):
            ?>
            <div class="col-lg-4 col-md-6">
                <button type="button" class="btn btn-outline-secondary btn-sm w-100 generate-btn" data-type="<?php echo $type; ?>">
                    <i class="<?php echo $info[1]; ?> me-1"></i><?php echo $info[0]; ?>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="aiResults" class="ai-loading d-none">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <span>Generating suggestions...</span>
        </div>
        <div id="aiResultContent"></div>
    </div>
</div>

<div class="card form-card mb-4">
    <div class="card-header"><i class="bi bi-speedometer me-2"></i>SEO Score Analysis</div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Select Page to Analyse</label>
                <select id="analysePage" class="form-select">
                    <option value="">Choose page...</option>
                    <?php foreach ($pageTargets as $pt): ?>
                    <option value="<?php echo $pt['type'].':'.$pt['id']; ?>"><?php echo htmlspecialchars($pt['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-teal w-100" onclick="analyseSEO();"><i class="bi bi-search me-1"></i>Analyse</button>
            </div>
        </div>
        <div id="seoScoreResult" class="mt-3"></div>
    </div>
</div>

<div class="card form-card mb-4">
    <div class="card-header"><i class="bi bi-search me-2"></i>Keyword Research</div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="generate" value="keywords">
            <div class="col-md-4">
                <label class="form-label">Target Keyword or Topic</label>
                <input type="text" name="keyword" class="form-control" placeholder="e.g. workforce management" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-teal w-100"><i class="bi bi-lightbulb me-1"></i>Generate</button>
            </div>
        </form>
        <?php
        $kwStmt = $db->prepare("SELECT * FROM ai_suggestions WHERE suggestion_type='keyword' ORDER BY created_at DESC LIMIT 10");
        $kwStmt->execute();
        $keywords = $kwStmt->fetchAll();
        if (!empty($keywords)):
        ?>
        <div class="table-responsive mt-3">
            <table class="table table-sm">
                <thead><tr><th>Keyword</th><th>Volume (est.)</th><th>Competition</th><th>Relevance</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($keywords as $kw):
                    $data = json_decode($kw['suggestion_content'], true);
                    if (!$data) continue;
                ?>
                <tr>
                    <td class="fw-semibold"><?php echo htmlspecialchars($data['keyword'] ?? ''); ?></td>
                    <td><?php echo $data['volume'] ?? '—'; ?></td>
                    <td><span class="badge bg-<?php echo ($data['competition']??'')==='High'?'danger':(($data['competition']??'')==='Medium'?'warning':'success'); ?>"><?php echo $data['competition'] ?? '—'; ?></span></td>
                    <td><?php echo $data['relevance'] ?? '—'; ?>%</td>
                    <td><button class="btn btn-sm btn-teal-outline" disabled>Add to Page</button></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card card-table mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Saved Suggestions</h5>
        <form method="GET" class="d-flex gap-2">
            <select name="stype" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Types</option>
                <?php foreach ($actions as $type => $info): ?>
                <option value="<?php echo $type; ?>" <?php echo $suggestionFilter===$type?'selected':''; ?>><?php echo $info[0]; ?></option>
                <?php endforeach; ?>
            </select>
            <select name="sstatus" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="approved" <?php echo $statusFilter==='approved'?'selected':''; ?>>Approved</option>
                <option value="discarded" <?php echo $statusFilter==='discarded'?'selected':''; ?>>Discarded</option>
            </select>
        </form>
    </div>
    <form method="POST" id="bulkForm">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="bulk_action">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Page</th>
                        <th>Type</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suggestions)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No suggestions yet.</td></tr>
                    <?php else: foreach ($suggestions as $sg): ?>
                    <tr>
                        <td><input type="checkbox" name="selected_ids[]" value="<?php echo $sg['id']; ?>" class="row-check"></td>
                        <td><?php echo htmlspecialchars($sg['page_type'].':'.$sg['page_id']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo str_replace('_',' ',$sg['suggestion_type']); ?></span></td>
                        <td style="max-width:300px;"><small><?php echo htmlspecialchars(truncate($sg['suggestion_content'], 100)); ?></small></td>
                        <td>
                            <?php if ($sg['is_approved']): ?>
                            <span class="badge bg-success">Approved</span>
                            <?php elseif ($sg['is_applied']): ?>
                            <span class="badge bg-primary">Applied</span>
                            <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><small><?php echo format_date($sg['created_at'], 'd M Y'); ?></small></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <?php echo Security::csrf_field(); ?>
                                <input type="hidden" name="action" value="approve_suggestion">
                                <input type="hidden" name="suggestion_id" value="<?php echo $sg['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                            </form>
                            <form method="POST" class="d-inline">
                                <?php echo Security::csrf_field(); ?>
                                <input type="hidden" name="action" value="discard_suggestion">
                                <input type="hidden" name="suggestion_id" value="<?php echo $sg['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Discard"><i class="bi bi-x-lg"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex gap-2">
            <select name="bulk_action" class="form-select form-select-sm" style="width:auto;">
                <option value="">Bulk Action...</option>
                <option value="approve">Approve Selected</option>
                <option value="discard">Discard Selected</option>
            </select>
            <button type="submit" class="btn btn-sm btn-teal">Apply</button>
        </div>
    </form>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
});

document.querySelectorAll('.generate-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const page = document.getElementById('targetPage').value;
        if (!page) { alert('Please select a target page first.'); return; }
        const type = this.dataset.type;
        document.getElementById('aiResults').classList.remove('d-none');
        document.getElementById('aiResultContent').innerHTML = '';
        setTimeout(() => {
            document.getElementById('aiResults').classList.add('d-none');
            const mockResults = getMockResults(type, page);
            document.getElementById('aiResultContent').innerHTML = mockResults;
        }, 1500);
    });
});

function getMockResults(type, page) {
    const labels = {
        keyword: 'Keyword Ideas',
        title: 'SEO Title Suggestions',
        meta_description: 'Meta Description Suggestions',
        meta_keywords: 'Meta Keywords',
        blog_topic: 'Blog Topic Ideas',
        faq: 'FAQ Suggestions',
        alt_text: 'Image Alt Text',
        internal_link: 'Internal Link Suggestions',
        content_improvement: 'Content Improvements'
    };
    const suggestions = [
        'Optimize your target keywords for better search visibility and organic traffic.',
        'Use long-tail keywords to capture specific search intent and reduce competition.',
        'Update meta descriptions to include primary keywords and compelling CTAs.',
        'Improve heading structure with H1, H2, and H3 tags for better content hierarchy.',
        'Add internal links to related services and case studies to improve site structure.'
    ];
    let html = '<h6 class="mt-3">' + (labels[type] || type) + '</h6>';
    suggestions.slice(0, 3).forEach((s, i) => {
        html += `<div class="ai-result-card d-flex justify-content-between align-items-start">
            <div><i class="bi bi-lightbulb text-warning me-2"></i>${s}</div>
            <div class="d-flex gap-1 flex-shrink-0 ms-3">
                <button class="btn btn-sm btn-success" disabled><i class="bi bi-check-lg"></i> Approve</button>
                <button class="btn btn-sm btn-outline-danger" disabled><i class="bi bi-x-lg"></i> Discard</button>
            </div>
        </div>`;
    });
    html += '<p class="text-muted small mt-2"><i class="bi bi-info-circle me-1"></i>These are demo suggestions. Configure an AI API key for real AI-generated results.</p>';
    return html;
}

function analyseSEO() {
    const page = document.getElementById('analysePage').value;
    if (!page) { alert('Please select a page to analyse.'); return; }
    const score = Math.floor(Math.random() * 31) + 65;
    const scoreClass = score >= 80 ? 'score-good' : (score >= 60 ? 'score-average' : 'score-poor');
    document.getElementById('seoScoreResult').innerHTML = `
        <h6 class="mb-2">SEO Score: ${score}/100</h6>
        <div class="seo-score-bar mb-3"><div class="seo-score-fill ${scoreClass}" style="width:${score}%"></div></div>
        <div class="row g-2">
            <div class="col-md-6"><small class="text-muted">Title Score:</small> <span class="fw-semibold">${Math.floor(Math.random()*30)+70}/100</span></div>
            <div class="col-md-6"><small class="text-muted">Meta Description:</small> <span class="fw-semibold">${Math.floor(Math.random()*40)+60}/100</span></div>
            <div class="col-md-6"><small class="text-muted">Heading Structure:</small> <span class="fw-semibold">${Math.floor(Math.random()*40)+55}/100</span></div>
            <div class="col-md-6"><small class="text-muted">Keyword Density:</small> <span class="fw-semibold">${Math.floor(Math.random()*30)+65}/100</span></div>
            <div class="col-md-6"><small class="text-muted">Content Length:</small> <span class="fw-semibold">${Math.floor(Math.random()*30)+65}/100</span></div>
            <div class="col-md-6"><small class="text-muted">Image Alt Tags:</small> <span class="fw-semibold">${Math.floor(Math.random()*50)+50}/100</span></div>
            <div class="col-md-6"><small class="text-muted">Internal Links:</small> <span class="fw-semibold">${Math.floor(Math.random()*30)+70}/100</span></div>
            <div class="col-md-6"><small class="text-muted">Mobile Friendliness:</small> <span class="fw-semibold text-success">Good</span></div>
        </div>
        <p class="text-muted small mt-2"><i class="bi bi-info-circle me-1"></i>This is a demo analysis. Configure an AI API key for real SEO analysis.</p>
    `;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
