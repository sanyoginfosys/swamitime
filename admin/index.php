<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/security.php';

Auth::requireLogin();

$adminUser = Auth::user();
$pageTitle = 'Dashboard';

$stats = [
    'enquiries'   => 0,
    'crm_leads'   => 0,
    'pages'       => 0,
    'blog_posts'  => 0,
    'case_studies'=> 0,
    'services'    => 0,
];

$recentEnquiries = [];
$recentLeads = [];
$crmStatusBreakdown = '';

try {
    $db = getDB();

    $stats['enquiries'] = (int) $db->query("SELECT COUNT(*) FROM enquiries")->fetchColumn();
    $stats['crm_leads'] = (int) $db->query("SELECT COUNT(*) FROM crm_leads")->fetchColumn();
    $stats['pages'] = (int) $db->query("SELECT COUNT(*) FROM pages WHERE status = 'published'")->fetchColumn();
    $stats['blog_posts'] = (int) $db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
    $stats['case_studies'] = (int) $db->query("SELECT COUNT(*) FROM case_studies")->fetchColumn();
    $stats['services'] = (int) $db->query("SELECT COUNT(*) FROM services")->fetchColumn();

    $newEnquiries = (int) $db->query("SELECT COUNT(*) FROM enquiries WHERE status = 'new'")->fetchColumn();

    $crmStatusData = $db->query("
        SELECT status, COUNT(*) as cnt
        FROM crm_leads
        GROUP BY status
        ORDER BY cnt DESC
    ")->fetchAll();

    $statusLabels = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'proposal' => 'Proposal',
        'negotiation' => 'Negotiation',
        'won' => 'Won',
        'lost' => 'Lost',
    ];

    $crmStatusParts = [];
    foreach ($crmStatusData as $row) {
        $label = $statusLabels[$row['status']] ?? ucfirst($row['status']);
        $crmStatusParts[] = $label . ': ' . $row['cnt'];
    }
    $crmStatusBreakdown = implode(' | ', $crmStatusParts);

    $recentEnquiries = $db->query("
        SELECT id, name, company, email, service, status, created_at
        FROM enquiries
        ORDER BY created_at DESC
        LIMIT 5
    ")->fetchAll();

    $recentLeads = $db->query("
        SELECT id, name, company, status, priority, created_at
        FROM crm_leads
        ORDER BY created_at DESC
        LIMIT 5
    ")->fetchAll();

} catch (Exception $e) {
    error_log('Admin dashboard query error: ' . $e->getMessage());
}

function statusBadge(string $status): string
{
    $map = [
        'new'     => ['bg' => 'bg-primary',       'label' => 'New'],
        'read'    => ['bg' => 'bg-success',        'label' => 'Read'],
        'replied' => ['bg' => 'bg-info',           'label' => 'Replied'],
        'spam'    => ['bg' => 'bg-secondary',      'label' => 'Spam'],
        'contacted'  => ['bg' => 'bg-info',        'label' => 'Contacted'],
        'qualified'  => ['bg' => 'bg-primary',     'label' => 'Qualified'],
        'proposal'   => ['bg' => 'bg-warning text-dark', 'label' => 'Proposal'],
        'negotiation'=> ['bg' => 'bg-warning text-dark', 'label' => 'Negotiation'],
        'won'     => ['bg' => 'bg-success',        'label' => 'Won'],
        'lost'    => ['bg' => 'bg-danger',         'label' => 'Lost'],
    ];
    $s = strtolower($status);
    $cfg = $map[$s] ?? ['bg' => 'bg-secondary', 'label' => ucfirst($status)];
    return '<span class="badge ' . $cfg['bg'] . '">' . htmlspecialchars($cfg['label'], ENT_QUOTES, 'UTF-8') . '</span>';
}

function priorityBadge(string $priority): string
{
    $map = [
        'low'    => 'bg-success',
        'medium' => 'bg-warning text-dark',
        'high'   => 'bg-danger',
        'urgent' => 'bg-danger',
    ];
    $p = strtolower($priority);
    $bg = $map[$p] ?? 'bg-secondary';
    return '<span class="badge ' . $bg . '">' . htmlspecialchars(ucfirst($priority), ENT_QUOTES, 'UTF-8') . '</span>';
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold">Dashboard</h1>
        <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($adminUser['name'] ?? $adminUser['username'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?>.</p>
    </div>
    <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
        <i class="bi bi-box-arrow-up-right me-1"></i> View Site
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-envelope"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['enquiries']); ?></span>
                <span class="stat-label">Total Enquiries
                    <?php if (($newEnquiries ?? 0) > 0): ?>
                        <span class="badge bg-primary ms-1"><?php echo $newEnquiries; ?> New</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-success-subtle text-success">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['crm_leads']); ?></span>
                <span class="stat-label">CRM Leads
                    <?php if ($crmStatusBreakdown): ?>
                        <small class="d-block text-muted" style="font-size:0.68rem;"><?php echo htmlspecialchars($crmStatusBreakdown, ENT_QUOTES, 'UTF-8'); ?></small>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-info-subtle text-info">
                <i class="bi bi-file-text"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['pages']); ?></span>
                <span class="stat-label">Published Pages</span>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-journal"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['blog_posts']); ?></span>
                <span class="stat-label">Blog Posts</span>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-danger-subtle text-danger">
                <i class="bi bi-star"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['case_studies']); ?></span>
                <span class="stat-label">Case Studies</span>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-teal-subtle text-teal">
                <i class="bi bi-briefcase"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($stats['services']); ?></span>
                <span class="stat-label">Services</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Enquiries</h5>
                <a href="<?php echo BASE_URL; ?>/admin/enquiries.php" class="btn btn-sm btn-outline-teal">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentEnquiries): ?>
                            <?php foreach ($recentEnquiries as $enq): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($enq['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($enq['company'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($enq['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($enq['service'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo statusBadge($enq['status']); ?></td>
                                    <td class="text-nowrap"><?php echo format_date($enq['created_at'], 'd M Y'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No enquiries yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Latest CRM Leads</h5>
                <a href="<?php echo BASE_URL; ?>/admin/crm-leads.php" class="btn btn-sm btn-outline-teal">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentLeads): ?>
                            <?php foreach ($recentLeads as $lead): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($lead['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($lead['company'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo statusBadge($lead['status']); ?></td>
                                    <td><?php echo priorityBadge($lead['priority']); ?></td>
                                    <td class="text-nowrap"><?php echo format_date($lead['created_at'], 'd M Y'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No leads yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="card-title mb-0">SEO Score Summary</h5>
            </div>
            <div class="card-body text-center py-4">
                <div class="seo-score-circle">
                    <svg width="120" height="120" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#E5E7EB" stroke-width="8"/>
                        <circle cx="60" cy="60" r="54" fill="none" stroke="var(--teal-mid)" stroke-width="8"
                            stroke-dasharray="339.292" stroke-dashoffset="84.8" stroke-linecap="round"
                            transform="rotate(-90 60 60)"/>
                    </svg>
                    <div class="seo-score-value">75<span style="font-size:0.4em;">/100</span></div>
                </div>
                <p class="text-muted mt-3 mb-2" style="font-size:0.85rem;">Overall SEO health score based on meta tags, content quality, and site performance.</p>
                <div class="d-flex justify-content-center gap-3 mt-2">
                    <span class="text-success" style="font-size:0.8rem;"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i> Good</span>
                    <span class="text-warning" style="font-size:0.8rem;"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i> Needs Work</span>
                    <span class="text-danger" style="font-size:0.8rem;"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i> Critical</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>/admin/blog-post-edit.php" class="admin-btn btn-teal">
                        <i class="bi bi-plus-circle me-2"></i> Add Blog Post
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/case-study-edit.php" class="admin-btn btn-teal">
                        <i class="bi bi-plus-circle me-2"></i> Add Case Study
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/pages.php" class="admin-btn btn-teal">
                        <i class="bi bi-file-text me-2"></i> Manage Pages
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/enquiries.php" class="admin-btn btn-teal">
                        <i class="bi bi-envelope me-2"></i> View Enquiries
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="card-title mb-0">Traffic Overview</h5>
            </div>
            <div class="card-body text-center py-4">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#F0FDF4;">
                            <i class="bi bi-eye fs-3 text-success"></i>
                            <div class="fw-bold fs-5">—</div>
                            <small class="text-muted">Page Views</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#EFF6FF;">
                            <i class="bi bi-person-check fs-3 text-primary"></i>
                            <div class="fw-bold fs-5">—</div>
                            <small class="text-muted">Unique Visitors</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#FFF7ED;">
                            <i class="bi bi-search fs-3 text-warning"></i>
                            <div class="fw-bold fs-5">—</div>
                            <small class="text-muted">Organic Clicks</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#FEF2F2;">
                            <i class="bi bi-graph-up fs-3 text-danger"></i>
                            <div class="fw-bold fs-5">—</div>
                            <small class="text-muted">Bounce Rate</small>
                        </div>
                    </div>
                </div>
                <p class="text-muted mb-0" style="font-size:0.8rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    Connect Google Analytics for live data.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
