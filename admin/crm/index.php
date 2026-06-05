<?php
$pageTitle = 'CRM - Lead Management';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

$perPage = 25;
$page = max(1, (int)($_GET['page'] ?? 1));

$statusFilter = sanitize($_GET['status'] ?? '');
$serviceFilter = sanitize($_GET['service'] ?? '');
$priorityFilter = sanitize($_GET['priority'] ?? '');
$dateFrom = sanitize($_GET['date_from'] ?? '');
$dateTo = sanitize($_GET['date_to'] ?? '');
$assignedFilter = sanitize($_GET['assigned'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$view = sanitize($_GET['view'] ?? 'table');

$where = [];
$params = [];

if ($statusFilter) { $where[] = 'l.lead_status = ?'; $params[] = $statusFilter; }
if ($serviceFilter) { $where[] = 'l.service_interested = ?'; $params[] = $serviceFilter; }
if ($priorityFilter) { $where[] = 'l.priority = ?'; $params[] = $priorityFilter; }
if ($assignedFilter) { $where[] = 'l.assigned_user_id = ?'; $params[] = $assignedFilter; }
if ($dateFrom) { $where[] = 'l.created_at >= ?'; $params[] = $dateFrom . ' 00:00:00'; }
if ($dateTo) { $where[] = 'l.created_at <= ?'; $params[] = $dateTo . ' 23:59:59'; }
if ($search) {
    $where[] = '(l.full_name LIKE ? OR l.company_name LIKE ? OR l.email LIKE ?)';
    $s = "%$search%";
    $params[] = $s; $params[] = $s; $params[] = $s;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $db->prepare("SELECT COUNT(*) FROM crm_leads l $whereClause");
$countStmt->execute($params);
$totalLeads = $countStmt->fetchColumn();

$statsStmt = $db->query("SELECT lead_status, COUNT(*) as cnt FROM crm_leads GROUP BY lead_status");
$stats = [];
foreach ($statsStmt as $row) { $stats[$row['lead_status']] = (int)$row['cnt']; }

$services = $db->query("SELECT title FROM services WHERE status='active' ORDER BY sort_order")->fetchAll(PDO::FETCH_COLUMN);
$admins = $db->query("SELECT id, full_name FROM admins WHERE status='active' ORDER BY full_name")->fetchAll();

$pagination = paginate($totalLeads, $perPage, $page);

$stmt = $db->prepare("
    SELECT l.*, a.full_name as assigned_name
    FROM crm_leads l
    LEFT JOIN admins a ON l.assigned_user_id = a.id
    $whereClause
    ORDER BY l.created_at DESC
    LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
");
$stmt->execute($params);
$leads = $stmt->fetchAll();

$export = isset($_GET['export']);
if ($export) {
    $allStmt = $db->prepare("SELECT l.*, a.full_name as assigned_name FROM crm_leads l LEFT JOIN admins a ON l.assigned_user_id = a.id $whereClause ORDER BY l.created_at DESC");
    $allStmt->execute($params);
    $allLeads = $allStmt->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="crm_leads_' . date('Ymd_His') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Company', 'Email', 'Phone', 'Service', 'Source', 'Status', 'Priority', 'Assigned To', 'Follow-up', 'Created']);
    foreach ($allLeads as $l) {
        fputcsv($output, [
            $l['full_name'], $l['company_name'], $l['email'], $l['phone'],
            $l['service_interested'], $l['lead_source'], $l['lead_status'], $l['priority'],
            $l['assigned_name'] ?? '', $l['follow_up_date'] ?? '', $l['created_at']
        ]);
    }
    fclose($output);
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row g-3 mb-4">
    <?php
    $statCards = [
        ['label' => 'Total Leads', 'key' => null, 'icon' => 'bi-people', 'class' => ''],
        ['label' => 'New', 'key' => 'new', 'icon' => 'bi-star', 'class' => 'badge-new'],
        ['label' => 'Contacted', 'key' => 'contacted', 'icon' => 'bi-telephone', 'class' => 'badge-contacted'],
        ['label' => 'Qualified', 'key' => 'qualified', 'icon' => 'bi-check-circle', 'class' => 'badge-qualified'],
        ['label' => 'Won', 'key' => 'won', 'icon' => 'bi-trophy', 'class' => 'badge-won'],
        ['label' => 'Lost', 'key' => 'lost', 'icon' => 'bi-x-circle', 'class' => 'badge-lost'],
    ];
    foreach ($statCards as $card):
        $val = $card['key'] ? ($stats[$card['key']] ?? 0) : $totalLeads;
    ?>
    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
        <div class="card card-stat">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value"><?php echo $val; ?></div>
                    <div class="stat-label"><?php echo $card['label']; ?></div>
                </div>
                <div class="stat-icon bg-teal-light">
                    <i class="bi <?php echo $card['icon']; ?>"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card card-table mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5>Lead Management</h5>
        <div class="d-flex gap-2">
            <a href="?view=table" class="btn btn-sm <?php echo $view !== 'kanban' ? 'btn-teal' : 'btn-outline-secondary'; ?>"><i class="bi bi-table me-1"></i>Table</a>
            <a href="?view=kanban" class="btn btn-sm <?php echo $view === 'kanban' ? 'btn-teal' : 'btn-outline-secondary'; ?>"><i class="bi bi-kanban me-1"></i>Kanban</a>
            <a href="edit.php" class="btn btn-sm btn-teal"><i class="bi bi-plus-lg me-1"></i>Add Lead</a>
            <a href="?export=1&<?php echo http_build_query(array_filter(['status'=>$statusFilter,'service'=>$serviceFilter,'priority'=>$priorityFilter,'date_from'=>$dateFrom,'date_to'=>$dateTo,'assigned'=>$assignedFilter,'search'=>$search])); ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>Export CSV</a>
        </div>
    </div>
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="view" value="<?php echo $view; ?>">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, company, email..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <?php foreach (['new','contacted','qualified','proposal_sent','won','lost'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $statusFilter === $s ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_', ' ', $s)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label mb-1">Service</label>
                <select name="service" class="form-select form-select-sm">
                    <option value="">All Services</option>
                    <?php foreach ($services as $svc): ?>
                    <option value="<?php echo htmlspecialchars($svc); ?>" <?php echo $serviceFilter === $svc ? 'selected' : ''; ?>><?php echo htmlspecialchars($svc); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label mb-1">Priority</label>
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="high" <?php echo $priorityFilter === 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="medium" <?php echo $priorityFilter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="low" <?php echo $priorityFilter === 'low' ? 'selected' : ''; ?>>Low</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label mb-1">Assigned To</label>
                <select name="assigned" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($admins as $a): ?>
                    <option value="<?php echo $a['id']; ?>" <?php echo $assignedFilter == $a['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6">
                <label class="form-label mb-1">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dateFrom); ?>">
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6">
                <label class="form-label mb-1">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dateTo); ?>">
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-sm btn-teal"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="?view=<?php echo $view; ?>" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>

    <?php if ($view === 'kanban'): ?>
    <div class="card-body">
        <?php
        $statuses = ['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'proposal_sent' => 'Proposal Sent', 'won' => 'Won', 'lost' => 'Lost'];
        $colColors = ['new'=>'#d1ecf1','contacted'=>'#cce5ff','qualified'=>'#d4edda','proposal_sent'=>'#fff3cd','won'=>'#d1f2d1','lost'=>'#f8d7da'];
        $kanbanStmt = $db->prepare("SELECT l.*, a.full_name as assigned_name FROM crm_leads l LEFT JOIN admins a ON l.assigned_user_id = a.id $whereClause ORDER BY l.created_at DESC");
        $kanbanStmt->execute($params);
        $allLeads = $kanbanStmt->fetchAll();
        $kanbanData = [];
        foreach ($allLeads as $l) { $kanbanData[$l['lead_status']][] = $l; }
        ?>
        <div class="kanban-board">
            <?php foreach ($statuses as $key => $label):
                $colLeads = $kanbanData[$key] ?? [];
                $count = count($colLeads);
            ?>
            <div class="kanban-column" style="background:<?php echo $colColors[$key]; ?>20;">
                <div class="kanban-column-header" style="background:<?php echo $colColors[$key]; ?>60;">
                    <?php echo $label; ?> <span class="count"><?php echo $count; ?></span>
                </div>
                <?php foreach ($colLeads as $lead): ?>
                <a href="view.php?id=<?php echo $lead['id']; ?>" class="kanban-card text-decoration-none" style="border-left-color: <?php echo $colColors[$key]; ?>;">
                    <div class="card-name text-dark"><?php echo htmlspecialchars($lead['full_name']); ?></div>
                    <div class="card-company"><?php echo htmlspecialchars($lead['company_name'] ?: 'N/A'); ?></div>
                    <div class="card-service"><?php echo htmlspecialchars($lead['service_interested'] ?: ''); ?></div>
                    <div class="card-meta">
                        <span class="badge badge-priority badge-<?php echo $lead['priority']; ?>"><?php echo ucfirst($lead['priority']); ?></span>
                        <span><?php echo format_date($lead['created_at'], 'd M Y'); ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
                <?php if ($count === 0): ?>
                <p class="text-muted text-center small mb-0 py-3">No leads</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Email</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Assigned To</th>
                    <th>Follow-up</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">No leads found.</td></tr>
                <?php else: foreach ($leads as $lead): ?>
                <tr>
                    <td><a href="view.php?id=<?php echo $lead['id']; ?>" class="fw-semibold text-decoration-none"><?php echo htmlspecialchars($lead['full_name']); ?></a></td>
                    <td><?php echo htmlspecialchars($lead['company_name'] ?: '—'); ?></td>
                    <td><?php echo htmlspecialchars($lead['email']); ?></td>
                    <td><?php echo htmlspecialchars($lead['service_interested'] ?: '—'); ?></td>
                    <td><span class="badge badge-status badge-<?php echo $lead['lead_status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $lead['lead_status'])); ?></span></td>
                    <td><span class="badge badge-priority badge-<?php echo $lead['priority']; ?>"><?php echo ucfirst($lead['priority']); ?></span></td>
                    <td><?php echo htmlspecialchars($lead['assigned_name'] ?? 'Unassigned'); ?></td>
                    <td><?php echo $lead['follow_up_date'] ? format_date($lead['follow_up_date'], 'd M Y') : '—'; ?></td>
                    <td>
                        <a href="view.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                        <a href="edit.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if ($pagination['total_pages'] > 1):
        $queryStr = http_build_query(array_filter(['view'=>$view,'status'=>$statusFilter,'service'=>$serviceFilter,'priority'=>$priorityFilter,'date_from'=>$dateFrom,'date_to'=>$dateTo,'assigned'=>$assignedFilter,'search'=>$search]));
    ?>
    <div class="card-footer">
        <nav>
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <li class="page-item <?php echo !$pagination['has_previous'] ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php echo $queryStr; ?>&page=<?php echo $page - 1; ?>">&laquo;</a>
                </li>
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo $queryStr; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?php echo !$pagination['has_next'] ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php echo $queryStr; ?>&page=<?php echo $page + 1; ?>">&raquo;</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
