<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$search   = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$page     = max(1, (int) ($_GET['p'] ?? 1));
$perPage  = 25;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id     = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'read';
        $allowed = ['new', 'read', 'replied', 'archived'];
        if (in_array($status, $allowed, true)) {
            $stmt = $db->prepare('UPDATE enquiries SET status = ? WHERE id = ?');
            $stmt->execute([$status, $id]);
            set_flash('success', 'Enquiry marked as ' . ucfirst($status) . '.');
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM enquiries WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'Enquiry deleted.');
    } elseif ($action === 'export_csv') {
        $where = '';
        $params = [];
        $conditions = [];
        if ($search !== '') {
            $conditions[] = '(full_name LIKE ? OR company_name LIKE ? OR email LIKE ? OR service_required LIKE ?)';
            $params = array_merge($params, ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"]);
        }
        if ($statusFilter !== '' && in_array($statusFilter, ['new', 'read', 'replied', 'archived'])) {
            $conditions[] = 'status = ?';
            $params[] = $statusFilter;
        }
        if (!empty($conditions)) {
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }

        $stmt = $db->prepare("SELECT * FROM enquiries {$where} ORDER BY created_at DESC");
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="enquiries_export_' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Company', 'Email', 'Phone', 'Service Required', 'Budget', 'Preferred Contact', 'Message', 'Status', 'GDPR Consent', 'Date']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['id'], $r['full_name'], $r['company_name'], $r['email'], $r['phone'],
                $r['service_required'], $r['budget_range'], $r['preferred_contact'], $r['message'],
                $r['status'], $r['gdpr_consent'] ? 'Yes' : 'No', $r['created_at']
            ]);
        }
        fclose($output);
        exit;
    }

    redirect(admin_url('enquiries.php?' . http_build_query(['search' => $search, 'status' => $statusFilter, 'p' => $page])));
}

$where  = '';
$params = [];
$conditions = [];

if ($search !== '') {
    $conditions[] = '(full_name LIKE ? OR company_name LIKE ? OR email LIKE ? OR service_required LIKE ?)';
    $params = array_merge($params, ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"]);
}
if ($statusFilter !== '' && in_array($statusFilter, ['new', 'read', 'replied', 'archived'])) {
    $conditions[] = 'status = ?';
    $params[] = $statusFilter;
}
if (!empty($conditions)) {
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

$countStmt = $db->prepare("SELECT COUNT(*) FROM enquiries {$where}");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

$pagination = paginate($total, $perPage, $page);

$stmt = $db->prepare("SELECT * FROM enquiries {$where} ORDER BY created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
$stmt->execute($params);
$enquiries = $stmt->fetchAll();

$statusLabels = [
    'new'      => ['label' => 'New',      'class' => 'bg-danger'],
    'read'     => ['label' => 'Read',     'class' => 'bg-info text-dark'],
    'replied'  => ['label' => 'Replied',  'class' => 'bg-success'],
    'archived' => ['label' => 'Archived', 'class' => 'bg-secondary'],
];

$serviceOptions = $db->query('SELECT title FROM services WHERE status = \'active\' ORDER BY sort_order ASC')->fetchAll(PDO::FETCH_COLUMN);
?>
<div class="admin-page-header">
  <h2>Enquiries</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Enquiries</li>
    </ol>
  </nav>
  <form method="post" class="d-inline">
    <?php echo Security::csrf_field(); ?>
    <input type="hidden" name="action" value="export_csv">
    <input type="hidden" name="search" value="<?php echo sanitize($search); ?>">
    <input type="hidden" name="status" value="<?php echo sanitize($statusFilter); ?>">
    <button type="submit" class="btn btn-outline-success"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</button>
  </form>
</div>

<?php foreach (get_flash() as $flash): ?>
  <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
    <?php echo sanitize($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endforeach; ?>

<div class="card">
  <div class="card-body">
    <form method="get" class="row g-3 mb-3">
      <div class="col-md-5">
        <input type="text" name="search" class="form-control" placeholder="Search name, company, email, service..." value="<?php echo sanitize($search); ?>">
      </div>
      <div class="col-md-3">
        <select name="status" class="form-select">
          <option value="">All Statuses</option>
          <option value="new" <?php echo $statusFilter === 'new' ? 'selected' : ''; ?>>New</option>
          <option value="read" <?php echo $statusFilter === 'read' ? 'selected' : ''; ?>>Read</option>
          <option value="replied" <?php echo $statusFilter === 'replied' ? 'selected' : ''; ?>>Replied</option>
          <option value="archived" <?php echo $statusFilter === 'archived' ? 'selected' : ''; ?>>Archived</option>
        </select>
      </div>
      <div class="col-md-auto">
        <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i> Filter</button>
        <a href="?" class="btn btn-outline-secondary">Clear</a>
      </div>
      <div class="col text-end">
        <span class="text-muted"><?php echo $total; ?> enquiries</span>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Company</th>
            <th>Email</th>
            <th>Service</th>
            <th>Date</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($enquiries)): ?>
            <tr><td colspan="7" class="text-center py-4 text-muted">No enquiries found.</td></tr>
          <?php endif; ?>
          <?php foreach ($enquiries as $enq): ?>
            <tr class="enquiry-row" style="cursor:pointer;" data-id="<?php echo (int) $enq['id']; ?>"
                data-name="<?php echo sanitize($enq['full_name']); ?>"
                data-company="<?php echo sanitize($enq['company_name'] ?? ''); ?>"
                data-email="<?php echo sanitize($enq['email']); ?>"
                data-phone="<?php echo sanitize($enq['phone'] ?? ''); ?>"
                data-service="<?php echo sanitize($enq['service_required'] ?? ''); ?>"
                data-budget="<?php echo sanitize($enq['budget_range'] ?? ''); ?>"
                data-contact="<?php echo sanitize($enq['preferred_contact'] ?? ''); ?>"
                data-message="<?php echo htmlspecialchars($enq['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                data-status="<?php echo sanitize($enq['status']); ?>"
                data-gdpr="<?php echo $enq['gdpr_consent'] ? 'Yes' : 'No'; ?>"
                data-date="<?php echo format_date($enq['created_at'], 'd M Y H:i'); ?>"
                data-ip="<?php echo sanitize($enq['ip_address'] ?? ''); ?>"
                data-bs-toggle="modal" data-bs-target="#enquiryModal">
              <td><strong><?php echo sanitize($enq['full_name']); ?></strong></td>
              <td><?php echo sanitize($enq['company_name'] ?? '—'); ?></td>
              <td><a href="mailto:<?php echo sanitize($enq['email']); ?>" onclick="event.stopPropagation();"><?php echo sanitize($enq['email']); ?></a></td>
              <td><?php echo sanitize($enq['service_required'] ?? '—'); ?></td>
              <td><?php echo time_ago($enq['created_at']); ?></td>
              <td>
                <?php $s = $statusLabels[$enq['status']] ?? $statusLabels['new']; ?>
                <span class="badge <?php echo $s['class']; ?>"><?php echo $s['label']; ?></span>
              </td>
              <td class="text-end">
                <div class="dropdown" onclick="event.stopPropagation();">
                  <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <form method="post" class="d-inline">
                        <?php echo Security::csrf_field(); ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo (int) $enq['id']; ?>">
                        <input type="hidden" name="status" value="read">
                        <button type="submit" class="dropdown-item"><i class="bi bi-check text-info"></i> Mark as Read</button>
                      </form>
                    </li>
                    <li>
                      <form method="post" class="d-inline">
                        <?php echo Security::csrf_field(); ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo (int) $enq['id']; ?>">
                        <input type="hidden" name="status" value="replied">
                        <button type="submit" class="dropdown-item"><i class="bi bi-reply text-success"></i> Mark as Replied</button>
                      </form>
                    </li>
                    <li>
                      <form method="post" class="d-inline">
                        <?php echo Security::csrf_field(); ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo (int) $enq['id']; ?>">
                        <input type="hidden" name="status" value="archived">
                        <button type="submit" class="dropdown-item"><i class="bi bi-archive"></i> Archive</button>
                      </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form method="post" class="d-inline" onsubmit="return confirm('Delete this enquiry permanently?');">
                        <?php echo Security::csrf_field(); ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo (int) $enq['id']; ?>">
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i> Delete</button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
      <nav>
        <ul class="pagination justify-content-center">
          <li class="page-item <?php echo !$pagination['has_previous'] ? 'disabled' : ''; ?>">
            <a class="page-link" href="?p=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>">&laquo;</a>
          </li>
          <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
              <a class="page-link" href="?p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>"><?php echo $i; ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?php echo !$pagination['has_next'] ? 'disabled' : ''; ?>">
            <a class="page-link" href="?p=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>">&raquo;</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</div>

<!-- Enquiry Detail Modal -->
<div class="modal fade" id="enquiryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Enquiry Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <table class="table table-sm table-borderless">
              <tr><th class="ps-0">Name:</th><td id="modalName"></td></tr>
              <tr><th class="ps-0">Company:</th><td id="modalCompany"></td></tr>
              <tr><th class="ps-0">Email:</th><td><a href="#" id="modalEmail"></a></td></tr>
              <tr><th class="ps-0">Phone:</th><td id="modalPhone"></td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-sm table-borderless">
              <tr><th class="ps-0">Service:</th><td id="modalService"></td></tr>
              <tr><th class="ps-0">Budget:</th><td id="modalBudget"></td></tr>
              <tr><th class="ps-0">Contact via:</th><td id="modalContact"></td></tr>
              <tr><th class="ps-0">GDPR:</th><td id="modalGdpr"></td></tr>
              <tr><th class="ps-0">Date:</th><td id="modalDate"></td></tr>
              <tr><th class="ps-0">IP:</th><td id="modalIp"></td></tr>
              <tr><th class="ps-0">Status:</th><td id="modalStatus"></td></tr>
            </table>
          </div>
        </div>
        <div class="card bg-light">
          <div class="card-body">
            <h6>Message</h6>
            <p class="mb-0" id="modalMessage" style="white-space:pre-wrap;"></p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <form method="post" class="d-inline" id="modalMarkRead">
          <?php echo Security::csrf_field(); ?>
          <input type="hidden" name="action" value="update_status">
          <input type="hidden" name="id" id="modalEnquiryId">
          <input type="hidden" name="status" value="read">
          <button type="submit" class="btn btn-info"><i class="bi bi-check"></i> Mark Read</button>
        </form>
        <form method="post" class="d-inline" id="modalMarkReplied">
          <?php echo Security::csrf_field(); ?>
          <input type="hidden" name="action" value="update_status">
          <input type="hidden" name="id" id="modalEnquiryId2">
          <input type="hidden" name="status" value="replied">
          <button type="submit" class="btn btn-success"><i class="bi bi-reply"></i> Mark Replied</button>
        </form>
        <form method="post" class="d-inline" id="modalArchive">
          <?php echo Security::csrf_field(); ?>
          <input type="hidden" name="action" value="update_status">
          <input type="hidden" name="id" id="modalEnquiryId3">
          <input type="hidden" name="status" value="archived">
          <button type="submit" class="btn btn-secondary"><i class="bi bi-archive"></i> Archive</button>
        </form>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var enquiryModal = document.getElementById('enquiryModal');
  if (enquiryModal) {
    enquiryModal.addEventListener('show.bs.modal', function (event) {
      var row = event.relatedTarget;
      var id = row.getAttribute('data-id');

      document.getElementById('modalName').textContent = row.getAttribute('data-name');
      document.getElementById('modalCompany').textContent = row.getAttribute('data-company') || '—';
      var emailEl = document.getElementById('modalEmail');
      emailEl.textContent = row.getAttribute('data-email');
      emailEl.href = 'mailto:' + row.getAttribute('data-email');
      document.getElementById('modalPhone').textContent = row.getAttribute('data-phone') || '—';
      document.getElementById('modalService').textContent = row.getAttribute('data-service') || '—';
      document.getElementById('modalBudget').textContent = row.getAttribute('data-budget') || '—';
      document.getElementById('modalContact').textContent = row.getAttribute('data-contact') || '—';
      document.getElementById('modalGdpr').textContent = row.getAttribute('data-gdpr');
      document.getElementById('modalDate').textContent = row.getAttribute('data-date');
      document.getElementById('modalIp').textContent = row.getAttribute('data-ip') || '—';
      document.getElementById('modalStatus').innerHTML = '<span class="badge">' + row.getAttribute('data-status') + '</span>';
      document.getElementById('modalMessage').textContent = row.getAttribute('data-message') || 'No message provided.';

      document.getElementById('modalEnquiryId').value = id;
      document.getElementById('modalEnquiryId2').value = id;
      document.getElementById('modalEnquiryId3').value = id;
    });
  }
});
</script>
