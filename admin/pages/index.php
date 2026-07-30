<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$search = trim($_GET['search'] ?? '');
$page   = max(1, (int) ($_GET['p'] ?? 1));
$perPage = 20;

// --- CSRF-protected actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_status') {
        $id     = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] === 'published' ? 'published' : 'draft';
        $stmt = $db->prepare('UPDATE pages SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        set_flash('success', 'Page status updated.');
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM pages WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'Page deleted.');
    }

    redirect(admin_url('pages.php'));
}

// --- Query ---
$where  = '';
$params = [];
if ($search !== '') {
    $where   = 'WHERE title LIKE ? OR slug LIKE ?';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$countStmt = $db->prepare("SELECT COUNT(*) FROM pages {$where}");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

$pagination = paginate($total, $perPage, $page);

$stmt = $db->prepare("SELECT * FROM pages {$where} ORDER BY sort_order ASC, updated_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
$stmt->execute($params);
$pages = $stmt->fetchAll();

// Preview data for modal
$templates = ['default', 'home', 'full-width', 'sidebar', 'landing'];
$allPages = $db->query('SELECT id, title FROM pages ORDER BY sort_order ASC')->fetchAll();
?>
<div class="admin-page-header">
  <h2>All Pages</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Pages</li>
    </ol>
  </nav>
  <a href="<?php echo admin_url('page-edit.php'); ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add New Page</a>
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
      <div class="col-md-6">
        <div class="input-group">
          <input type="text" name="search" class="form-control" placeholder="Search by title or slug..." value="<?php echo sanitize($search); ?>">
          <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
        </div>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Slug</th>
            <th>Status</th>
            <th>Updated</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($pages)): ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">No pages found.</td></tr>
          <?php endif; ?>
          <?php foreach ($pages as $p): ?>
            <tr>
              <td><?php echo (int) $p['id']; ?></td>
              <td>
                <strong><?php echo sanitize($p['title']); ?></strong>
                <?php if ($p['parent_id']): ?>
                  <br><small class="text-muted">Child of: <?php
                    foreach ($allPages as $ap) {
                      if ((int) $ap['id'] === (int) $p['parent_id']) {
                        echo sanitize($ap['title']);
                        break;
                      }
                    }
                  ?></small>
                <?php endif; ?>
              </td>
              <td><code><?php echo sanitize($p['slug']); ?></code></td>
              <td>
                <button type="button" class="btn btn-sm status-toggle badge-btn <?php echo $p['status'] === 'published' ? 'bg-success' : 'bg-warning text-dark'; ?>"
                        data-id="<?php echo (int) $p['id']; ?>"
                        data-status="<?php echo sanitize($p['status']); ?>">
                  <?php echo $p['status'] === 'published' ? 'Published' : 'Draft'; ?>
                </button>
              </td>
              <td><?php echo format_date($p['updated_at'], 'd M Y H:i'); ?></td>
              <td class="text-end">
                <a href="<?php echo admin_url('page-edit.php?id=' . (int) $p['id']); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <a href="<?php echo site_url($p['slug']); ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-box-arrow-up-right"></i></a>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo (int) $p['id']; ?>" data-title="<?php echo sanitize($p['title']); ?>"><i class="bi bi-trash"></i></button>
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
            <a class="page-link" href="?p=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>">&laquo;</a>
          </li>
          <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
              <a class="page-link" href="?p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?php echo !$pagination['has_next'] ? 'disabled' : ''; ?>">
            <a class="page-link" href="?p=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>">&raquo;</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="deleteForm">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong id="deleteTitle"></strong>? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- AJAX Status Toggle -->
<form method="post" id="toggleForm" style="display:none;">
  <?php echo Security::csrf_field(); ?>
  <input type="hidden" name="action" value="toggle_status">
  <input type="hidden" name="id" id="toggleId">
  <input type="hidden" name="status" id="toggleStatus">
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var deleteModal = document.getElementById('deleteModal');
  if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('deleteId').value = button.getAttribute('data-id');
      document.getElementById('deleteTitle').textContent = button.getAttribute('data-title');
    });
  }

  document.querySelectorAll('.status-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = this.getAttribute('data-id');
      var current = this.getAttribute('data-status');
      var newStatus = current === 'published' ? 'draft' : 'published';
      document.getElementById('toggleId').value = id;
      document.getElementById('toggleStatus').value = newStatus;
      document.getElementById('toggleForm').submit();
    });
  });
});
</script>
