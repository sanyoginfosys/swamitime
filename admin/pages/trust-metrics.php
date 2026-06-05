<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$errors   = [];
$editData = null;
$editId   = (int) ($_GET['edit'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id   = (int) ($_POST['id'] ?? 0);
        $data = [
            'title'       => sanitize($_POST['title'] ?? ''),
            'value'       => sanitize($_POST['value'] ?? ''),
            'icon'        => sanitize($_POST['icon'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'status'      => $_POST['status'] === 'active' ? 'active' : 'inactive',
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
        ];

        if ($data['title'] === '') {
            $errors[] = 'Title is required.';
        }
        if ($data['value'] === '') {
            $errors[] = 'Value is required.';
        }

        if (empty($errors)) {
            if ($id === 0) {
                $stmt = $db->prepare('INSERT INTO trust_metrics (title, value, icon, description, status, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$data['title'], $data['value'], $data['icon'], $data['description'], $data['status'], $data['sort_order']]);
                set_flash('success', 'Trust metric created successfully.');
            } else {
                $stmt = $db->prepare('UPDATE trust_metrics SET title = ?, value = ?, icon = ?, description = ?, status = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([$data['title'], $data['value'], $data['icon'], $data['description'], $data['status'], $data['sort_order'], $id]);
                set_flash('success', 'Trust metric updated successfully.');
            }
            redirect(admin_url('pages/trust-metrics.php'));
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM trust_metrics WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'Trust metric deleted.');
        redirect(admin_url('pages/trust-metrics.php'));
    }
}

$metrics = $db->query('SELECT * FROM trust_metrics ORDER BY sort_order ASC')->fetchAll();

if ($editId > 0) {
    $stmt = $db->prepare('SELECT * FROM trust_metrics WHERE id = ?');
    $stmt->execute([$editId]);
    $editData = $stmt->fetch();
}

$iconList = [
    'fa-solid fa-brain', 'fa-solid fa-clock', 'fa-solid fa-eye', 'fa-solid fa-sliders',
    'fa-solid fa-headset', 'fa-solid fa-shield-halved', 'fa-solid fa-chart-line',
    'fa-solid fa-users', 'fa-solid fa-check-circle', 'fa-solid fa-rocket',
    'fa-solid fa-star', 'fa-solid fa-trophy', 'fa-solid fa-award',
];
?>
<div class="admin-page-header">
  <h2>Trust Metrics</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Trust Metrics</li>
    </ol>
  </nav>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#metricModal" onclick="resetForm()">
    <i class="bi bi-plus-lg"></i> Add New Metric
  </button>
</div>

<?php foreach (get_flash() as $flash): ?>
  <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
    <?php echo sanitize($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endforeach; ?>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?><li><?php echo sanitize($e); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:60px">ID</th>
            <th>Title</th>
            <th>Value</th>
            <th>Icon</th>
            <th>Status</th>
            <th style="width:80px">Order</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($metrics)): ?>
            <tr><td colspan="7" class="text-center py-4 text-muted">No trust metrics found.</td></tr>
          <?php endif; ?>
          <?php foreach ($metrics as $m): ?>
            <tr>
              <td><?php echo (int) $m['id']; ?></td>
              <td><strong><?php echo sanitize($m['title']); ?></strong></td>
              <td><span class="fw-bold fs-5"><?php echo sanitize($m['value']); ?></span></td>
              <td><i class="<?php echo sanitize($m['icon'] ?? ''); ?> fa-lg"></i></td>
              <td>
                <span class="badge <?php echo $m['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                  <?php echo $m['status'] === 'active' ? 'Active' : 'Inactive'; ?>
                </span>
              </td>
              <td><?php echo (int) $m['sort_order']; ?></td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadEdit(<?php echo (int) $m['id']; ?>)" data-bs-toggle="modal" data-bs-target="#metricModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo (int) $m['id']; ?>" data-title="<?php echo sanitize($m['title']); ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Metric Edit Modal -->
<div class="modal fade" id="metricModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" novalidate id="metricForm">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="mtId" value="0">
        <div class="modal-header">
          <h5 class="modal-title" id="metricModalLabel">Add / Edit Trust Metric</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="mtTitle" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" id="mtTitle" name="title" class="form-control" required maxlength="255">
          </div>
          <div class="mb-3">
            <label for="mtValue" class="form-label">Value <span class="text-danger">*</span></label>
            <input type="text" id="mtValue" name="value" class="form-control" required maxlength="50" placeholder="e.g. 85% or 10+">
            <div class="form-text">Display value shown on the front end.</div>
          </div>
          <div class="mb-3">
            <label for="mtIcon" class="form-label">Icon</label>
            <div class="input-group">
              <span class="input-group-text"><i id="mtIconPreview" class=""></i></span>
              <input type="text" id="mtIcon" name="icon" class="form-control" oninput="document.getElementById('mtIconPreview').className = this.value" placeholder="fa-solid fa-brain">
            </div>
            <div class="icon-grid mt-2" style="max-height:120px;overflow-y:auto;">
              <?php foreach ($iconList as $ic): ?>
                <button type="button" class="btn btn-sm btn-outline-secondary m-1 icon-pick" data-icon="<?php echo $ic; ?>">
                  <i class="<?php echo $ic; ?>"></i>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="mb-3">
            <label for="mtDescription" class="form-label">Description</label>
            <textarea id="mtDescription" name="description" class="form-control" rows="3"></textarea>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="mtStatus" class="form-label">Status</label>
              <select id="mtStatus" name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="mtSortOrder" class="form-label">Sort Order</label>
              <input type="number" id="mtSortOrder" name="sort_order" class="form-control" value="0">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Delete trust metric <strong id="deleteTitle"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
var metricData = <?php echo json_encode($metrics); ?>;

function resetForm() {
  document.getElementById('mtId').value = '0';
  document.getElementById('mtTitle').value = '';
  document.getElementById('mtValue').value = '';
  document.getElementById('mtIcon').value = '';
  document.getElementById('mtIconPreview').className = '';
  document.getElementById('mtDescription').value = '';
  document.getElementById('mtStatus').value = 'active';
  document.getElementById('mtSortOrder').value = '0';
  document.getElementById('metricModalLabel').textContent = 'Add New Trust Metric';
}

function loadEdit(id) {
  var item = metricData.find(function(i) { return parseInt(i.id) === id; });
  if (!item) return;
  document.getElementById('mtId').value = item.id;
  document.getElementById('mtTitle').value = item.title;
  document.getElementById('mtValue').value = item.value;
  document.getElementById('mtIcon').value = item.icon || '';
  document.getElementById('mtIconPreview').className = item.icon || '';
  document.getElementById('mtDescription').value = item.description || '';
  document.getElementById('mtStatus').value = item.status;
  document.getElementById('mtSortOrder').value = item.sort_order;
  document.getElementById('metricModalLabel').textContent = 'Edit Trust Metric';
}

document.querySelectorAll('.icon-pick').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var icon = this.getAttribute('data-icon');
    document.getElementById('mtIcon').value = icon;
    document.getElementById('mtIconPreview').className = icon;
  });
});

document.addEventListener('DOMContentLoaded', function () {
  var deleteModal = document.getElementById('deleteModal');
  if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('deleteId').value = button.getAttribute('data-id');
      document.getElementById('deleteTitle').textContent = button.getAttribute('data-title');
    });
  }

  <?php if ($editId > 0 && $editData): ?>
    var modal = new bootstrap.Modal(document.getElementById('metricModal'));
    modal.show();
    loadEdit(<?php echo $editId; ?>);
  <?php endif; ?>
});
</script>
