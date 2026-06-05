<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$page   = max(1, (int) ($_GET['p'] ?? 1));
$perPage = 20;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_status') {
        $id     = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] === 'active' ? 'active' : 'inactive';
        $stmt = $db->prepare('UPDATE services SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        set_flash('success', 'Service status updated.');
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM services WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'Service deleted.');
    }

    redirect(admin_url('services/'));
}

$countStmt = $db->query('SELECT COUNT(*) FROM services');
$total = (int) $countStmt->fetchColumn();

$pagination = paginate($total, $perPage, $page);

$stmt = $db->prepare("SELECT * FROM services ORDER BY sort_order ASC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
$stmt->execute();
$services = $stmt->fetchAll();
?>
<div class="admin-page-header">
  <h2>All Services</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Services</li>
    </ol>
  </nav>
  <a href="<?php echo admin_url('service-edit.php'); ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add New Service</a>
</div>

<?php foreach (get_flash() as $flash): ?>
  <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
    <?php echo sanitize($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endforeach; ?>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:60px">ID</th>
            <th>Title</th>
            <th style="width:60px">Icon</th>
            <th>Status</th>
            <th style="width:80px">Order</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($services)): ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">No services found.</td></tr>
          <?php endif; ?>
          <?php foreach ($services as $s): ?>
            <tr>
              <td><?php echo (int) $s['id']; ?></td>
              <td><strong><?php echo sanitize($s['title']); ?></strong></td>
              <td><i class="<?php echo sanitize($s['icon'] ?? ''); ?> fa-lg"></i></td>
              <td>
                <button type="button" class="btn btn-sm status-toggle badge-btn <?php echo $s['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>"
                        data-id="<?php echo (int) $s['id']; ?>"
                        data-status="<?php echo sanitize($s['status']); ?>">
                  <?php echo $s['status'] === 'active' ? 'Active' : 'Inactive'; ?>
                </button>
              </td>
              <td><?php echo (int) $s['sort_order']; ?></td>
              <td class="text-end">
                <a href="<?php echo admin_url('service-edit.php?id=' . (int) $s['id']); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <a href="<?php echo site_url('services/' . $s['slug']); ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-box-arrow-up-right"></i></a>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo (int) $s['id']; ?>" data-title="<?php echo sanitize($s['title']); ?>"><i class="bi bi-trash"></i></button>
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
            <a class="page-link" href="?p=<?php echo $pagination['current_page'] - 1; ?>">&laquo;</a>
          </li>
          <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
              <a class="page-link" href="?p=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?php echo !$pagination['has_next'] ? 'disabled' : ''; ?>">
            <a class="page-link" href="?p=<?php echo $pagination['current_page'] + 1; ?>">&raquo;</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong id="deleteTitle"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
      var newStatus = current === 'active' ? 'inactive' : 'active';
      document.getElementById('toggleId').value = id;
      document.getElementById('toggleStatus').value = newStatus;
      document.getElementById('toggleForm').submit();
    });
  });
});
</script>
